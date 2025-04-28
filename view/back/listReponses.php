<?php
require_once '../../config/database.php'; // Connexion à la base de données

// Requête pour récupérer les réclamations avec la réponse (si disponible)
$sql = "SELECT r.id, r.sujet, r.message AS reclamation_message, r.statut, c.nom, c.prenom, re.date_reservation, 
                rep.message AS reponse_message, rep.date_reponse
        FROM reclamations r
        JOIN clients c ON r.client_id = c.id
        JOIN reservations re ON r.reservation_id = re.id
        LEFT JOIN reponse rep ON r.id = rep.id_reclamation";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute();  // Exécution de la requête
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupération des résultats
} catch (PDOException $e) {
    die("Erreur de la requête : " . $e->getMessage());
}

?>

<?php include('includes/header.php'); ?>

<!-- Intégration du fichier CSS dashboard.css -->
<link rel="stylesheet" href="../../assets/css/dashboard.css">

<!-- Section avec l'image de fond -->
<div class="container-fluid mt-5 mb-5 reclamation-section" style="background-image: url('../../assets/images/best-03.jpg'); background-size: cover; background-position: center; padding: 0;">
    <div class="container p-4 shadow rounded" style="background-color: rgba(255, 255, 255, 0.8);">
        <h2 class="text-center custom-header">Liste des Réclamations</h2>

        <table class="table table-hover table-bordered text-center align-middle">
            <thead class="table-info">
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
                                // Affichage des statuts sous forme lisible
                                switch ($row['statut']) {
                                    case 0:
                                        echo "En cours";
                                        break;
                                    case 1:
                                        echo "Résolu";
                                        break;
                                    case 2:
                                        echo "Non résolu";
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
                                Pas encore répondu
                            <?php endif; ?>
                        </td>
                        <td>
                            <!-- Répondre à la réclamation -->
                            <a href="ajouterReponse.php?id=<?= $row['id'] ?>" class="btn btn-outline-dark btn-sm">Répondre</a>

                            <!-- Changer le statut -->
                            <a href="changerStatut.php?id=<?= $row['id'] ?>" class="btn btn-gradient-blue btn-sm">Changer statut</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8" class="text-center text-muted">Aucune réclamation trouvée.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>

        <!-- Bouton retour à l'accueil -->
        <div class="text-center mt-2">
            <a href="../../index.php" class="btn btn-marine">Retour à l'accueil</a>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
