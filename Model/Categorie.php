<?php

class Categorie {
    private $id_categorie = null;
    private $nom_categorie = null;
    private $desc_categorie = null;

    public function __construct($id_categorie = null, $nom_categorie, $desc_categorie) {
        $this->id_categorie = $id_categorie;
        $this->nom_categorie = $nom_categorie;
        $this->desc_categorie = $desc_categorie;
    }

    // Getters
    public function getIdCategorie() {
        return $this->id_categorie;
    }

    public function getNomCategorie() {
        return $this->nom_categorie;
    }

    public function getDescCategorie() {
        return $this->desc_categorie;
    }

    // Setters
    public function setNomCategorie($nom_categorie) {
        $this->nom_categorie = $nom_categorie;
    }

    public function setDescCategorie($desc_categorie) {
        $this->desc_categorie = $desc_categorie;
    }
}
?>
