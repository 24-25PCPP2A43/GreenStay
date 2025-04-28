<?php
require_once '../config/database.php';
require_once '../model/Reclamation.php';

$reclamation = new Reclamation($pdo);

// Ajouter une réclamation
if (isset($_POST['add'])) {
    $client_id = $_POST['client_id'];
    $reservation_id = $_POST['reservation_id'];
    $sujet = $_POST['sujet'];
    $message = $_POST['message'];
    
    if ($reclamation->create($client_id, $reservation_id, $sujet, $message)) {
        header("Location: ../view/front/listReclamations.php"); // Redirection vers la liste des réclamations
    }
}

// Modifier une réclamation
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $sujet = $_POST['sujet'];
    $message = $_POST['message'];
    
    if ($reclamation->update($id, $sujet, $message)) {
        header("Location: ../view/front/listReclamations.php"); // Redirection après modification
    }
}

// Supprimer une réclamation
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    if ($reclamation->delete($id)) {
        header("Location: ../view/front/listReclamations.php"); // Redirection après suppression
    }
}

// Mettre à jour le statut de la réclamation
if (isset($_GET['update_status'])) {
    $id = $_GET['id'];
    $statut = $_GET['statut']; // 'En cours de traitement' ou 'Terminé'
    
    if ($reclamation->updateStatus($id, $statut)) {
        header("Location: ../view/back/viewReclamations.php"); // Redirection vers la page admin
    }
}

// Ajouter une réponse de l'admin à une réclamation
if (isset($_POST['add_response'])) {
    $reclamation_id = $_POST['reclamation_id'];
    $reponse = $_POST['reponse']; // Réponse de l'admin
    
    if ($reclamation->addResponse($reclamation_id, $reponse)) {
        header("Location: ../view/front/listReclamations.php"); // Redirection vers la liste des réclamations
    }
}
?>
