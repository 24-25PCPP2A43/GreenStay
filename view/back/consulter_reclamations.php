<?php
include('../../config/database.php');

// Requête pour récupérer les réclamations
$sql = "SELECT r.id, r.sujet, r.message, r.statut, c.nom, c.prenom, re.date_reservation
        FROM reclamations r
        JOIN clients c ON r.client_id = c.id
        JOIN reservations re ON r.reservation_id = re.id";

try {
    $result = $conn->query($sql);

    if ($result === false) {
        throw new Exception("Erreur dans la requête SQL : " . implode(" ", $conn->errorInfo()));
    }
} catch (Exception $e) {
    die("Erreur de la requête : " . $e->getMessage());
}
?>

<?php include('includes/header.php'); ?>

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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result->rowCount() > 0): ?>
                <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['sujet']) ?></td>
                        <td><?= htmlspecialchars($row['message']) ?></td>
                        <td><?= $row['statut'] ?></td>
                        <td><?= $row['nom'] . ' ' . $row['prenom'] ?></td>
                        <td><?= $row['date_reservation'] ?></td>
                        <td>
                            <a href="repondre.php?id=<?= $row['id'] ?>" class="btn btn-outline-dark btn-sm">Répondre</a>
                            <a href="changerStatut.php?id=<?= $row['id'] ?>" class="btn btn-gradient-blue btn-sm">Changer statut</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center text-muted">Aucune réclamation trouvée.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>

        <div class="text-center mt-4">
            <a href="../../index.php" class="btn btn-marine">Retour à l'accueil</a>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<style>
    /* Personnalisation du titre de la section */
    .custom-header {
        color: #003366; /* Bleu foncé pour le titre */
        font-family: 'Poppins', sans-serif;
        font-size: 36px;
        font-weight: bold;
        text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2); /* Ombre subtile pour le texte */
    }

    /* Bouton Retour à l'accueil en bleu marine */
    .btn-marine {
        background-color: #003366; /* Bleu marine */
        color: white;
        border: none;
    }
    .btn-marine:hover {
        background-color: #00224d; /* Un bleu plus foncé au survol */
    }

    /* Bouton Changer statut avec dégradé de bleu */
    .btn-gradient-blue {
        background: linear-gradient(to right, #00aaff, #0047b3); /* Dégradé de bleu */
        color: white;
        border: none;
    }
    .btn-gradient-blue:hover {
        background: linear-gradient(to right, #0047b3, #00aaff); /* Inverser le dégradé au survol */
    }

    /* Pour enlever les marges et la largeur grise de la section */
    .reclamation-section {
        width: 100%;
        margin: 0;
        padding: 0;
    }

    .container-fluid {
        padding: 0;
        width: 100%;
    }
</style>
