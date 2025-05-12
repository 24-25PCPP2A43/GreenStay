<?php
class Reservation {
    private $id_reservation;
    private $id_logement;
    private $nom_client;
    private $email_client;
    private $date_debut;
    private $date_fin;
    private $statut;

    public function __construct($id_reservation = null, $id_logement = null, $nom_client = null, 
                              $email_client = null, $date_debut = null, $date_fin = null, $statut = null) {
        $this->id_reservation = $id_reservation;
        $this->id_logement = $id_logement;
        $this->nom_client = $nom_client;
        $this->email_client = $email_client;
        $this->date_debut = $date_debut;
        $this->date_fin = $date_fin;
        $this->statut = $statut;
    }

    // Getters
    public function getIdReservation() {
        return $this->id_reservation;
    }

    public function getIdLogement() {
        return $this->id_logement;
    }

    public function getNomClient() {
        return $this->nom_client;
    }

    public function getEmailClient() {
        return $this->email_client;
    }

    public function getDateDebut() {
        return $this->date_debut;
    }

    public function getDateFin() {
        return $this->date_fin;
    }

    public function getStatut() {
        return $this->statut;
    }

    // Setters
    public function setIdReservation($id_reservation) {
        $this->id_reservation = $id_reservation;
    }

    public function setIdLogement($id_logement) {
        $this->id_logement = $id_logement;
    }

    public function setNomClient($nom_client) {
        $this->nom_client = $nom_client;
    }

    public function setEmailClient($email_client) {
        $this->email_client = $email_client;
    }

    public function setDateDebut($date_debut) {
        $this->date_debut = $date_debut;
    }

    public function setDateFin($date_fin) {
        $this->date_fin = $date_fin;
    }

    public function setStatut($statut) {
        $this->statut = $statut;
    }
}
