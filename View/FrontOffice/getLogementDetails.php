<?php
include('../../config/database.php');

if (isset($_GET['date_reservation'])) {
    $date_reservation = $_GET['date_reservation']; // Récupérer la date de réservation

    // Préparer la requête pour récupérer les détails du logement
    $sql = "SELECT l.nom, l.adresse, l.image FROM logements l
            JOIN reservations r ON l.id = r.logement_id
            WHERE r.date_reservation = :date_reservation";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':date_reservation', $date_reservation);

    if ($stmt->execute()) {
        $logement = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($logement) {
            echo json_encode($logement);
        } else {
            echo json_encode(['error' => 'Logement non trouvé.']);
        }
    } else {
        echo json_encode(['error' => 'Erreur lors de l\'exécution de la requête.']);
    }
} else {
    echo json_encode(['error' => 'Date de réservation manquante.']);
}
?>