<?php
include '../../config/database_reclamations.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if (isset($_GET['id'])) {
    $id_reclamation = $_GET['id'];

    try {
        $conn->beginTransaction();

        // 1. Récupérer la réponse associée (s'il y en a une)
        $stmt = $conn->prepare("SELECT reponse_id FROM reclamations WHERE id = ?");
        $stmt->execute([$id_reclamation]);
        $reclamation = $stmt->fetch(PDO::FETCH_ASSOC);

        // 2. Supprimer la réponse si elle existe
        if ($reclamation && $reclamation['reponse_id']) {
            $stmt = $conn->prepare("DELETE FROM reponse WHERE id_reponse = ?");
            $stmt->execute([$reclamation['reponse_id']]);
        }

        // 3. Supprimer la réclamation
        $stmt = $conn->prepare("DELETE FROM reclamations WHERE id = ?");
        $stmt->execute([$id_reclamation]);

        $conn->commit();
        $response['success'] = true;
        $response['message'] = "Réclamation supprimée avec succès.";

    } catch (PDOException $e) {
        $conn->rollBack();
        $response['message'] = "Erreur lors de la suppression : " . $e->getMessage();
    }
} else {
    $response['message'] = "ID manquant.";
}

echo json_encode($response);
