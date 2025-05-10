<?php
session_start();

if (isset($_POST['verify_code'])) {
    $input_code = $_POST['verification_code'];

    if ($input_code == $_SESSION['verification_code']) {
        header("Location: reset_password.php");
        exit();
    } else {
        $error = "Code de vérification incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vérification du code</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background-color: #f4f7fc;
        }

        .card {
            border-radius: 1rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
            color: #333;
        }

        .navbar {
            background-color: #1D548DFF;
            color: white;
        }

        .btn-primary {
            background-color: #1D548DFF;
            border-color: #1D548DFF;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container-fluid justify-content-center">
            <span class="navbar-brand mb-0 h1">Vérification du code</span>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card p-4">
                    <h4 class="mb-4 text-center">Entrez le code reçu par SMS</h4>

                    <?php if (isset($error)) : ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="verification_code">Code de vérification</label>
                            <input type="text" name="verification_code" class="form-control" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="verify_code" class="btn btn-primary">Vérifier</button>
                        </div>
                    </form>

                    <div class="mt-3 text-center">
                        <a href="motdepasse_oublie.php">← Retour</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
