<?php
 // Ajout du point-virgule à la fin
require_once __DIR__ . '/../Model/MessageModel.php';
class MessageController {

    private $model;

    public function __construct() {
        $this->model = new MessageModel();  // Instanciation du modèle MessageModel
    }

    // Traitement pour envoyer un message
    public function envoyerMessage($expediteur, $contenu) {
        if (!empty($contenu)) {
            return $this->model->ajouterMessage($expediteur, $contenu);  // Ajouter le message
        }
        return false;  // Retourne false si le contenu est vide
    }

    // Obtenir tous les messages
    public function afficherMessages() {
        return $this->model->getTousLesMessages();  // Retourner tous les messages
    }
}
?>
