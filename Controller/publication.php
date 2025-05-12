<?php
require_once __DIR__ . '/../Model/publicationClass.php';
require_once __DIR__ . '/../Model/Config.php';

class publication {

    // Ajouter une publication
    public function addPublication($publication) {
        $sql = "INSERT INTO publication (titre, contenu, image, statut, date, id_categorie)
                VALUES (:titre, :contenu, :image, :statut, :date, :id_categorie)";
        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute([
                'titre' => $publication->getTitre(),
                'contenu' => $publication->getContenu(),
                'image' => $publication->getImage(),
                'statut' => $publication->getStatut(),
                'date' => $publication->getDate(),
                'id_categorie' => $publication->getIdCategorie() // Ajout de l'id_categorie
            ]);
        } catch (PDOException $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    // Liste de toutes les publications

    public function listPublications() {
        $sql = "SELECT p.*, c.nom_categorie 
                FROM publication p
                LEFT JOIN categorie c ON p.id_categorie = c.id_categorie";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
    // Supprimer une publication
    public function deletePublication($id_publication) {
        $sql = "DELETE FROM publication WHERE id_publication = :id_publication";
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id_publication', $id_publication, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Modifier une publication
    public function updatePublication($id_publication, $titre, $contenu, $image, $statut, $date, $id_categorie) {
        $sql = "UPDATE publication 
                SET titre = :titre, contenu = :contenu, image = :image, statut = :statut, date = :date, id_categorie = :id_categorie 
                WHERE id_publication = :id_publication";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindParam(':id_publication', $id_publication);
            $query->bindParam(':titre', $titre);
            $query->bindParam(':contenu', $contenu);
            $query->bindParam(':image', $image);
            $query->bindParam(':statut', $statut);
            $query->bindParam(':date', $date);
            $query->bindParam(':id_categorie', $id_categorie); // Ajout de l'id_categorie
            $query->execute();
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }










    // Récupérer une seule publication par son ID
    public function showPublication($id_publication) {
        $sql = "SELECT * FROM publication WHERE id_publication = :id_publication";
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_publication', $id_publication, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
}
?>
