<?php
// Affichage des erreurs PHP pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inclure la base de données et la configuration
include('../../config/database.php');

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
        // Préparer la requête d'insertion
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

// Récupération du critère de tri (sujet ou date_reservation)
$tri = isset($_GET['tri']) ? $_GET['tri'] : 'sujet'; // Valeur par défaut : 'sujet'

// Requête pour récupérer les réclamations avec le tri sélectionné
$sql = "SELECT r.id, r.sujet, r.message AS reclamation_message, r.statut, c.nom, c.prenom, re.date_reservation, 
                rep.message AS reponse_message, rep.date_reponse
        FROM reclamations r
        JOIN clients c ON r.client_id = c.id
        JOIN reservations re ON r.reservation_id = re.id
        LEFT JOIN reponse rep ON r.id = rep.id_reclamation
        ORDER BY $tri"; // Tri dynamique selon le critère sélectionné

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute();  // Utilisation de execute() pour un meilleur contrôle des erreurs
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupérer tous les résultats
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
        <form action="" method="POST">
            <div class="form-group mb-3">
                <label for="sujet" style="color: #333;">Sujet</label>
                <input type="text" class="form-control" id="sujet" name="sujet" placeholder="Entrez le sujet de la réclamation" required>
            </div>
            <div class="form-group mb-3">
                <label for="message" style="color: #333;">Message</label>
                <textarea class="form-control" id="message" name="message" rows="4" placeholder="Décrivez votre réclamation" required></textarea>
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

        <!-- Formulaire de sélection pour trier les réclamations -->
        <form method="GET" action="">
            <div class="form-group">
                <label for="tri">Trier par :</label>
                <select name="tri" id="tri" class="form-control" onchange="this.form.submit()">
                    <option value="sujet" <?= (isset($_GET['tri']) && $_GET['tri'] == 'sujet') ? 'selected' : '' ?>>Sujet</option>
                    <option value="date_reservation" <?= (isset($_GET['tri']) && $_GET['tri'] == 'date_reservation') ? 'selected' : '' ?>>Date de réservation</option>
                </select>
            </div>
        </form>

        <!-- Bouton pour afficher la liste des réclamations -->
        <button type="button" class="btn" style="background-color: #ADD8E6; color: white; border: none; padding: 15px 30px; font-size: 16px; width: 100%; margin-top: 10px;" onclick="toggleReclamations()">Voir Liste des Réclamations</button>

        <!-- Section pour afficher la liste des réclamations -->
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
                                    // Affichage des statuts avec les nouvelles options
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
                                    <!-- Affiche la réponse de l'admin -->
                                    <?= htmlspecialchars($row['reponse_message']) ?> 
                                    <br><small>Le <?= date("d/m/Y H:i", strtotime($row['date_reponse'])) ?></small>
                                <?php else: ?>
                                    <!-- Si aucune réponse, affiche "Pas encore répondu" -->
                                    <span class="text-muted">Pas encore répondu</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <!-- Modifier la réclamation -->
                                <a href="modifierReclamation.php?id=<?= $row['id'] ?>" class="btn btn-outline-dark btn-sm">Modifier</a>

                                <!-- Supprimer la réclamation -->
                                <a href="supprimerReclamation.php?id=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8" class="text-center text-muted">Aucune réclamation trouvée.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Bouton retour à l'accueil -->
        <div class="text-center mt-2">
            <a href="../../index.php" class="btn" style="background-color: #ADD8E6; color: white; padding: 10px 20px;">Retour à l'Accueil</a>
        </div>
    </div>
</div>

<!-- Script pour afficher/masquer la liste des réclamations -->
<script>
function toggleReclamations() {
    var list = document.getElementById('reclamationsList');
    if (list.style.display === "none") {
        list.style.display = "block";
    } else {
        list.style.display = "none";
    }
}
</script>

<?php include('includes/footer.php'); ?>
