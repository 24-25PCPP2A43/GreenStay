<?php
include '../../config/database.php'; // Connexion à la base de données

// Vérifier si l'ID de la réclamation est passé en paramètre
if (isset($_GET['id'])) {
    $id_reclamation = $_GET['id'];

    try {
        // Start a transaction to ensure atomicity
        $conn->beginTransaction();

        // First, get the reponse_id associated with the reclamation
        $sql_select_reclamation = "SELECT reponse_id FROM reclamations WHERE id = :id_reclamation";
        $stmt_select_reclamation = $conn->prepare($sql_select_reclamation);
        $stmt_select_reclamation->bindParam(':id_reclamation', $id_reclamation, PDO::PARAM_INT);
        $stmt_select_reclamation->execute();

        $reclamation = $stmt_select_reclamation->fetch(PDO::FETCH_ASSOC);

        // Check if a reponse_id exists for this reclamation
        if ($reclamation && $reclamation['reponse_id'] !== null) {
            $id_reponse = $reclamation['reponse_id'];

            // Then, delete the associated response
            $sql_delete_reponse = "DELETE FROM reponse WHERE id_reponse = :id_reponse";
            $stmt_delete_reponse = $conn->prepare($sql_delete_reponse);
            $stmt_delete_reponse->bindParam(':id_reponse', $id_reponse, PDO::PARAM_INT);
            $stmt_delete_reponse->execute();
        }

        // Then, delete the reclamation
        $sql_delete_reclamation = "DELETE FROM reclamations WHERE id = :id_reclamation";
        $stmt_delete_reclamation = $conn->prepare($sql_delete_reclamation);
        $stmt_delete_reclamation->bindParam(':id_reclamation', $id_reclamation, PDO::PARAM_INT);

        if ($stmt_delete_reclamation->execute()) {
            // Commit the transaction
            $conn->commit();

            // Message de succès et redirection
            echo '
            <div style="background-color:#e0ffe0; border:2px solid green; padding:15px; border-radius:8px; font-size: 20px; color:green; margin: 50px auto; width:fit-content; text-align:center;">
                <img src="../../images/green_checkmark.png" alt="Succès" style="width: 30px; vertical-align: middle; margin-right: 10px;">
                Réclamation supprimée avec succès.
            </div>
            <script>
                setTimeout(function() {
                    window.location.href = "listReclamations.php";
                }, 3000); // Redirection après 3 secondes
            </script>';
        } else {
            // Rollback the transaction if there was an error
            $conn->rollBack();
            echo '
            <div style="background-color:#ffe0e0; border:2px solid red; padding:15px; border-radius:8px; font-size: 20px; color:red; margin: 50px auto; width:fit-content; text-align:center;">
                <img src="../../images/error_icon.png" alt="Erreur" style="width: 30px; vertical-align: middle; margin-right: 10px;">
                Erreur lors de la suppression de la réclamation.
            </div>';
        }
    } catch (PDOException $e) {
        // Rollback the transaction if there was an exception
        $conn->rollBack();
        echo '
        <div style="background-color:#ffe0e0; border:2px solid red; padding:15px; border-radius:8px; font-size: 20px; color:red; margin: 50px auto; width:fit-content; text-align:center;">
            <img src="../../images/error_icon.png" alt="Erreur" style="width: 30px; vertical-align: middle; margin-right: 10px;">
            Erreur lors de la suppression de la réclamation: ' . $e->getMessage() . '
        </div>';
    }
} else {
    echo '
    <div style="background-color:#ffe0e0; border:2px solid red; padding:15px; border-radius:8px; font-size: 20px; color:red; margin: 50px auto; width:fit-content; text-align:center;">
        <img src="../../images/error_icon.png" alt="Erreur" style="width: 30px; vertical-align: middle; margin-right: 10px;">
        ID de réclamation manquant.
    </div>';
    exit;
}
?>