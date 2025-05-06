<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use Twilio\Rest\Client;

$error = "";
session_start();

if (isset($_POST['submit_phone'])) {
    $phone = $_POST['telephone'];

    if (!preg_match("/^[0-9]{8}$/", $phone)) {
        $error = "Numéro de téléphone invalide. Veuillez entrer un numéro valide à 8 chiffres.";
    } else {
        $fullPhone = "+216" . $phone;
        $code = rand(1000, 9999);

        $sid = "AC0729edffd46d22d2629f003d21d10b14";
        $token = "a9d278fb30ccc4bf76ea2b7fea133e48";
        $messagingServiceSid = "MG8cabe2d097db9942c279a3c73473ae32";

        try {
            $client = new Client($sid, $token);
            $message = $client->messages->create(
                $fullPhone,
                [
                    'messagingServiceSid' => $messagingServiceSid,
                    'body' => "Votre code de vérification est : $code"
                ]
            );

            $_SESSION['verification_code'] = $code;
            $_SESSION['phone'] = $phone;

            echo "<script>window.addEventListener('load', function() {
                var modal = new bootstrap.Modal(document.getElementById('verificationModal'));
                modal.show();
            });</script>";
        } catch (Exception $e) {
            $error = "Erreur lors de l'envoi du SMS : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié</title>
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
        <span class="navbar-brand mb-0 h1">Mot de passe oublié</span>
    </div>
</nav>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card p-4">
                <h4 class="mb-4 text-center"><i class="fas fa-sms"></i> Recevoir un code de vérification</h4>

                <?php if (!empty($error)) : ?>
                    <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="telephone" class="form-label">Numéro de téléphone (8 chiffres)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            <input type="text" name="telephone" class="form-control" placeholder="Ex: 12345678" required>
                        </div>
                    </div>

                    <div class="d-grid mt-3">
                        <button type="submit" name="submit_phone" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Envoyer le code
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

<!-- Modal Bootstrap -->
<div class="modal fade" id="verificationModal" tabindex="-1" aria-labelledby="verificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vérification du code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <form action="resetpassword.php" method="POST">
                    <div class="mb-3">
                        <label for="code" class="form-label">Code de vérification</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-shield-alt"></i></span>
                            <input type="text" name="verification_code" class="form-control" required>
                        </div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" name="verify_code" class="btn btn-primary">
                            <i class="fas fa-check-circle"></i> Vérifier
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- ✅ Toast d'erreur -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
    <div id="toastError" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessageError"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fermer"></button>
        </div>
    </div>
</div>

<!-- ✅ Toast de succès -->
<div class="position-fixed bottom-0 start-0 p-3" style="z-index: 9999">
    <div id="toastSuccess" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">✅ Code envoyé avec succès</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fermer"></button>
        </div>
    </div>
</div>

<!-- ✅ Scripts Bootstrap + Animation -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const form = document.querySelector("form");
    const submitBtn = document.querySelector("button[name='submit_phone']");
    const spinner = document.createElement("span");

    spinner.className = "spinner-border spinner-border-sm me-2";
    spinner.role = "status";
    spinner.ariaHidden = true;

    form?.addEventListener("submit", () => {
        if (!submitBtn.querySelector(".spinner-border")) {
            submitBtn.prepend(spinner);
        }
    });

    // ✅ Toast erreur si erreur PHP
    <?php if (!empty($error)) : ?>
        window.addEventListener('load', () => {
            document.getElementById("toastMessageError").textContent = <?= json_encode($error) ?>;
            const toast = new bootstrap.Toast(document.getElementById("toastError"));
            toast.show();
        });
    <?php endif; ?>

    // ✅ Toast succès si modal s'ouvre (donc SMS envoyé)
    <?php if (strpos(ob_get_contents(), 'modal.show();') !== false) : ?>
        window.addEventListener('load', () => {
            const toast = new bootstrap.Toast(document.getElementById("toastSuccess"));
            toast.show();
        });
    <?php endif; ?>
</script>

</body>
</html>
