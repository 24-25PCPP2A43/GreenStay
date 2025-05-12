<?php
require_once '../Controller/LogementController.php';

$logementController = new LogementController();

// Check if ID is provided
if (isset($_GET['id'])) {
    $id_logement = $_GET['id'];

    // Call the controller method to delete the logement
    $logementController->deleteLogement($id_logement);

    // Redirect after deletion
    header('Location: listLogementFront.php');
    exit();
}
?>
