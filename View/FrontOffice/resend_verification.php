<?php
session_start();

// Correction du chemin (remonte de 2 niveaux depuis View/FrontOffice)
require_once __DIR__ . '/../../Controller/UserController.php';

if (isset($_GET['email'])) {
    $email = $_GET['email'];
    $userController = new UserController();
    
    if ($userController->resendVerificationEmail($email)) {
        $_SESSION['verification_resent'] = "Un nouvel email de vérification a été envoyé à votre adresse.";
    } else {
        $_SESSION['verification_error'] = "Impossible de renvoyer l'email. Votre compte est peut-être déjà vérifié.";
    }
}

header("Location: inscription.php");
exit();
?>