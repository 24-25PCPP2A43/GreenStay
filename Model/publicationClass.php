<?php

class PublicationClass {
    private $id_publication = null;
    private $titre = null;
    private $contenu = null;
    private $image = null;
    private $statut = null;
    private $date = null;
    private $id_categorie = null; // Nouvel attribut

    public function __construct($id_publication = null, $titre, $contenu, $image, $statut, $date, $id_categorie = null) {
        $this->id_publication = $id_publication;
        $this->titre = $titre;
        $this->contenu = $contenu;
        $this->image = $image;
        $this->statut = $statut;
        $this->date = $date;
        $this->id_categorie = $id_categorie; // Initialisation de l'attribut
    }

    // Getters
    public function getIdPublication() {
        return $this->id_publication;
    }

    public function getTitre() {
        return $this->titre;
    }

    public function getContenu() {
        return $this->contenu;
    }

    public function getImage() {
        return $this->image;
    }

    public function getStatut() {
        return $this->statut;
    }

    public function getDate() {
        return $this->date;
    }

    public function getIdCategorie() { // Getter pour id_categorie
        return $this->id_categorie;
    }

    // Setters
    public function setTitre($titre) {
        $this->titre = $titre;
    }

    public function setContenu($contenu) {
        $this->contenu = $contenu;
    }

    public function setImage($image) {
        $this->image = $image;
    }

    public function setStatut($statut) {
        $this->statut = $statut;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function setIdCategorie($id_categorie) { // Setter pour id_categorie
        $this->id_categorie = $id_categorie;
    }
}
?>
