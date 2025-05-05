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

        .container {
            width: 90%;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background-color: #28a745;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }

        .navbar {
            width: 250px;
            background-color: #28a745;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            padding-top: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .navbar a {
            display: block;
            padding: 15px;
            color: white;
            text-decoration: none;
            font-size: 18px;
            border-bottom: 1px solid #1e7e34;
            transition: background-color 0.3s ease;
        }

        .navbar a:hover {
            background-color: #1e7e34;
        }

        .main-content {
            margin-left: 270px;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
            background-color: #28a745;
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
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            border: none;
            font-size: 16px;
            cursor: pointer;
        }

        form button:hover {
            background-color: #218838;
        }

        footer {
            background-color: #28a745;
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
</header>

<div class="navbar">
    <a href="?action=back_office">Dashboard</a>
    <a href="?action=front_office">Voir le Front Office</a>

</div>

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
<div class="container mt-4">
    <h2>📜 Historique des activités</h2>
    <ul class="list-group">
        <?php if (empty($logs)) : ?>
            <li class="list-group-item">Aucune activité enregistrée.</li>
        <?php else: ?>
            <?php foreach ($logs as $log) : ?>
                <li class="list-group-item"><?= htmlspecialchars($log) ?></li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>
<h3>🕒 Historique des activités</h3>
<?php if (!empty($logs)): ?>
    <h3>🕓 Historique des activités</h3>
    <ul>
        <?php foreach ($logs as $log): ?>
            <li><?= htmlspecialchars($log) ?></li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Aucune activité enregistrée.</p>
<?php endif; ?>


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
