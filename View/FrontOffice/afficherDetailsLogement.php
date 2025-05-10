<?php
include('../../config/database.php');

// Vérifiez si l'ID du logement est passé en paramètre
if (isset($_GET['logement_id'])) {
    $logement_id = intval($_GET['logement_id']);

    // Préparer la requête pour récupérer les détails du logement
    $sql = "SELECT nom, adresse, image FROM logements WHERE id = :logement_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':logement_id', $logement_id);

    if ($stmt->execute()) {
        $logement = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($logement) {
            // Afficher les détails du logement
            echo "<h1>" . htmlspecialchars($logement['nom']) . "</h1>";
            echo "<p>Adresse: " . htmlspecialchars($logement['adresse']) . "</p>";
            if ($logement['image']) {
                echo "<img src='" . htmlspecialchars($logement['image']) . "' alt='Image du logement' />";
            } else {
                echo "<p>Aucune image disponible.</p>";
            }
        } else {
            echo "Logement non trouvé.";
        }
    } else {
        echo "Erreur lors de l'exécution de la requête.";
    }
} else {
    echo "ID de logement manquant.";
}
?>