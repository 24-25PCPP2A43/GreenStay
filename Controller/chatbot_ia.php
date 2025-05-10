<?php
// Affichage des erreurs PHP pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inclure la base de données et la configuration
include('../../config/database.php');

// Définir le dossier de destination des fichiers téléchargés
$upload_dir = '../../uploads/'; // Chemin sécurisé hors de la racine web

// Traitement du formulaire d'ajout de réclamation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sujet = trim($_POST['sujet']);
    $message = trim($_POST['message']);
    $client_id = trim($_POST['client_id']);
    $reservation_id = trim($_POST['reservation_id']);

    // Validation côté serveur
    if (empty($sujet) || empty($message) || empty($client_id) || empty($reservation_id)) {
        $error = "Tous les champs doivent être remplis.";
    } elseif (!is_numeric($client_id) || !is_numeric($reservation_id)) {
        $error = "L'ID client et l'ID réservation doivent être des nombres valides.";
    } else {
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        // Traitement des fichiers téléchargés
        $fichiers_paths = [];

        if (isset($_FILES['fichiers']) && is_array($_FILES['fichiers']['name'])) {
            $nb_fichiers = count($_FILES['fichiers']['name']);

            for ($i = 0; $i < $nb_fichiers; $i++) {
                if ($_FILES['fichiers']['error'][$i] === UPLOAD_ERR_OK) {
                    $fichier_tmp_name = $_FILES['fichiers']['tmp_name'][$i];
                    $fichier_name = $_FILES['fichiers']['name'][$i];
                    $fichier_size = $_FILES['fichiers']['size'][$i];
                    $fichier_type = $_FILES['fichiers']['type'][$i];

                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm'];
                    if (!in_array($fichier_type, $allowed_types)) {
                        $error .= "Type de fichier non autorisé : " . htmlspecialchars($fichier_name) . "<br>";
                        continue;
                    }

                    $max_size = 5 * 1024 * 1024; // 5MB
                    if ($fichier_size > $max_size) {
                        $error .= "Fichier trop volumineux : " . htmlspecialchars($fichier_name) . "<br>";
                        continue;
                    }

                    $fichier_name_safe = basename($fichier_name);
                    $fichier_name_safe = preg_replace("/[^a-zA-Z0-9._-]/", "", $fichier_name_safe);
                    $fichier_path = $upload_dir . uniqid() . '_' . $fichier_name_safe;

                    if (move_uploaded_file($fichier_tmp_name, $fichier_path)) {
                        $fichiers_paths[] = $fichier_path;
                    } else {
                        $error .= "Erreur lors du téléchargement du fichier : " . htmlspecialchars($fichier_name) . "<br>";
                    }
                } elseif ($_FILES['fichiers']['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                    $error .= "Erreur lors du téléchargement du fichier : " . htmlspecialchars($fichier_name) . " (Code d'erreur : " . $_FILES['fichiers']['error'][$i] . ")<br>";
                }
            }
        }

        if (!empty($fichiers_paths)) {
            $message .= "\n\nPièces jointes :\n";
            foreach ($fichiers_paths as $fichier_path) {
                $message .= htmlspecialchars($fichier_path) . "\n";
            }
        }

        $sql = "INSERT INTO reclamations (sujet, message, statut, client_id, reservation_id) 
                VALUES (:sujet, :message, 'Nouveau', :client_id, :reservation_id)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':sujet', $sujet);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':client_id', $client_id);
        $stmt->bindParam(':reservation_id', $reservation_id);

        if ($stmt->execute()) {
            header("Location: listReclamations.php?success=1");
            exit();
        } else {
            $error = "Une erreur est survenue. Veuillez réessayer.";
        }
    }
}

// Récupération du critère de tri
$tri = isset($_GET['tri']) ? $_GET['tri'] : 'sujet';

// Requête pour récupérer les réclamations
$sql = "SELECT r.id, r.sujet, r.message AS reclamation_message, r.statut, c.nom, c.prenom, re.date_reservation, 
                rep.message AS reponse_message, rep.date_reponse
        FROM reclamations r
        JOIN clients c ON r.client_id = c.id
        JOIN reservations re ON r.reservation_id = re.id
        LEFT JOIN reponse rep ON r.id = rep.id_reclamation
        ORDER BY $tri";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de la requête : " . $e->getMessage());
}
?>

<?php include('includes/header.php'); ?>
<?php include('includes/navbar.php'); ?>

