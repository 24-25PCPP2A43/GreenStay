<?php
require_once __DIR__ . '/../Model/Categorie.php';
require_once __DIR__ . '/../Model/Config.php';

class CategorieController {

    // Ajouter une catégorie
    public function addCategorie($categorie) {
        $sql = "INSERT INTO categorie (nom_categorie, desc_categorie)
                VALUES (:nom_categorie, :desc_categorie)";
        $db = config::getConnexion();

        try {
            $query = $db->prepare($sql);
            $query->execute([
                'nom_categorie' => $categorie->getNomCategorie(),
                'desc_categorie' => $categorie->getDescCategorie()
            ]);
        } catch (PDOException $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    // Liste des catégories
    public function listCategories() {
        $sql = "SELECT * FROM categorie";
        $db = config::getConnexion();
        try {
            $list = $db->query($sql);
            return $list;
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Supprimer une catégorie
    public function deleteCategorie($id_categorie) {
        $sql = "DELETE FROM categorie WHERE id_categorie = :id_categorie";
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id_categorie', $id_categorie, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }

    // Modifier une catégorie
    public function updateCategorie($id_categorie, $nom_categorie, $desc_categorie) {
        $sql = "UPDATE categorie 
                SET nom_categorie = :nom_categorie, desc_categorie = :desc_categorie 
                WHERE id_categorie = :id_categorie";
        $db = config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->bindParam(':id_categorie', $id_categorie);
            $query->bindParam(':nom_categorie', $nom_categorie);
            $query->bindParam(':desc_categorie', $desc_categorie);
            $query->execute();
        } catch (Exception $e) {
            echo 'Erreur: ' . $e->getMessage();
        }
    }

    // Récupérer une seule catégorie par ID
    public function showCategorie($id_categorie) {
        $sql = "SELECT * FROM categorie WHERE id_categorie = :id_categorie";
        $db = config::getConnexion();
        try {
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id_categorie', $id_categorie, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            die('Erreur: ' . $e->getMessage());
        }
    }
}
?>
