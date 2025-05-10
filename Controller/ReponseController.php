<?php
require_once __DIR__ . '/../Model/Reponse.php';
require_once __DIR__ . '/../config/database.php'; // Inclure database.php pour accéder à la connexion

class ReponseController {
    private $pdo;

    public function __construct() {
        global $conn;  // Utiliser la variable globale $conn définie dans database.php
        $this->pdo = $conn;
    }

    public function ajouterReponse(Reponse $reponse) {
        $sql = "INSERT INTO reponses (id_reclamation, message, date_reponse) 
                VALUES (:id_reclamation, :message, :date_reponse)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_reclamation' => $reponse->getIdReclamation(),
            ':message' => $reponse->getMessage(),
            ':date_reponse' => $reponse->getDateReponse()
        ]);
    }

    public function modifierStatutReclamation($id_reclamation, $nouveau_statut) {
        $sql = "UPDATE reclamations SET statut = :statut WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':statut' => $nouveau_statut,
            ':id' => $id_reclamation
        ]);
    }

    public function afficherReponses() {
        $sql = "SELECT * FROM reponse";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function afficherReponseParId($id_reponse) {
        $sql = "SELECT * FROM reponse WHERE id_reponse = :id_reponse";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_reponse' => $id_reponse]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function supprimerReponse($id_reponse) {
        $sql = "DELETE FROM reponse WHERE id_reponse = :id_reponse";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_reponse' => $id_reponse]);
    }

    // Méthode pour modifier une réponse
    public function modifierReponse(Reponse $reponse) {
        $sql = "UPDATE reponse SET id_reclamation = :id_reclamation, message = :message, date_reponse = :date_reponse 
                WHERE id_reponse = :id_reponse";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_reclamation' => $reponse->getIdReclamation(),
            ':message' => $reponse->getMessage(),
            ':date_reponse' => $reponse->getDateReponse(),
            ':id_reponse' => $reponse->getIdReponse()
        ]);
    }
}
?>
