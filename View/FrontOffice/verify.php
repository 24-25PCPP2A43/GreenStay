<?php
session_start();

// Inclure les dépendances avec des chemins relatifs corrects
require_once __DIR__ . '/../../Controller/UserController.php';
require_once __DIR__ . '/../../Model/User.php';

$userController = new UserController();

if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = $_GET['token'];

    // Vérifie que le compte existe avec ce token et n'est pas encore vérifié
    if ($userController->verifyAccount($token) !== false) {
        $email = $userController->getActivatedUserEmailByCode($token);
       
        $_SESSION['verification_success'] = "Votre compte a été activé avec succès. Vous pouvez maintenant vous connecter.";
    } else {
        $_SESSION['verification_error'] = "Code d'activation invalide ou compte déjà activé.";
    }

    // Rediriger vers la page d'inscription avec message
    header('Location: inscription.php?activated=1');
    exit();
}

// Si aucun token présent, rediriger simplement
$_SESSION['verification_error'] = "Lien de vérification invalide ou expiré.";
header('Location: inscription.php');
exit();
?>
