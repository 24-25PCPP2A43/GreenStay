<?php
require_once __DIR__ . '/../../Controller/MessageController.php';

session_start();

$controller = new MessageController();
$expediteur = 'admin';

// Envoi de message par l'admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contenu'])) {
    $controller->envoyerMessage($expediteur, $_POST['contenu']);
}

// Récupération des messages
$messages = $controller->afficherMessages();




// Exemple : 'admin' ou 'client' selon ton système d'authentification
$expediteur = $_SESSION['role'] ?? 'admin';

$controller = new MessageController();

// Traitement de l'envoi de message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contenu'])) {
    $controller->envoyerMessage($expediteur, $_POST['contenu']);
}

$messages = $controller->afficherMessages();
?>

<!-- BOUTON FLOTTANT EN BAS À DROITE -->
<button onclick="toggleDiscussion()" id="chatToggleBtn">
    💬
</button>

<!-- BOÎTE DE DISCUSSION CACHÉE PAR DÉFAUT -->
<div id="discussionBox">
    <h3 style="margin-top: 0;">💬 Discussion</h3>
    <form method="post" style="display: flex; flex-direction: column; gap: 8px;">
        <textarea name="contenu" rows="3" placeholder="Écrire un message..." required style="padding: 8px; border-radius: 5px; border: 1px solid #ccc; font-size: 13px;"></textarea>
        <button type="submit" style="padding: 8px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">
            Envoyer
        </button>
    </form>

    <div class="messages" style="margin-top: 12px; max-height: 200px; overflow-y: auto;">
        <?php foreach ($messages as $msg): ?>
            <div style="margin-bottom: 10px; padding: 10px; border-radius: 6px; background-color: <?= $msg['expediteur'] === 'admin' ? '#e3f2fd' : '#d4edda' ?>;">
                <strong style="color: #007bff;"><?= htmlspecialchars($msg['expediteur']) ?>:</strong>
                <p style="font-size: 13px; margin: 5px 0;"><?= htmlspecialchars($msg['contenu']) ?></p>
                <div style="font-size: 0.75em; color: gray;"><?= $msg['date_envoi'] ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- STYLES -->
<style>
#chatToggleBtn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 45px;
    height: 45px;
    border: none;
    border-radius: 50%;
    background-color: #007bff;
    color: white;
    font-size: 20px;
    cursor: pointer;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    z-index: 999;
}

#discussionBox {
    display: none;
    position: fixed;
    bottom: 80px;
    right: 20px;
    width: 320px;
    background-color: white;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0px 5px 15px rgba(0,0,0,0.2);
    z-index: 998;
}
</style>

<!-- SCRIPT -->
<script>
function toggleDiscussion() {
    const box = document.getElementById('discussionBox');
    box.style.display = box.style.display === 'none' ? 'block' : 'none';
}
</script>

