<?php
class Reponse {
    private $id_reponse;
    private $id_reclamation;
    private $message;
    private $date_reponse;

    public function __construct($id_reclamation, $message, $date_reponse, $id_reponse = null) {
        $this->id_reclamation = $id_reclamation;
        $this->message = $message;
        $this->date_reponse = $date_reponse;
        $this->id_reponse = $id_reponse;
    }

    public function getIdReponse() {
        return $this->id_reponse;
    }

    public function getIdReclamation() {
        return $this->id_reclamation;
    }

    public function getMessage() {
        return $this->message;
    }

    public function getDateReponse() {
        return $this->date_reponse;
    }

    public function setIdReponse($id_reponse) {
        $this->id_reponse = $id_reponse;
    }

    public function setIdReclamation($id_reclamation) {
        $this->id_reclamation = $id_reclamation;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function setDateReponse($date_reponse) {
        $this->date_reponse = $date_reponse;
    }
}
?>
