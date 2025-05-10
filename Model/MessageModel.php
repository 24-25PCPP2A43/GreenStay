<?php
class MessageModel {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new PDO('mysql:host=localhost;dbname=ecotech', 'root', '');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    // Ajouter un message à la base
    public function ajouterMessage($expediteur, $contenu) {
        $stmt = $this->pdo->prepare("INSERT INTO messages (expediteur, contenu) VALUES (?, ?)");
        return $stmt->execute([$expediteur, $contenu]);
    }

    // Récupérer tous les messages
    public function getTousLesMessages() {
        $stmt = $this->pdo->query("SELECT * FROM messages ORDER BY date_envoi ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
