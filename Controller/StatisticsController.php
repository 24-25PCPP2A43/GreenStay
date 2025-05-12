<?php
// Controller/StatisticsController.php
require_once __DIR__ . '/../Model/Config.php';

class StatisticsController {

    /**
     * Obtenir le nombre total de logements
     * @return int
     */
    public function getTotalLogements() {
        $sql = "SELECT COUNT(*) as total FROM logement";
        $db = Config::getConnexion();
        try {
            $query = $db->query($sql);
            $result = $query->fetch();
            return $result['total'];
        } catch (Exception $e) {
            error_log("Erreur lors du calcul du nombre total de logements: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtenir le nombre total de réservations
     * @return int
     */
    public function getTotalReservations() {
        $sql = "SELECT COUNT(*) as total FROM reservation";
        $db = Config::getConnexion();
        try {
            $query = $db->query($sql);
            $result = $query->fetch();
            return $result['total'];
        } catch (Exception $e) {
            error_log("Erreur lors du calcul du nombre total de réservations: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Calculer le taux d'occupation moyen des logements
     * @return float
     */
    public function getOccupancyRate() {
        $sql = "SELECT 
                    COUNT(DISTINCT r.id_reservation) as reservation_count,
                    SUM(DATEDIFF(r.date_fin, r.date_debut)) as total_days,
                    (SELECT SUM(DATEDIFF(CURRENT_DATE, l.date_ajout)) FROM logement l) as total_available_days
                FROM 
                    reservation r 
                WHERE 
                    r.statut = 'confirmée'";

        $db = Config::getConnexion();
        try {
            $query = $db->query($sql);
            $result = $query->fetch();

            if ($result['total_available_days'] > 0) {
                $rate = ($result['total_days'] / $result['total_available_days']) * 100;
                return min($rate, 100); // Ne pas dépasser 100%
            }
            return 0;
        } catch (Exception $e) {
            error_log("Erreur lors du calcul du taux d'occupation: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Calculer la durée moyenne de séjour
     * @return float
     */
    public function getAverageStayDuration() {
        $sql = "SELECT AVG(DATEDIFF(date_fin, date_debut)) as avg_stay
                FROM reservation
                WHERE statut != 'annulée'";

        $db = Config::getConnexion();
        try {
            $query = $db->query($sql);
            $result = $query->fetch();
            return $result['avg_stay'] ?: 0;
        } catch (Exception $e) {
            error_log("Erreur lors du calcul de la durée moyenne de séjour: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtenir la ville la plus populaire
     * @return array
     */
    public function getMostPopularCity() {
        $sql = "SELECT l.ville, COUNT(r.id_reservation) as count
                FROM reservation r
                JOIN logement l ON r.id_logement = l.id_logement
                WHERE r.statut != 'annulée'
                GROUP BY l.ville
                ORDER BY count DESC
                LIMIT 1";

        $db = Config::getConnexion();
        try {
            $query = $db->query($sql);
            $result = $query->fetch();
            return $result ?: ['ville' => 'Aucune donnée', 'count' => 0];
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération de la ville la plus populaire: " . $e->getMessage());
            return ['ville' => 'Erreur', 'count' => 0];
        }
    }

    /**
     * Calculer le revenu total des réservations
     * @return float
     */
    public function getTotalRevenue() {
        $sql = "SELECT SUM(l.prix_par_nuit * DATEDIFF(r.date_fin, r.date_debut)) as total_revenue
                FROM reservation r
                JOIN logement l ON r.id_logement = l.id_logement
                WHERE r.statut = 'confirmée'";

        $db = Config::getConnexion();
        try {
            $query = $db->query($sql);
            $result = $query->fetch();
            return $result['total_revenue'] ?: 0;
        } catch (Exception $e) {
            error_log("Erreur lors du calcul du revenu total: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtenir les revenus par mois
     * @return array
     */
    public function getRevenueByMonth() {
        $sql = "SELECT 
                    DATE_FORMAT(r.date_debut, '%Y-%m') as month,
                    DATE_FORMAT(r.date_debut, '%b %Y') as month_name,
                    SUM(l.prix_par_nuit * DATEDIFF(r.date_fin, r.date_debut)) as total_revenue
                FROM 
                    reservation r
                JOIN 
                    logement l ON r.id_logement = l.id_logement
                WHERE 
                    r.statut = 'confirmée'
                GROUP BY 
                    month
                ORDER BY 
                    month ASC
                LIMIT 12";

        $db = Config::getConnexion();
        try {
            $query = $db->query($sql);
            $results = $query->fetchAll();

            // Si aucun résultat, retourner des données fictives pour les 6 derniers mois
            if (empty($results)) {
                $results = [];
                for ($i = 5; $i >= 0; $i--) {
                    $date = new DateTime();
                    $date->modify("-$i month");
                    $results[] = [
                        'month' => $date->format('Y-m'),
                        'month_name' => $date->format('M Y'),
                        'total_revenue' => 0
                    ];
                }
            }

            return $results;
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des revenus par mois: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtenir le nombre de réservations par statut
     * @return array
     */
    public function getReservationsByStatus() {
        $sql = "SELECT 
                    statut,
                    COUNT(*) as count
                FROM 
                    reservation
                GROUP BY 
                    statut
                ORDER BY
                    count DESC";

        $db = Config::getConnexion();
        try {
            $query = $db->query($sql);
            $results = $query->fetchAll();

            // Si aucun résultat, retourner des données fictives
            if (empty($results)) {
                $results = [
                    ['statut' => 'confirmée', 'count' => 0],
                    ['statut' => 'en attente', 'count' => 0],
                    ['statut' => 'annulée', 'count' => 0]
                ];
            }

            return $results;
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des réservations par statut: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtenir les logements les plus réservés
     * @param int $limit Nombre de logements à retourner
     * @return array
     */
    public function getTopLogements($limit = 5) {
        $sql = "SELECT 
                    l.id_logement,
                    l.titre,
                    l.ville,
                    COUNT(r.id_reservation) as reservation_count,
                    SUM(l.prix_par_nuit * DATEDIFF(r.date_fin, r.date_debut)) as revenue
                FROM 
                    logement l
                LEFT JOIN 
                    reservation r ON l.id_logement = r.id_logement AND r.statut != 'annulée'
                GROUP BY 
                    l.id_logement, l.titre, l.ville
                ORDER BY 
                    reservation_count DESC, revenue DESC
                LIMIT :limit";

        $db = Config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindValue(':limit', $limit, PDO::PARAM_INT);
            $query->execute();
            return $query->fetchAll();
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des top logements: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtenir les statistiques de réservation par type de logement
     * @return array
     */
    public function getReservationsByType() {
        $sql = "SELECT 
                    l.type,
                    COUNT(r.id_reservation) as reservation_count
                FROM 
                    logement l
                LEFT JOIN 
                    reservation r ON l.id_logement = r.id_logement
                WHERE 
                    r.statut != 'annulée'
                GROUP BY 
                    l.type
                ORDER BY 
                    reservation_count DESC";

        $db = Config::getConnexion();
        try {
            $query = $db->query($sql);
            return $query->fetchAll();
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des réservations par type: " . $e->getMessage());
            return [];
        }
    }
}