<!-- Section avec l'image de fond -->
<div class="container-fluid mt-5 mb-5 reclamation-section" style="background-image: url('../../assets/images/best-03.jpg'); background-size: cover; background-position: center; padding: 0;">
    <div class="container p-4 shadow rounded" style="background-color: rgba(255, 255, 255, 0.9);">
        <h2 class="text-center custom-header">Ajouter une Réclamation</h2>

        <!-- Affichage des erreurs -->
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
            <div class="alert alert-success">Réclamation ajoutée avec succès !</div>
        <?php endif; ?>

        <!-- Formulaire d'ajout de réclamation -->
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group mb-3">
                <label for="sujet" style="color: #333;">Sujet</label>
                <input type="text" class="form-control" id="sujet" name="sujet" placeholder="Entrez le sujet de la réclamation" required>
            </div>
            <div class="form-group mb-3">
                <label for="message" style="color: #333;">Message</label>
                <textarea class="form-control" id="message" name="message" rows="4" placeholder="Décrivez votre réclamation" required></textarea>
            </div>

            <div class="form-group mb-3">
                <label for="langue" style="color: #333;">Langue de transcription</label>
                <select class="form-control" id="langue" name="langue">
                    <option value="fr-FR">Français</option>
                    <option value="en-US">Anglais</option>
                    <option value="es-ES">Espagnol</option>
                </select>
                <small class="text-muted">La qualité de la transcription peut varier selon le navigateur.</small>
            </div>
            <div class="form-group mb-3">
                <button type="button" class="btn btn-secondary" id="startRecord">Démarrer l'enregistrement</button>
                <button type="button" class="btn btn-secondary" id="stopRecord" disabled>Arrêter l'enregistrement</button>
            </div>

            <div class="form-group mb-3">
                <label for="fichiers" style="color: #333;">Pièces Jointes (Images ou Vidéos)</label>
                <input type="file" class="form-control" id="fichiers" name="fichiers[]" multiple accept="image/*, video/*">
                <small class="text-muted">Vous pouvez joindre plusieurs fichiers (images ou vidéos).</small>
            </div>

            <div class="form-group mb-3">
                <label for="client_id" style="color: #333;">ID Client</label>
                <input type="number" class="form-control" id="client_id" name="client_id" placeholder="Entrez l'ID du client" required>
            </div>
            <div class="form-group mb-4">
                <label for="reservation_id" style="color: #333;">ID Réservation</label>
                <input type="number" class="form-control" id="reservation_id" name="reservation_id" placeholder="Entrez l'ID de la réservation" required>
            </div>
            <button type="submit" class="btn" style="background-color: #ADD8E6; color: white; border: none; padding: 15px 30px; font-size: 16px; width: 100%;">Soumettre la Réclamation</button>
        </form>

        <form method="GET" action="">
            <div class="form-group">
                <label for="tri">Trier par :</label>
                <select name="tri" id="tri" class="form-control" onchange="this.form.submit()">
                    <option value="sujet" <?= (isset($_GET['tri']) && $_GET['tri'] == 'sujet') ? 'selected' : '' ?>>Sujet</option>
                    <option value="date_reservation" <?= (isset($_GET['tri']) && $_GET['tri'] == 'date_reservation') ? 'selected' : '' ?>>Date de réservation</option>
                </select>
            </div>
        </form>

        <button type="button" class="btn" style="background-color: #ADD8E6; color: white; border: none; padding: 15px 30px; font-size: 16px; width: 100%; margin-top: 10px;" onclick="toggleReclamations()">Voir Liste des Réclamations</button>

        <div id="reclamationsList" class="mt-5" style="display: none;">
            <h2 class="text-center custom-header">Liste des Réclamations</h2>

            <table class="table table-hover table-bordered text-center align-middle">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Sujet</th>
                        <th>Message</th>
                        <th>Statut</th>
                        <th>Client</th>
                        <th>Date Réservation</th>
                        <th>Réponse</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($result) > 0): ?>
                    <?php foreach ($result as $row): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['sujet']) ?></td>
                            <td><?= htmlspecialchars($row['reclamation_message']) ?></td>
                            <td>
                                <?php
                                    switch ($row['statut']) {
                                        case 0:
                                            echo "<span class='badge badge-warning'>Nouveau</span>";
                                            break;
                                        case 1:
                                            echo "<span class='badge badge-info'>En cours de traitement</span>";
                                            break;
                                        case 2:
                                            echo "<span class='badge badge-success'>Traitée</span>";
                                            break;
                                        default:
                                            echo "Statut inconnu";
                                            break;
                                    }
                                ?>
                            </td>
                            <td><?= $row['nom'] . ' ' . $row['prenom'] ?></td>
                            <td><?= date("d/m/Y", strtotime($row['date_reservation'])) ?></td>
                            <td>
                                <?php if ($row['reponse_message']): ?>
                                    <?= htmlspecialchars($row['reponse_message']) ?>
                                    <br><small>Le <?= date("d/m/Y H:i", strtotime($row['date_reponse'])) ?></small>
                                <?php else: ?>
                                    <span class="text-muted">Pas encore répondu</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="modifierReclamation.php?id=<?= $row['id'] ?>" class="btn btn-outline-dark btn-sm">Modifier</a>
                                <a href="supprimerReclamation.php?id=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">Aucune réclamation trouvée.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="text-center mt-2">
            <a href="../../index.php" class="btn" style="background-color: #ADD8E6; color: white; padding: 10px 20px;">Retour à l'Accueil</a>
        </div>
    </div>
