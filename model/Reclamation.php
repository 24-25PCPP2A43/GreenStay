<?php
class Reclamation {
    private $id;
    private $client_id;
    private $reservation_id;
    private $sujet;
    private $message;
    private $statut;

    public function __construct($sujet, $message, $statut, $client_id, $reservation_id, $id = null) {
        $this->sujet = $sujet;
        $this->message = $message;
        $this->statut = $statut;
        $this->client_id = $client_id;
        $this->reservation_id = $reservation_id;
        $this->id = $id;
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
}
?>
