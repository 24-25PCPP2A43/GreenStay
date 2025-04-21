<?php
include '../../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sujet = $_POST['sujet'];
    $message = $_POST['message'];
    $client_id = $_POST['client_id'];
    $reservation_id = $_POST['reservation_id'];

    $sql = "INSERT INTO reclamations (sujet, message, statut, client_id, reservation_id) 
            VALUES (:sujet, :message, 'Nouveau', :client_id, :reservation_id)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':sujet', $sujet);
    $stmt->bindParam(':message', $message);
    $stmt->bindParam(':client_id', $client_id);
    $stmt->bindParam(':reservation_id', $reservation_id);

    if ($stmt->execute()) {
        header("Location: listReclamations.php?success=1");
        exit();
    } else {
        $error = "Une erreur est survenue. Veuillez réessayer.";
    }
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<!-- 🌄 Ajout du background image personnalisé pour la section -->
<style>
  body {
    background-image: url('/EcoTravel/assets/images/best-03.jpg');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
  }

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

  label {
    color: #333333; 
    font-weight: bold;
  }

  .form-control {
    border-radius: 10px;
  }

  .form-group {
    margin-bottom: 1.5rem;
  }

  .section {
    background-image: url('/EcoTravel/assets/images/best-03.jpg');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    padding-top: 100px;
    padding-bottom: 100px;
  }
</style>

<?php if (!empty($error)): ?>
  <div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<!-- 📝 Formulaire stylisé avec animation -->
<section class="section">
  <div class="container">
    <div class="row justify-content-center wow fadeInUp" data-wow-delay="0.2s">
      <div class="col-lg-8">
        <div class="card shadow-lg border-0">
          <div class="card-body p-5">
            <h2 class="mb-4 text-center">📩 Ajouter une <em>Réclamation</em></h2>

            <form name="reclamationForm" method="POST" action="" onsubmit="return validateForm()">
              <div class="form-group mb-3">
                <label for="sujet">Sujet <span class="text-danger">*</span></label>
                <input type="text" name="sujet" id="sujet" class="form-control" required>
              </div>

              <div class="form-group mb-3">
                <label for="message">Message <span class="text-danger">*</span></label>
                <textarea name="message" id="message" rows="4" class="form-control" required></textarea>
              </div>

              <div class="form-group mb-3">
                <label for="client_id">ID Client <span class="text-danger">*</span></label>
                <input type="text" name="client_id" id="client_id" class="form-control" required>
              </div>

              <div class="form-group mb-4">
                <label for="reservation_id">ID Réservation <span class="text-danger">*</span></label>
                <input type="text" name="reservation_id" id="reservation_id" class="form-control" required>
              </div>

              <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                  ✉️ Envoyer
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

<!-- JavaScript pour la validation -->
<script>
function validateForm() {
    // Récupérer les valeurs des champs
    var sujet = document.forms["reclamationForm"]["sujet"].value;
    var message = document.forms["reclamationForm"]["message"].value;
    var clientId = document.forms["reclamationForm"]["client_id"].value;
    var reservationId = document.forms["reclamationForm"]["reservation_id"].value;

    // Validation du sujet
    if (sujet.trim() == "") {
        alert("Le champ 'Sujet' ne peut pas être vide.");
        return false;
    }

    // Validation du message
    if (message.trim() == "") {
        alert("Le champ 'Message' ne peut pas être vide.");
        return false;
    }

    // Validation de client_id (doit être un nombre)
    if (isNaN(clientId) || clientId.trim() == "") {
        alert("L'ID du client doit être un nombre valide.");
        return false;
    }

    // Validation de reservation_id (doit être un nombre)
    if (isNaN(reservationId) || reservationId.trim() == "") {
        alert("L'ID de la réservation doit être un nombre valide.");
        return false;
    }

    // Si toutes les validations passent
    return true;
}
</script>
