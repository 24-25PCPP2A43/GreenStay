<?php
class Logement {
    private $id_logement;
    private $titre;
    private $description;
    private $adresse;
    private $ville;
    private $type;
    private $prix_par_nuit;
    private $capacite;
    private $image;
    private $disponibilite;

    public function __construct($id_logement = null, $titre = null, $description = null, $adresse = null, 
                               $ville = null, $type = null, $prix_par_nuit = null, $capacite = null, 
                               $image = null, $disponibilite = null) {
        $this->id_logement = $id_logement;
        $this->titre = $titre;
        $this->description = $description;
        $this->adresse = $adresse;
        $this->ville = $ville;
        $this->type = $type;
        $this->prix_par_nuit = $prix_par_nuit;
        $this->capacite = $capacite;
        $this->image = $image;
        $this->disponibilite = $disponibilite;
    }

    // Getters
    public function getIdLogement() {
        return $this->id_logement;
    }

    public function getTitre() {
        return $this->titre;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getAdresse() {
        return $this->adresse;
    }

    public function getVille() {
        return $this->ville;
    }

    public function getType() {
        return $this->type;
    }

    public function getPrixParNuit() {
        return $this->prix_par_nuit;
    }

    public function getCapacite() {
        return $this->capacite;
    }

    public function getImage() {
        return $this->image;
    }

    public function getDisponibilite() {
        return $this->disponibilite;
    }

    // Setters
    public function setIdLogement($id_logement) {
        $this->id_logement = $id_logement;
    }

    public function setTitre($titre) {
        $this->titre = $titre;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setAdresse($adresse) {
        $this->adresse = $adresse;
    }

    public function setVille($ville) {
        $this->ville = $ville;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function setPrixParNuit($prix_par_nuit) {
        $this->prix_par_nuit = $prix_par_nuit;
    }

    public function setCapacite($capacite) {
        $this->capacite = $capacite;
    }

    public function setImage($image) {
        $this->image = $image;
    }

    public function setDisponibilite($disponibilite) {
        $this->disponibilite = $disponibilite;
    }
}
