<?php
require_once __DIR__ . '/../config/database.php'; // Assure-toi que ce chemin est correct

class DemandeService {
    private $conn;
    private $table = "demande_service";

    // Propriétés privées
    private $id;
    private $service_id;
    private $client_id;
    private $description;
    private $date_demande;
    private $etat;
   
   
  
    
    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function getConnection() {
        return $this->conn;
    }

    public function getTable() {
        return $this->table;
    }

    public function getId() {
        return $this->id;
    }

    public function getServiceId() {
        return $this->service_id;
    }

    public function getClientId() {
        return $this->client_id;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getDateDemande() {
        return $this->date_demande;
    }

    public function getEtat() {
        return $this->etat;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setServiceId($service_id) {
        $this->service_id = $service_id;
    }

    public function setClientId($client_id) {
        $this->client_id = $client_id;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setDateDemande($date_demande) {
        $this->date_demande = $date_demande;
    }

    public function setEtat($etat) {
        $this->etat = $etat;
    }
    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
