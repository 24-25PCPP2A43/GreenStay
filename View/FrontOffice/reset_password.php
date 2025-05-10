<?php
session_start();

if (isset($_POST['reset_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password === $confirm_password) {
        $phone = $_SESSION['phone'];

        try {
            $conn = new PDO('mysql:host=localhost;dbname=ecotech', 'root', '');
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("UPDATE utilisateurs SET password = :password WHERE telephone = :telephone");
            $stmt->bindParam(':password', $new_password);
            $stmt->bindParam(':telephone', $phone);
            $stmt->execute();

            unset($_SESSION['verification_code']);
            unset($_SESSION['phone']);
            header("Location: inscription.php");
            exit();
        } catch (PDOException $e) {
            $error = "Erreur de connexion à la base de données : " . $e->getMessage();
        }
    } else {
        $error = "Les mots de passe ne correspondent pas.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réinitialiser le mot de passe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: linear-gradient(to right, #dceefc, #f1f8ff);
            min-height: 100vh;
        }
        .card {
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .navbar {
            background-color: #1D548DFF;
        }
        .btn-primary {
            background-color: #1D548DFF;
            border-color: #1D548DFF;
        }
        .input-group-text {
            background-color: #f0f0f0;
        }
        a {
            color: #1D548DFF;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid justify-content-center">
        <span class="navbar-brand mb-0 h1">Réinitialisation du mot de passe</span>
    </div>
</nav>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card p-4">
                <h4 class="mb-4 text-center"><i class="fas fa-key"></i> Réinitialiser votre mot de passe</h4>

                <?php if (isset($error)) : ?>
                    <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nouveau mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" name="new_password" class="form-control" required />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" name="confirm_password" class="form-control" required />
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" name="reset_password" class="btn btn-primary">
                            <i class="fas fa-redo-alt"></i> Réinitialiser
                        </button>
                    </div>
                </form>

                <div class="mt-3 text-center">
                    <a href="inscription.php"><i class="fas fa-arrow-left"></i> Retour à la connexion</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ✅ Toast Bootstrap -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
    <div id="toastError" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">
                <!-- Message dynamique injecté ici -->
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fermer"></button>
        </div>
    </div>
</div>

<!-- ✅ Spinner & Toast JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const form = document.querySelector("form");
    const spinnerIcon = document.createElement("span");
    spinnerIcon.className = "spinner-border spinner-border-sm me-2";
    spinnerIcon.role = "status";
    spinnerIcon.ariaHidden = "true";

    const submitBtn = document.querySelector("button[name='reset_password']");
    const toastEl = document.getElementById("toastError");
    const toastMessage = document.getElementById("toastMessage");

    form?.addEventListener("submit", () => {
        // Ajoute le spinner dans le bouton
        if (!submitBtn.querySelector(".spinner-border")) {
            submitBtn.prepend(spinnerIcon);
        }
    });

    // Si un message d’erreur est présent côté PHP, l'afficher dans le toast
    <?php if (isset($error)) : ?>
        window.addEventListener('load', () => {
            toastMessage.textContent = <?= json_encode($error) ?>;
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        });
    <?php endif; ?>
</script>

</body>
</html>
