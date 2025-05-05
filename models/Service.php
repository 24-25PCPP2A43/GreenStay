<?php
require_once __DIR__ . '/../config/database.php';

class Service {
    private $conn;
    private $table = "services"; // Table des services

    private $id_service;
    private $nom_service;
    private $description;
    private $categorie;
    private $prix_estime;
    private $disponible;
    private $image;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function getConnection() {
        return $this->conn;
    }

    public function getTable() {
        return $this->table;
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id_service = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Getters
    public function getIdService() {
        return $this->id_service;
    }

    public function getNomService() {
        return $this->nom_service;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getCategorie() {
        return $this->categorie;
    }
    public function getimage() {
        return $this->image;
    }

    public function getPrixEstime() {
        return $this->prix_estime;
    }

    public function getDisponible() {
        return $this->disponible;
    }

    // Setters
    public function setIdService($id_service) {
        $this->id_service = $id_service;
    }
    public function setimage($image) {
        $this->image = $image;
    }
    public function setNomService($nom_service) {
        $this->nom_service = $nom_service;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setCategorie($categorie) {
        $this->categorie = $categorie;
    }

    public function setPrixEstime($prix_estime) {
        $this->prix_estime = $prix_estime;
    }

    public function setDisponible($disponible) {
        $this->disponible = $disponible;
    }
}
?>
