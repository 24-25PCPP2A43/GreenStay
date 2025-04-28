<?php
include('../../config/database.php');

// Vérifier si l'ID de la réclamation est passé et est valide
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: consulter_reclamations.php");  // Redirige vers la liste des réclamations
    exit;
}

$id_reclamation = $_GET['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer le message de réponse
    $message = trim($_POST['message']);
    $statut = $_POST['statut'];  // Nouveau statut à mettre à jour

    // Vérifier si le message est vide
    if (empty($message)) {
        $error = "Le message de réponse ne peut pas être vide.";
    } else {
        try {
            // Insérer la réponse dans la table 'reponse'
            $sql = "INSERT INTO reponse (id_reclamation, message, date_reponse) 
                    VALUES (:id_reclamation, :message, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_reclamation', $id_reclamation);
            $stmt->bindParam(':message', $message);
            $stmt->execute();

            // Mettre à jour le statut dans la table 'reclamations'
            $sqlUpdate = "UPDATE reclamations SET statut = :statut WHERE id = :id_reclamation";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bindParam(':id_reclamation', $id_reclamation);
            $stmtUpdate->bindParam(':statut', $statut);
            $stmtUpdate->execute();

            // Vérification si la mise à jour a bien été effectuée
            if ($stmtUpdate->rowCount() > 0) {
                // Si la mise à jour a été effectuée, rediriger vers la page de réclamations
                header("Location: consulter_reclamations.php");
                exit;
            } else {
                // Si la mise à jour n'a pas été effectuée
                $error = "Le statut de la réclamation n'a pas pu être mis à jour.";
            }

        } catch (PDOException $e) {
            $error = "Erreur : " . $e->getMessage();
        }
    }
}
?>

<?php include('includes/header.php'); ?>

<!-- Formulaire de réponse -->
<div class="container mt-5">
    <h2>Répondre à la réclamation #<?= htmlspecialchars($id_reclamation) ?></h2>
    <form action="ajouterReponse.php?id=<?= $id_reclamation ?>" method="POST" onsubmit="return validateForm()">
        <div class="form-group">
            <label for="message">Votre réponse</label>
            <textarea class="form-control" name="message" rows="5"></textarea>
        </div>
        <div class="form-group">
            <label for="statut">Changer le statut</label>
            <select class="form-control" name="statut">
                <option value="Nouveau">Nouveau</option>
                <option value="En cours de traitement">En cours de traitement</option>
                <option value="Traité">Traité</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Envoyer la réponse</button>
    </form>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger mt-3"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
</div>

<?php include('includes/footer.php'); ?>

<script>
    // Fonction de validation en JS pour le formulaire
    function validateForm() {
        // Récupérer les valeurs des champs
        var message = document.querySelector('textarea[name="message"]').value.trim();
        var statut = document.querySelector('select[name="statut"]').value;

        // Vérifier que le message n'est pas vide
        if (message === '') {
            alert('Le message de réponse ne peut pas être vide.');
            return false; // Empêche l'envoi du formulaire
        }

        // Vérifier que le statut est sélectionné
        if (statut === '') {
            alert('Veuillez sélectionner un statut.');
            return false; // Empêche l'envoi du formulaire
        }

        // Si tout est valide, envoyer le formulaire
        return true;
    }
</script>
