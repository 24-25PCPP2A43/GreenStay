<?php
require_once __DIR__ . '/../../Controller/ReponseController.php';
require_once __DIR__ . '/../../Model/Reponse.php';

// Création de l'instance du contrôleur
$reponseController = new ReponseController();

// Vérification si l'ID de la réponse est passé dans l'URL
if (isset($_GET['id_reponse'])) {
    $id_reponse = intval($_GET['id_reponse']);
    $reponseData = $reponseController->afficherReponseParId($id_reponse);

    if (!$reponseData) {
        echo "Réponse introuvable.";
        exit();
    }
} else {
    echo "ID réponse manquant.";
    exit();
}

// Traitement de la modification après soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_reponse'], $_POST['id_reclamation'], $_POST['message'], $_POST['date_reponse'])) {
        $id_reponse = intval($_POST['id_reponse']);
        $id_reclamation = intval($_POST['id_reclamation']);
        $message = trim($_POST['message']);
        $date_reponse = $_POST['date_reponse'];

        $reponse = new Reponse($id_reclamation, $message, $date_reponse, $id_reponse);
        $reponseController->modifierReponse($reponse);

        header('Location: consulter_reponses.php?success=true');
        exit();
    }
}
?>

<?php include('includes/header.php'); ?>

<link rel="stylesheet" href="../../assets/css/dashboard.css">

<div class="container-fluid mt-5 mb-5" style="background-image: url('../../assets/images/best-03.jpg'); background-size: cover; background-position: center; padding: 0;">
    <div class="container p-4 shadow rounded" style="background-color: rgba(255, 255, 255, 0.9);">
        <!-- Titre modifié en noir -->
        <h2 class="text-center mb-4" style="color: black;">Modifier une Réponse</h2>

        <form method="POST" action="">
            <input type="hidden" name="id_reponse" value="<?= htmlspecialchars($reponseData['id_reponse']) ?>">

            <div class="mb-3">
                <label for="id_reclamation" class="form-label">ID Réclamation :</label>
                <input type="number" class="form-control" id="id_reclamation" name="id_reclamation" value="<?= htmlspecialchars($reponseData['id_reclamation']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="message" class="form-label">Message :</label>
                <textarea class="form-control" id="message" name="message" rows="5" required><?= htmlspecialchars($reponseData['message']) ?></textarea>
            </div>

            <div class="mb-3">
                <label for="date_reponse" class="form-label">Date Réponse :</label>
                <input type="date" class="form-control" id="date_reponse" name="date_reponse" value="<?= htmlspecialchars($reponseData['date_reponse']) ?>" required>
            </div>

            <div class="text-center">
                <input type="submit" class="btn btn-marine" value="Mettre à jour">
            </div>
        </form>

        <!-- Bouton de retour -->
        <div class="text-center mt-4">
            <a href="consulter_reclamations.php" class="btn btn-secondary">Retour aux Réclamations</a>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
