<?php
class Reclamation {
    private $id;
    private $client_id;
    private $reservation_id;
    private $sujet;
    private $message;
    private $statut;
    private $date_creation;
    private $reponse_id;
    private $reponse;

    // Constructeur
    public function __construct($id = null, $client_id = null, $reservation_id = null, $sujet = null, 
                                $message = null, $statut = null, $date_creation = null, $reponse_id = null, 
                                $reponse = null) {
        $this->id = $id;
        $this->client_id = $client_id;
        $this->reservation_id = $reservation_id;
        $this->sujet = $sujet;
        $this->message = $message;
        $this->statut = $statut;
        $this->date_creation = $date_creation;
        $this->reponse_id = $reponse_id;
        $this->reponse = $reponse;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getClientId() {
        return $this->client_id;
    }

    public function getReservationId() {
        return $this->reservation_id;
    }

    public function getSujet() {
        return $this->sujet;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getStatut() {
        return $this->statut;
    }

    public function getDateCreation() {
        return $this->date_creation;
    }

    public function getReponseId() {
        return $this->reponse_id;
    }

    public function getReponse() {
        return $this->reponse;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setClientId($client_id) {
        $this->client_id = $client_id;
    }

    public function setReservationId($reservation_id) {
        $this->reservation_id = $reservation_id;
    }

    public function setSujet($sujet) {
        $this->sujet = $sujet;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function setStatut($statut) {
        $this->statut = $statut;
    }

    public function setDateCreation($date_creation) {
        $this->date_creation = $date_creation;
    }

    public function setReponseId($reponse_id) {
        $this->reponse_id = $reponse_id;
    }

    public function setReponse($reponse) {
        $this->reponse = $reponse;
    }
}
?>
