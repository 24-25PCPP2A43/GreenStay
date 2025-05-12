<?php
require_once __DIR__ . '/../Model/Reservation.php';
require_once __DIR__ . '/../Model/Config.php';
require_once __DIR__ . '/../Controller/EmailService.php'; // Ajout du service d'email

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/../vendor/autoload.php';

class ReservationController {
private $emailService;
    public function __construct() {
        $this->emailService = new EmailService();
    }
    // Send email using PHPMailer


    // HTML Email Template


    // Ajouter une réservation
    public function addReservation($reservation) {
        // 1) Insert into database
        $sql = "INSERT INTO reservation (
                    id_logement, nom_client, email_client,
                    date_debut, date_fin, statut
                ) VALUES (
                    :id_logement, :nom_client, :email_client,
                    :date_debut, :date_fin, :statut
                )";
        $db = config::getConnexion();

        try {
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([
                'id_logement'   => $reservation->getIdLogement(),
                'nom_client'    => $reservation->getNomClient(),
                'email_client'  => $reservation->getEmailClient(),
                'date_debut'    => $reservation->getDateDebut(),
                'date_fin'      => $reservation->getDateFin(),
                'statut'        => $reservation->getStatut(),
            ]);

            if ($result) {
                // Get logement title
                $sql = "SELECT titre FROM logement WHERE id_logement = :id_logement";
                $query = $db->prepare($sql);
                $query->execute(['id_logement' => $reservation->getIdLogement()]);
                $logement = $query->fetch();
                $logementTitle = $logement ? $logement['titre'] : "Logement #" . $reservation->getIdLogement();

                // 2) Send confirmation email if DB insert was successful
                $emailBody = "Details de votre reservation :\n\n" .
                    "▶ Logement : " . $logementTitle . "\n" .
                    "▶ Du " . $reservation->getDateDebut() . " au " . $reservation->getDateFin() . "\n\n" .
                    "Votre reservation est actuellement " . $reservation->getStatut() . ".\n" .
                    "Vous recevrez un email si le statut de votre reservation change.";

                $this->emailService->send(
                    $reservation->getEmailClient(),
                    $reservation->getNomClient(),
                    'Votre reservation WoOx Travel est confirmee !',
                    $emailBody
                );

                return true;
            }

            return false;
        } catch (\PDOException $e) {
            error_log("DB Error: " . $e->getMessage());
            return false;
        }
    }

    // Liste de toutes les réservations
    public function listReservations() {
        $sql = "SELECT r.*, l.titre as titre_logement 
                FROM reservation r 
                JOIN logement l ON r.id_logement = l.id_logement";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Supprimer une réservation
    public function deleteReservation($id) {
        $sql = "DELETE FROM reservation WHERE id_reservation = :id";
        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }

    }

    // Récupérer une réservation par ID
    public function showReservation($id) {
        $sql = "SELECT r.*, l.titre as titre_logement 
                FROM reservation r 
                JOIN logement l ON r.id_logement = l.id_logement 
                WHERE r.id_reservation = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            $reservation = $query->fetch();
            return $reservation;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
    /**
     * Recherche des réservations en fonction des critères fournis
     *
     * @param string $search Terme de recherche global (nom ou email)
     * @param string $status Statut de la réservation
     * @param string $dateFrom Date de début de la période
     * @param string $dateTo Date de fin de la période
     * @return array Liste des réservations filtrées
     */
    public function searchReservations($search = '', $status = '', $dateFrom = '', $dateTo = '') {
        $sql = "SELECT r.*, l.titre as titre_logement 
            FROM reservation r 
            JOIN logement l ON r.id_logement = l.id_logement 
            WHERE 1=1";
        $params = [];

        // Recherche par terme global (nom ou email)
        if (!empty($search)) {
            $sql .= " AND (r.nom_client LIKE :search OR r.email_client LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        // Filtre par statut
        if (!empty($status)) {
            $sql .= " AND r.statut = :status";
            $params[':status'] = $status;
        }

        // Filtre par date de début
        if (!empty($dateFrom)) {
            $sql .= " AND r.date_debut >= :dateFrom";
            $params[':dateFrom'] = $dateFrom;
        }

        // Filtre par date de fin
        if (!empty($dateTo)) {
            $sql .= " AND r.date_fin <= :dateTo";
            $params[':dateTo'] = $dateTo;
        }

        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute($params);
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Récupère la liste des statuts distincts
     *
     * @return array Liste des statuts
     */
    public function getDistinctStatuses() {
        $sql = "SELECT DISTINCT statut FROM reservation ORDER BY statut";
        $db = config::getConnexion();
        try {
            $query = $db->query($sql);
            $result = $query->fetchAll(PDO::FETCH_COLUMN, 0);
            return $result;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Mettre à jour une réservation
    public function updateReservation($id, $id_logement, $nom_client, $email_client, $date_debut, $date_fin, $statut) {
        $sql = "UPDATE reservation SET 
                id_logement = :id_logement, 
                nom_client = :nom_client, 
                email_client = :email_client, 
                date_debut = :date_debut, 
                date_fin = :date_fin, 
                statut = :statut
                WHERE id_reservation = :id";

        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $result = $query->execute([
                'id' => $id,
                'id_logement' => $id_logement,
                'nom_client' => $nom_client,
                'email_client' => $email_client,
                'date_debut' => $date_debut,
                'date_fin' => $date_fin,
                'statut' => $statut
            ]);

            if ($result) {
                // Get the old reservation to check for status change
                $oldRes = $this->showReservation($id);

                // If status has changed, send a notification email
                if ($oldRes && $oldRes['statut'] != $statut) {
                    // Get logement title
                    $sql = "SELECT titre FROM logement WHERE id_logement = :id_logement";
                    $query = $db->prepare($sql);
                    $query->execute(['id_logement' => $id_logement]);
                    $logement = $query->fetch();
                    $logementTitle = $logement ? $logement['titre'] : "Logement #" . $id_logement;

                    $emailBody = "Mise à jour de votre réservation :\n\n" .
                        "▶ Logement : " . $logementTitle . "\n" .
                        "▶ Du " . $date_debut . " au " . $date_fin . "\n\n" .
                        "Le statut de votre réservation a été changé à : " . $statut . "\n\n" .
                        "Si vous avez des questions, n'hésitez pas à nous contacter.";

                    $this->sendEmail(
                        $email_client,
                        $nom_client,
                        'Mise à jour de votre réservation WoOx Travel',
                        $emailBody
                    );
                }
            }

            return $result;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }


    // Liste des réservations pour un logement spécifique
    public function getReservationsByLogement($id_logement) {
        $sql = "SELECT r.*, l.titre as titre_logement 
                FROM reservation r 
                JOIN logement l ON r.id_logement = l.id_logement 
                WHERE r.id_logement = :id_logement";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id_logement' => $id_logement]);
            return $query->fetchAll();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Vérifier disponibilité pour une période
    public function checkAvailability($id_logement, $date_debut, $date_fin) {
        $sql = "SELECT COUNT(*) as count FROM reservation 
                WHERE id_logement = :id_logement 
                AND statut != 'annulée' 
                AND ((date_debut BETWEEN :date_debut AND :date_fin) 
                OR (date_fin BETWEEN :date_debut AND :date_fin) 
                OR (:date_debut BETWEEN date_debut AND date_fin))";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id_logement' => $id_logement,
                'date_debut' => $date_debut,
                'date_fin' => $date_fin
            ]);
            $result = $query->fetch();
            return $result['count'] == 0; // Retourne true si disponible, false sinon
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Changer le statut d'une réservation
    public function changeStatus($id_reservation, $statut) {
        // Get current reservation
        $reservation = $this->showReservation($id_reservation);
        if (!$reservation) {
            return false;
        }

        // Update status
        $sql = "UPDATE reservation SET statut = :statut WHERE id_reservation = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $result = $query->execute([
                'id' => $id_reservation,
                'statut' => $statut
            ]);

            // Send notification email if update successful
            if ($result && $reservation['statut'] != $statut) {
                $emailBody = "Mise à jour de votre réservation :\n\n" .
                    "▶ Logement : " . $reservation['titre_logement'] . "\n" .
                    "▶ Du " . $reservation['date_debut'] . " au " . $reservation['date_fin'] . "\n\n" .
                    "Le statut de votre réservation a été changé à : " . $statut . "\n\n" .
                    "Si vous avez des questions, n'hésitez pas à nous contacter.";

                $this->sendEmail(
                    $reservation['email_client'],
                    $reservation['nom_client'],
                    'Mise à jour de votre réservation WoOx Travel',
                    $emailBody
                );
            }

            return $result;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }


}