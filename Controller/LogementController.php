<?php
require_once __DIR__ . '/../Model/Logement.php';
require_once __DIR__ . '/../Model/Config.php';

class LogementController {

    // Ajouter un logement
    public function addLogement($logement) {
        $sql = "INSERT INTO logement (titre, description, adresse, ville, type, prix_par_nuit, capacite, image, disponibilite)
                VALUES (:titre, :description, :adresse, :ville, :type, :prix_par_nuit, :capacite, :image, :disponibilite)";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'titre'          => $logement->getTitre(),
                'description'    => $logement->getDescription(),
                'adresse'        => $logement->getAdresse(),
                'ville'          => $logement->getVille(),
                'type'           => $logement->getType(),
                'prix_par_nuit'  => $logement->getPrixParNuit(),
                'capacite'       => $logement->getCapacite(),
                'image'          => $logement->getImage(),
                'disponibilite'  => $logement->getDisponibilite()
            ]);
        } catch (PDOException $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    /**
     * Récupère tous les logements, éventuellement triés.
     *
     * @param string|null $sortBy   Colonne à trier (titre, ville, type, prix_par_nuit, capacite, disponibilite)
     * @param string      $sortDir  'ASC' ou 'DESC'
     * @return array
     */
    public function listLogements($sortBy = null, $sortDir = 'ASC') {
        // Liste blanche des colonnes autorisées
        $allowed = ['titre','ville','type','prix_par_nuit','capacite','disponibilite'];
        $orderBy = '';

        if ($sortBy && in_array($sortBy, $allowed, true)) {
            $direction = strtoupper($sortDir) === 'DESC' ? 'DESC' : 'ASC';
            // On backtick le nom de colonne pour éviter toute injection
            $orderBy = " ORDER BY `$sortBy` $direction";
        }

        $sql = "SELECT * FROM logement" . $orderBy;
        $db  = config::getConnexion();
        try {
            $stmt = $db->query($sql);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Supprimer un logement
    public function deleteLogement($id) {
        $sql = "DELETE FROM logement WHERE id_logement = :id";
        $db  = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Récupérer un logement par ID
    public function showLogement($id) {
        $sql = "SELECT * FROM logement WHERE id_logement = :id";
        $db  = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);
            return $query->fetch();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Mettre à jour un logement
    public function updateLogement($id, $titre, $description, $adresse, $ville, $type, $prix_par_nuit, $capacite, $image, $disponibilite) {
        $sql = "UPDATE logement SET 
                    titre          = :titre, 
                    description    = :description, 
                    adresse        = :adresse, 
                    ville          = :ville, 
                    type           = :type, 
                    prix_par_nuit  = :prix_par_nuit, 
                    capacite       = :capacite, 
                    image          = :image, 
                    disponibilite  = :disponibilite
                WHERE id_logement   = :id";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([
                'id'               => $id,
                'titre'            => $titre,
                'description'      => $description,
                'adresse'          => $adresse,
                'ville'            => $ville,
                'type'             => $type,
                'prix_par_nuit'    => $prix_par_nuit,
                'capacite'         => $capacite,
                'image'            => $image,
                'disponibilite'    => $disponibilite
            ]);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Rechercher des logements par ville
    /**
     * Recherche des logements en fonction des critères fournis
     *
     * @param string $search Terme de recherche global
     * @param string $type Type de logement
     * @param string $ville Ville
     * @param string $disponibilite Statut de disponibilité
     * @param float $minPrice Prix minimum par nuit
     * @param float $maxPrice Prix maximum par nuit
     * @return array Liste des logements filtrés
     */
    public function searchLogements($search = '', $type = '', $ville = '', $disponibilite = '', $minPrice = '', $maxPrice = '') {
        $sql = "SELECT * FROM logement WHERE 1=1";
        $params = [];

        // Recherche par terme global (titre ou description)
        if (!empty($search)) {
            $sql .= " AND (titre LIKE :search OR description LIKE :search OR ville LIKE :search OR type LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        // Filtre par type de logement
        if (!empty($type)) {
            $sql .= " AND type = :type";
            $params[':type'] = $type;
        }

        // Filtre par ville
        if (!empty($ville)) {
            $sql .= " AND ville = :ville";
            $params[':ville'] = $ville;
        }

        // Filtre par disponibilité
        if ($disponibilite !== '') {
            $sql .= " AND disponibilite = :disponibilite";
            $params[':disponibilite'] = $disponibilite;
        }

        // Filtre par prix minimum
        if (!empty($minPrice)) {
            $sql .= " AND prix_par_nuit >= :minPrice";
            $params[':minPrice'] = $minPrice;
        }

        // Filtre par prix maximum
        if (!empty($maxPrice)) {
            $sql .= " AND prix_par_nuit <= :maxPrice";
            $params[':maxPrice'] = $maxPrice;
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
     * Récupère la liste des types de logement distincts
     *
     * @return array Liste des types de logement
     */
    public function getDistinctTypes() {
        $sql = "SELECT DISTINCT type FROM logement ORDER BY type";
        $db = config::getConnexion();
        try {
            $query = $db->query($sql);
            $result = $query->fetchAll(PDO::FETCH_COLUMN, 0);
            return $result;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Récupère la liste des villes distinctes
     *
     * @return array Liste des villes
     */
    public function getDistinctVilles() {
        $sql = "SELECT DISTINCT ville FROM logement ORDER BY ville";
        $db = config::getConnexion();
        try {
            $query = $db->query($sql);
            $result = $query->fetchAll(PDO::FETCH_COLUMN, 0);
            return $result;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Lister les logements disponibles
    public function listAvailableLogements() {
        $sql = "SELECT * FROM logement WHERE disponibilite = 1";
        $db  = config::getConnexion();
        try {
            $stmt = $db->query($sql);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

}
