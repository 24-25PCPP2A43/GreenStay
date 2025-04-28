<?php
include '../../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sujet = trim($_POST['sujet']);
    $message = trim($_POST['message']);
    $client_id = trim($_POST['client_id']);
    $reservation_id = trim($_POST['reservation_id']);

    // Validation côté serveur
    if (empty($sujet) || empty($message) || empty($client_id) || empty($reservation_id)) {
        $error = "Tous les champs doivent être remplis.";
    } elseif (!is_numeric($client_id) || !is_numeric($reservation_id)) {
        $error = "L'ID client et l'ID réservation doivent être des nombres valides.";
    } else {
        // Préparer la requête d'insertion
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
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

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

<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
  <div class="alert alert-success">Réclamation ajoutée avec succès !</div>
<?php endif; ?>

<section class="section">
  <div class="container">
    <div class="row justify-content-center wow fadeInUp" data-wow-delay="0.2s">
      <div class="col-lg-8">
        <div class="card shadow-lg border-0">
          <div class="card-body p-5">
            <h2 class="mb-4 text-center">📩 Ajouter une <em>Réclamation</em></h2>

            <form name="reclamationForm" method="POST" action
