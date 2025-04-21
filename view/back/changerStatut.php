<?php
include '../../config/database.php'; // Inclure la connexion à la base de données

// Vérifier si l'ID de la réclamation est passé dans l'URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Récupérer les informations de la réclamation
    $sql = "SELECT * FROM reclamations WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $reclamation = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier si la réclamation existe
    if (!$reclamation) {
        echo "Réclamation non trouvée.";
        exit;
    }

    // Vérifier si le formulaire est soumis
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nouveauStatut = $_POST['statut']; // Récupérer le statut sélectionné

        // Requête SQL pour mettre à jour le statut
        $sql = "UPDATE reclamations SET statut = :statut WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':statut', $nouveauStatut, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        // Exécuter la mise à jour
        if ($stmt->execute()) {
            echo "Statut mis à jour avec succès.";
            header('Location: consulter_reclamations.php'); // Redirige vers la page des réclamations
            exit();
        } else {
            echo "Erreur lors de la mise à jour du statut.";
        }
    }
} else {
    echo "Aucun ID de réclamation trouvé.";
    exit;
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<!-- 🌄 Ajout du background image personnalisé -->
<style>
  body {
    background-image: url('/EcoTravel/assets/images/best-03.jpg'); /* L'image de fond */
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
  }

  .card {
    background-color: rgba(255, 255, 255, 0.95); /* fond translucide pour la carte */
    border-radius: 20px;
  }

  /* Styles pour le bouton "Envoyer" */
  .btn-primary {
    background-color: #2980b9; /* Bleu légèrement foncé */
    border-color: #2980b9;
    transition: 0.3s;
  }

  .btn-primary:hover {
    background-color: #1d5984; /* Bleu foncé au survol */
    border-color: #1d5984;
  }

  footer {
    background-color: #f0f0f0; /* Gris clair */
    text-align: center;
    padding: 10px 0;
    font-size: 14px;
    color: #333;
    margin-top: 50px; /* Décaler le footer plus bas */
  }
</style>

<!-- 📝 Formulaire de changement de statut -->
<section class="section" style="margin-top: 100px;">
  <div class="container">
    <div class="row justify-content-center wow fadeInUp" data-wow-delay="0.2s">
      <div class="col-lg-8">
        <div class="card shadow-lg border-0">
          <div class="card-body p-5">
            <h2 class="mb-4 text-center">Changer le statut de la réclamation #<?= $reclamation['id'] ?></h2>

            <!-- Affichage d'erreur éventuelle -->
            <?php if (!empty($error)): ?>
              <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <!-- Formulaire pour changer le statut -->
            <form method="POST" action="">
              <div class="form-group mb-3">
                <label for="statut">Statut :</label>
                <select name="statut" id="statut" class="form-control" required>
                  <option value="Nouveau" <?= $reclamation['statut'] == 'Nouveau' ? 'selected' : '' ?>>Nouveau</option>
                  <option value="En cours de traitement" <?= $reclamation['statut'] == 'En cours de traitement' ? 'selected' : '' ?>>En cours de traitement</option>
                  <option value="Traitée" <?= $reclamation['statut'] == 'Traitée' ? 'selected' : '' ?>>Traitée</option>
                </select>
              </div>

              <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                  💼 Changer le statut
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
