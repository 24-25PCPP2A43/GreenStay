<?php
include '../../config/database.php'; // Connexion à la base de données

// Vérifier si l'ID de la réclamation est passé en paramètre
if (isset($_GET['id'])) {
    $id_reclamation = $_GET['id'];

    // Supprimer la réclamation
    $sql = "DELETE FROM reclamations WHERE id = :id_reclamation";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_reclamation', $id_reclamation, PDO::PARAM_INT);

    if ($stmt->execute()) {
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
        echo '
        <div style="background-color:#ffe0e0; border:2px solid red; padding:15px; border-radius:8px; font-size: 20px; color:red; margin: 50px auto; width:fit-content; text-align:center;">
            <img src="../../images/error_icon.png" alt="Erreur" style="width: 30px; vertical-align: middle; margin-right: 10px;">
            Erreur lors de la suppression de la réclamation.
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
