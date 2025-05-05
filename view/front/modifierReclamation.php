<?php
include '../../config/database.php'; // Connexion à la base de données

// Vérification de l'ID de la réclamation à modifier
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Récupérer la réclamation à modifier
    $sql = "SELECT * FROM reclamations WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $reclamation = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les nouvelles informations de la réclamation
    $sujet = $_POST['sujet'];
    $message = $_POST['message'];

    // Mettre à jour la réclamation dans la base de données
    $sql = "UPDATE reclamations SET sujet = :sujet, message = :message WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':sujet', $sujet);
    $stmt->bindParam(':message', $message);
    $stmt->bindParam(':id', $id);

    if ($stmt->execute()) {
        // Message de confirmation
        $message_success = "Réclamation modifiée avec succès !";
        // Redirection vers la liste après 3 secondes
        //header("refresh:3;url=listReclamations.php");  // Removed refresh header
    } else {
        $message_error = "Erreur lors de la modification de la réclamation.";
    }
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<!-- 🌄 Ajout du background image personnalisé pour la section spécifique -->
<style>
  .card {
    background-color: rgba(255, 255, 255, 0.85); /* Fond semi-transparent pour la carte */
    border-radius: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Légère ombre pour la carte */
  }

  .btn-primary {
    background-color: #2980b9; /* Bleu un peu foncé */
    border-color: #2980b9;
    transition: 0.3s;
  }

  .btn-primary:hover {
    background-color: #1d5984; /* Bleu plus foncé au survol */
    border-color: #1d5984;
  }

  .alert {
    border-radius: 8px;
    font-size: 20px;
    text-align: center;
    margin: 20px auto;
    padding: 15px;
    width: fit-content;
  }

  .alert-success {
    background-color: #e0ffe0;
    border: 2px solid green;
    color: green;
  }

  .alert-error {
    background-color: #ffe0e0;
    border: 2px solid red;
    color: red;
  }

  /* Styles des labels (titres des champs) */
  label {
    color: #333333; /* Couleur foncée pour les labels */
    font-weight: bold;
  }

  .form-control {
    border-radius: 10px;
  }

  .form-group {
    margin-bottom: 1.5rem;
  }

  /* Retirer toute marge et padding autour du formulaire */
  .container {
    padding: 0; /* Enlever tout padding par défaut */
    margin: 0;  /* Enlever toute marge par défaut */
  }

  /* Section contenant le formulaire avec fond personnalisé */
  .section {
    background-image: url('/EcoTravel/assets/images/best-03.jpg'); /* L'image de fond directement sur la section */
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    padding-top: 100px;
    padding-bottom: 100px;
  }
</style>

<?php if (isset($message_success)) { ?>
    <div class="alert alert-success">
        <img src="../../images/green_checkmark.png" alt="Succès" style="width: 30px; vertical-align: middle; margin-right: 10px;">
        <?= $message_success ?>
    </div>
<?php } elseif (isset($message_error)) { ?>
    <div class="alert alert-error">
        <img src="../../images/error_icon.png" alt="Erreur" style="width: 30px; vertical-align: middle; margin-right: 10px;">
        <?= $message_error ?>
    </div>
<?php } ?>

<!-- 📝 Formulaire de modification de réclamation -->
<section class="section">
  <div class="container">
    <div class="row justify-content-center wow fadeInUp" data-wow-delay="0.2s">
      <div class="col-lg-8">
        <div class="card shadow-lg border-0">
          <div class="card-body p-5">
            <h2 class="mb-4 text-center">📝 Modifier une <em>Réclamation</em></h2>

            <form method="POST" action="">
              <div class="form-group mb-3">
                <label for="sujet">Sujet <span class="text-danger">*</span></label>
                <input type="text" name="sujet" class="form-control" value="<?= htmlspecialchars($reclamation['sujet']) ?>" required>
              </div>

              <div class="form-group mb-3">
                <label for="message">Message <span class="text-danger">*</span></label>
                <textarea name="message" rows="4" class="form-control" required><?= htmlspecialchars($reclamation['message']) ?></textarea>
              </div>



              <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                  💾 Sauvegarder les modifications
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
  <?php if (isset($message_success)) { ?>
    setTimeout(function() {
      window.location.href = "listReclamations.php";
    }, 3000); // Redirect after 3 seconds
  <?php } ?>
</script>

<?php include 'includes/footer.php'; ?>