</div>

<script>
function toggleReclamations() {
    var list = document.getElementById('reclamationsList');
    list.style.display = (list.style.display === "none") ? "block" : "none";
}

// Script pour le chatbot
document.addEventListener('DOMContentLoaded', function() {
    const chatToggle = document.createElement('button');
    chatToggle.innerText = 'Chat';
    chatToggle.style.position = 'fixed';
    chatToggle.style.bottom = '20px';
    chatToggle.style.right = '20px';
    chatToggle.style.backgroundColor = '#007bff';
    chatToggle.style.color = 'white';
    chatToggle.style.border = 'none';
    chatToggle.style.borderRadius = '5px';
    chatToggle.style.padding = '10px';
    document.body.appendChild(chatToggle);

    const chatbot = document.createElement('div');
    chatbot.style.position = 'fixed';
    chatbot.style.bottom = '70px';
    chatbot.style.right = '20px';
    chatbot.style.width = '300px';
    chatbot.style.background = 'white';
    chatbot.style.border = '1px solid #ccc';
    chatbot.style.borderRadius = '5px';
    chatbot.style.display = 'none';
    chatbot.innerHTML = `<div id="chatbox" style="height: 300px; overflow-y: auto; padding: 10px;"></div>
                        <input type="text" id="userInput" placeholder="Posez votre question..." style="width: calc(100% - 30px);">
                        <button id="sendBtn">Envoyer</button>`;
    document.body.appendChild(chatbot);

    chatToggle.addEventListener('click', () => {
        chatbot.style.display = (chatbot.style.display === 'none') ? 'block' : 'none';
    });

    document.getElementById('sendBtn').addEventListener('click', () => {
        const userInput = document.getElementById('userInput').value;
        if (userInput.trim() === '') return;

        document.getElementById('chatbox').innerHTML += `<div style="color: black;"><strong>Vous:</strong> ${userInput}</div>`;
        document.getElementById('userInput').value = '';

        // Envoyer la requête à l'IA
        fetch('chatbot_ia.php', {  // Assurez-vous que le chemin est correct
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `message=${encodeURIComponent(userInput)}`,
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById('chatbox').innerHTML += `<div style="color: black;"><strong>Assistant:</strong> ${data}</div>`;
            document.getElementById('chatbox').scrollTop = document.getElementById('chatbox').scrollHeight;
        })
        .catch(error => {
            console.error('Erreur:', error);
            document.getElementById('chatbox').innerHTML += `<div style="color: black;"><strong>Assistant:</strong> Désolé, une erreur est survenue.</div>`;
        });
    });
});

// Enregistrement vocal
const startRecordButton = document.getElementById('startRecord');
const stopRecordButton = document.getElementById('stopRecord');
const langueSelect = document.getElementById('langue');
const messageTextarea = document.getElementById('message');

let recognition;

startRecordButton.addEventListener('click', () => {
    startRecordButton.disabled = true;
    stopRecordButton.disabled = false;
    messageTextarea.value = '';

    if ('webkitSpeechRecognition' in window) {
        recognition = new webkitSpeechRecognition();
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = langueSelect.value;

        recognition.onstart = () => {
            console.log('Démarrage de la reconnaissance vocale...');
        };

        recognition.onresult = (event) => {
            const transcript = event.results[0][0].transcript;
            messageTextarea.value = transcript;
            console.log('Transcription:', transcript);
            stopRecordButton.disabled = true;
            startRecordButton.disabled = false;
        };

        recognition.onerror = (event) => {
            console.error('Erreur de reconnaissance vocale:', event.error);
            messageTextarea.value = 'Erreur de reconnaissance vocale.';
            stopRecordButton.disabled = true;
            startRecordButton.disabled = false;
        };

        recognition.onend = () => {
            console.log('Fin de la reconnaissance vocale.');
            stopRecordButton.disabled = true;
            startRecordButton.disabled = false;
        };

        recognition.start();
    } else {
        messageTextarea.value = 'L\'API Web Speech n\'est pas prise en charge par votre navigateur.';
        startRecordButton.disabled = false;
        stopRecordButton.disabled = true;
    }
});

stopRecordButton.addEventListener('click', () => {
    if (recognition) {
        recognition.stop();
    }
    stopRecordButton.disabled = true;
    startRecordButton.disabled = false;
});
</script>

<?php include('includes/footer.php'); ?>