<!DOCTYPE html>
<html lang="fr">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Back Office - GREEN STAY</title>

    <style>
    body {
        background-image: url("http://localhost/eco_tech/assets/images/banner-01.jpg");
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
    }

    header {
        background-color: rgba(0, 0, 0, 0.7); /* Noir transparent */
        color: white;
        padding: 20px;
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        position: fixed;
        top: 0;
        width: 100%;
        z-index: 1000;
        box-shadow: 0 2px 5px rgba(0,0,0,0.5);
    }

    .main-content {
        margin-top: 100px; /* espace sous le header fixe */
        padding: 20px;
    }

    .container {
        width: 90%;
        margin: 0 auto;
        padding: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background-color: rgba(255, 255, 255, 0.5); /* Fond transparent */
    }

    table, th, td {
        border: 1px solid #ddd;
    }

    th, td {
        padding: 12px;
        text-align: left;
        font-size: 16px;
    }

    th {
        background-color:#5bc0de; /* Vert transparent */
        color: white;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    form {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        margin-top: 20px;
    }

    form input, form textarea, form select {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 5px;
        border: 1px solid #ddd;
        font-size: 16px;
    }

    form button {
        background-color: rgb(40, 135, 167);
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        border: none;
        font-size: 16px;
        cursor: pointer;
    }

    form button:hover {
        background-color: #5bc0de;
    }

    footer {
        background-color: #5bc0de;
        color: white;
        text-align: center;
        padding: 10px;
        margin-top: 30px;
    }
</style>

</head>

<body>
<header>
  Back Office - GREEN STAY

  <div class="navbar">
    <!-- page=1 pour le Dashboard -->
    <a href="/Projet/View/BackOffice/userdash.php?page=1">retour</a>
  </div>
</header>

<div class="main-content">
    <!-- Liste des Services -->
    <h2>🌿 Liste des Services</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Description</th>
            <th>Catégorie</th>
            <th>Prix</th>
            <th>Disponible</th>
            <th>Actions</th>
        </tr>
        <?php if (!empty($services)): ?>
            <?php foreach ($services as $service): ?>
                <tr>
                    <td><?= $service['id_service'] ?></td>
                    <td><?= $service['nom_service'] ?></td>
                    <td><?= $service['description'] ?></td>
                    <td><?= $service['categorie'] ?></td>
                    <td><?= $service['prix_estime'] ?> €</td>
                    <td><?= $service['disponible'] ? 'Oui' : 'Non' ?></td>
                    <td>
                        <a href="?action=edit_service&id=<?= $service['id_service'] ?>">Modifier</a> |
                        <a href="?action=delete_service&id=<?= $service['id_service'] ?>">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7">Aucun service disponible.</td></tr>
        <?php endif; ?>
    </table>
    <a href="index.php?action=afficher_journal" class="btn btn-info">Voir l'historique</a>

   <?php if (isset($serviceToEdit)): ?>
    <!-- FORMULAIRE DE MODIFICATION -->
    <h2>✏️ Modifier le Service</h2>
    <form method="POST" action="index.php?action=update_service&id=<?= $serviceToEdit['id_service'] ?>">
        <input type="text" name="nom_service" value="<?= htmlspecialchars($serviceToEdit['nom_service']) ?>" required>
        <textarea name="description" required><?= htmlspecialchars($serviceToEdit['description']) ?></textarea>
        <input type="text" name="categorie" value="<?= htmlspecialchars($serviceToEdit['categorie']) ?>" required>
        <input type="number" name="prix_estime" step="0.01" value="<?= $serviceToEdit['prix_estime'] ?>" required>
        <select name="disponible" required>
            <option value="1" <?= $serviceToEdit['disponible'] ? 'selected' : '' ?>>Disponible</option>
            <option value="0" <?= !$serviceToEdit['disponible'] ? 'selected' : '' ?>>Indisponible</option>
        </select>
        <button type="submit">Mettre à jour</button>
    </form>
<?php else: ?>
    <!-- FORMULAIRE D'AJOUT -->
    <h2>➕ Ajouter un Nouveau Service</h2>
    <form method="POST" action="index.php?action=store_service">
        <input type="text" name="nom_service" placeholder="Nom du service">
        <textarea name="description" placeholder="Description"></textarea>
        <input type="text" name="categorie" placeholder="Catégorie">
        <input type="number" name="prix_estime" placeholder="Prix estimé" step="0.01">
        <select name="disponible">
            <option value="1">Disponible</option>
            <option value="0">Indisponible</option>
        </select>
        <button type="submit">Ajouter le Service</button>
    </form>
<?php endif; ?>
<style>
    .activity-log {
        background-color: rgba(255, 255, 255, 0.8);
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
        margin-top: 40px;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
    }

    .activity-log h2 {
        font-size: 28px;
        color: #333;
        margin-bottom: 20px;
        text-align: center;
    }

    .activity-item {
        background-color: #f9f9f9;
        margin-bottom: 10px;
        padding: 15px 20px;
        border-left: 6px solid #28a745;
        border-radius: 6px;
        font-size: 16px;
        color: #444;
        transition: transform 0.2s, background-color 0.2s;
    }

    .activity-item:hover {
        transform: scale(1.02);
        background-color: #eef8f0;
    }

    .empty-log {
        text-align: center;
        color: #888;
        font-style: italic;
    }
</style>

<div class="activity-log">
    <h2>📜 Historique des activités</h2>
    <?php if (empty($logs)) : ?>
        <div class="empty-log">Aucune activité enregistrée.</div>
    <?php else: ?>
        <?php foreach ($logs as $log) : ?>
            <div class="activity-item"><?= htmlspecialchars($log) ?></div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>




    <!-- Liste des Demandes -->
    <h2>📋 Liste des Demandes de Service</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Service ID</th>
            <th>Client ID</th>
            <th>Description</th>
            <th>Date</th>
            <th>État</th>
        </tr>
        <?php if (!empty($demandes)): ?>
            <?php foreach ($demandes as $demande): ?>
                <tr>
                    <td><?= $demande['id'] ?></td>
                    <td><?= $demande['service_id'] ?></td>
                    <td><?= $demande['client_id'] ?></td>
                    <td><?= $demande['description'] ?></td>
                    <td><?= $demande['date_demande'] ?></td>
                    <td><?= $demande['etat'] ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6">Aucune demande enregistrée.</td></tr>
        <?php endif; ?>
    </table>
</div>



<footer>
    &copy; 2025 GREEN STAY. Tous droits réservés.
</footer>

<!-- Validation JavaScript pour les services (Back Office) -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const serviceForm = document.querySelector('form[action*="service"]');

    if (serviceForm) {
        serviceForm.addEventListener('submit', function (e) {
            const nomService = serviceForm.querySelector('input[name="nom_service"]');
            const description = serviceForm.querySelector('textarea[name="description"]');
            const categorie = serviceForm.querySelector('input[name="categorie"]');
            const prixEstime = serviceForm.querySelector('input[name="prix_estime"]');
            const disponible = serviceForm.querySelector('select[name="disponible"]');

            let message = "";

            // Vérification : champs vides
            if (!nomService.value.trim()) {
                message += "Le champ Nom du service ne doit pas être vide.\n";
            }
            if (!description.value.trim()) {
                message += "Le champ Description ne doit pas être vide.\n";
            }
            if (!categorie.value.trim()) {
                message += "Le champ Catégorie ne doit pas être vide.\n";
            }
            if (!prixEstime.value.trim()) {
                message += "Le champ Prix estimé ne doit pas être vide.\n";
            }

            // Vérification : prix est un nombre positif
            if (prixEstime.value.trim() && (isNaN(prixEstime.value) || Number(prixEstime.value) < 0)) {
                message += "Le Prix estimé doit être un nombre positif.\n";
            }

            // Vérification : catégorie doit être une chaîne de caractères (pas un nombre)
            if (categorie.value.trim() && !/[a-zA-ZÀ-ÿ\s]/.test(categorie.value)) {
                message += "La catégorie doit être une chaîne de caractères valide .\n";
            }

            // Vérification : nom et description contiennent au moins une lettre
            if (nomService.value.trim() && !/[a-zA-ZÀ-ÿ]/.test(nomService.value)) {
                message += "Le nom du service doit contenir au moins une lettre.\n";
            }
            if (description.value.trim() && !/[a-zA-ZÀ-ÿ]/.test(description.value)) {
                message += "La description doit contenir au moins une lettre.\n";
            }

            if (message) {
                alert(message);
                e.preventDefault(); // Empêche l'envoi si erreurs
            }
        });
    }
});
</script>



</body>
</html>
