<?php
require_once '../Controller/ReservationController.php';

$reservationController = new ReservationController();

// Check if ID is provided
if (isset($_GET['id'])) {
    $id_reservation = $_GET['id'];

    // Call the controller method to delete the reservation
    $reservationController->deleteReservation($id_reservation);

    // Redirect after deletion
    header('Location: listReservationFront.php');
    exit();
}
?>
