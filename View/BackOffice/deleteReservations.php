// In deleteReservation.php
<?php
require_once '../Controller/ReservationController.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $reservationController = new ReservationController();
    $reservationController->deleteReservation($id);

    header('Location: listReservations.php');
    exit();
} else {
    header('Location: listReservations.php');
    exit();
}
?>
