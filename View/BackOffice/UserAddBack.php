<?php
global $row;
require_once __DIR__ . '/../../Config/database.php';

// Initialisation des variables d'erreur
$nomErr = $prenomErr = $emailErr = $passwordErr = $telephoneErr = $roleErr = "";
$emailExistErr = "";
$validForm = true;

if (isset($_POST["submit"])) {
    // Récupération des données
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $role = $_POST['role'] ?? '';

    // Validation
    $validForm = true;

    if (empty($nom)) {
        $nomErr = "Le nom est requis";
        $validForm = false;
    } elseif (!preg_match("/^[a-zA-Z-' ]*$/", $nom)) {
        $nomErr = "Caractères autorisés : lettres, espaces et apostrophes";
        $validForm = false;
    }

    if (empty($prenom)) {
        $prenomErr = "Le prénom est requis";
        $validForm = false;
    } elseif (!preg_match("/^[a-zA-Z-' ]*$/", $prenom)) {
        $prenomErr = "Caractères autorisés : lettres, espaces et apostrophes";
        $validForm = false;
    }

    if (empty($email)) {
        $emailErr = "L'email est requis";
        $validForm = false;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Format d'email invalide";
        $validForm = false;
    }

    if (empty($password)) {
        $passwordErr = "Le mot de passe est requis";
        $validForm = false;
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/', $password)) {
        $passwordErr = "8 caractères min., 1 majuscule, 1 minuscule, 1 chiffre";
        $validForm = false;
    }

    if (empty($telephone)) {
        $telephoneErr = "Le numéro de téléphone est requis";
        $validForm = false;
    } elseif (!preg_match('/^\+?[0-9]{8,15}$/', $telephone)) {
        $telephoneErr = "Format invalide (8-15 chiffres)";
        $validForm = false;
    }

    if (empty($role)) {
        $roleErr = "Le rôle est requis";
        $validForm = false;
    } elseif (!in_array($role, ['Admin', 'Client'])) {
        $roleErr = "Rôle non valide";
        $validForm = false;
    }

    if ($validForm) {
        try {
            $conn = Database::connect();
            $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $emailExistErr = "Cet email est déjà utilisé";
                $validForm = false;
            }
        } catch (PDOException $e) {
            echo "Erreur de vérification : " . $e->getMessage();
            $validForm = false;
        }
    }

    if ($validForm) {
        try {
            $conn = Database::connect();
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO utilisateurs 
                (nom, prenom, email, password, telephone, role) 
                VALUES (:nom, :prenom, :email, :password, :telephone, :role)");

            $stmt->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':email' => $email,
                ':password' => $hashed_password,
                ':telephone' => $telephone,
                ':role' => $role
            ]);

            header("Location: userdash.php?success=1");
            exit();

        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Ajouter un utilisateur</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles personnalisés -->
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

        .btn-success {
            background-color: #1D548DFF;
            border-color: #1D548DFF;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container-fluid justify-content-center">
            <span class="navbar-brand mb-0 h1">Dashboard - Ajouter un utilisateur</span>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card p-4">
                    <h4 class="mb-4 text-center">Nouveau utilisateur</h4>

                    <form method="post">

                        <div class="mb-3">
                            <label>Nom</label>
                            <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                            <div class="text-danger"><?= $nomErr ?></div>
                        </div>

                        <div class="mb-3">
                            <label>Prénom</label>
                            <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
                            <div class="text-danger"><?= $prenomErr ?></div>
                        </div>

                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            <div class="text-danger"><?= $emailErr ?> <?= $emailExistErr ?></div>
                        </div>

                        <div class="mb-3">
                            <label>Mot de passe</label>
                            <input type="password" name="password" class="form-control">
                            <div class="text-danger"><?= $passwordErr ?></div>
                        </div>

                        <div class="mb-3">
                            <label>Téléphone</label>
                            <input type="text" name="telephone" class="form-control" value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>">
                            <div class="text-danger"><?= $telephoneErr ?></div>
                        </div>

                        <div class="mb-3">
                            <label>Rôle</label>
                            <div>
                                <input type="radio" name="role" value="Admin" class="form-check-input"
                                    <?= (isset($_POST['role']) && $_POST['role'] === 'Admin') ? 'checked' : '' ?>>
                                <label class="form-check-label me-3">Admin</label>

                                <input type="radio" name="role" value="Client" class="form-check-input"
                                    <?= (isset($_POST['role']) && $_POST['role'] === 'Client') ? 'checked' : '' ?>>
                                <label class="form-check-label">Client</label>
                            </div>
                            <div class="text-danger"><?= $roleErr ?></div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" name="submit" class="btn btn-success">Enregistrer</button>
                            <a href="userdash.php" class="btn btn-secondary">Annuler</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
