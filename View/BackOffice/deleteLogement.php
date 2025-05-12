<?php
require_once '/../../Controller/LogementController.php';

// Check if the ID is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Create an instance of the controller and delete the logement
    $logementController = new LogementController();
    $logementController->deleteLogement($id);

    // Redirect to the list page after deletion
    header('Location: listLogements.php');
    exit();
} else {
    // Redirect if no ID is provided
    header('Location: listLogements.php');
    exit();
}
?>
