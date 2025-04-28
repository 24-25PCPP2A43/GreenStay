<?php
require_once __DIR__ . '/../../Controller/ReponseController.php';

if (isset($_GET['id_reponse']) && !empty($_GET['id_reponse'])) {
    $id_reponse = intval($_GET['id_reponse']);
    
    $reponseController = new ReponseController();
    $reponseController->supprimerReponse($id_reponse);

    header('Location: consulter_reponses.php?success=1');
    exit();
} else {
    echo "ID réponse manquant.";
}
?>
