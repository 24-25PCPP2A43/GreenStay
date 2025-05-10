<?php
require_once __DIR__ . '/../../Config/database.php';
$conn = Database::connect();
$id = $_GET["id"];

// Initialisation des variables d'erreur
$nomErr = $prenomErr = $emailErr = $passwordErr = $telephoneErr = $roleErr = $emailExistErr = "";
$validForm = true;

if (isset($_POST["submit"])) {
    $nom= $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $telephone = $_POST['telephone'];
    $role = $_POST['role'];

     // Validation des champs
     $validForm = true;

     // Validation du nom
     if (empty($nom)) {
         $nomErr = "Le nom est requis";
         $validForm = false;
     } elseif (!preg_match("/^[a-zA-Z-' ]*$/", $nom)) {
         $nomErr = "Caractères autorisés : lettres, espaces et apostrophes";
         $validForm = false;
     }
 
     // Validation du prénom
     if (empty($prenom)) {
         $prenomErr = "Le prénom est requis";
         $validForm = false;
     } elseif (!preg_match("/^[a-zA-Z-' ]*$/", $prenom)) {
         $prenomErr = "Caractères autorisés : lettres, espaces et apostrophes";
         $validForm = false;
     }
 
     // Validation de l'email
     if (empty($email)) {
         $emailErr = "L'email est requis";
         $validForm = false;
     } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
         $emailErr = "Format d'email invalide";
         $validForm = false;
     }
 
     // Validation du mot de passe
     if (empty($password)) {
         $passwordErr = "Le mot de passe est requis";
         $validForm = false;
     } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/', $password)) {
         $passwordErr = "Doit contenir : 8 caractères, 1 majuscule, 1 minuscule, 1 chiffre";
         $validForm = false;
     }
 
     // Validation du téléphone
     if (empty($telephone)) {
         $telephoneErr = "Le numéro de téléphone est requis";
         $validForm = false;
     } elseif (!preg_match('/^\+?[0-9]{8,15}$/', $telephone)) {
         $telephoneErr = "Format invalide (8-15 chiffres)";
         $validForm = false;
     }
 
     // Validation du rôle
     if (empty($role)) {
         $roleErr = "Le rôle est requis";
         $validForm = false;
     } elseif (!in_array($role, ['Admin', 'Client'])) {
         $roleErr = "Rôle non valide";
         $validForm = false;
     }
 
     // Vérification de l'unicité de l'email
     if ($validForm) {
         try {
             $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE email = :email AND id != :id");
             $stmt->execute([':email' => $email, ':id' => $id]);
             
             if ($stmt->rowCount() > 0) {
                 $emailExistErr = "Cet email est déjà utilisé par un autre utilisateur";
                 $validForm = false;
             }
         } catch (PDOException $e) {
             die("Erreur de vérification : " . $e->getMessage());
         }
     }
 
     if ($validForm) {
         try {
             // Hashage du mot de passe
             $hashed_password = password_hash($password, PASSWORD_DEFAULT);
 
             $stmt = $conn->prepare("UPDATE utilisateurs SET 
                 nom = :nom,
                 prenom = :prenom,
                 email = :email,
                 password = :password,
                 telephone = :telephone,
                 role = :role 
                 WHERE id = :id");
 
             $stmt->execute([
                 ':nom' => $nom,
                 ':prenom' => $prenom,
                 ':email' => $email,
                 ':password' => $hashed_password,
                 ':telephone' => $telephone,
                 ':role' => $role,
                 ':id' => $id
             ]);
 
             header("Location: userdash.php?success=1");
             exit();
         } catch (PDOException $e) {
             die("Erreur de mise à jour : " . $e->getMessage());
         }
     }
 }
 
 // Récupération des données existantes
 $sql = "SELECT * FROM utilisateurs WHERE id = :id LIMIT 1";
 $stmt = $conn->prepare($sql);
 $stmt->execute([':id' => $id]);
 $row = $stmt->fetch(PDO::FETCH_ASSOC);
 ?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Modifier utilisateur</title>

    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Style personnalisé -->
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
        <span class="navbar-brand mb-0 h1">Modifier l'utilisateur</span>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card p-4">
                <h4 class="mb-4 text-center">Modifier les coordonnées d'utilisateur</h4>

                <!-- Ton formulaire de modification ici -->
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="nom">Nom</label>
                        <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($row['nom']) ?>" required>
                        <?php if (!empty($nomErr)) : ?><small class="text-danger"><?= htmlspecialchars($nomErr) ?></small><?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="prenom">Prénom</label>
                        <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($row['prenom']) ?>" required>
                        <?php if (!empty($prenomErr)) : ?><small class="text-danger"><?= htmlspecialchars($prenomErr) ?></small><?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($row['email']) ?>" required>
                        <?php if (!empty($emailErr) || !empty($emailExistErr)) : ?><small class="text-danger"><?= htmlspecialchars($emailErr ?: $emailExistErr) ?></small><?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="password">Mot de passe</label>
                        <input type="password" name="password" class="form-control" placeholder="Nouveau mot de passe" required>
                        <?php if (!empty($passwordErr)) : ?><small class="text-danger"><?= htmlspecialchars($passwordErr) ?></small><?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="telephone">Téléphone</label>
                        <input type="text" name="telephone" class="form-control" value="<?= htmlspecialchars($row['telephone']) ?>" required>
                        <?php if (!empty($telephoneErr)) : ?><small class="text-danger"><?= htmlspecialchars($telephoneErr) ?></small><?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="role">Rôle</label>
                        <select name="role" class="form-control" required>
                            <option value="">Sélectionner un rôle</option>
                            <option value="Admin" <?= $row['role'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="Client" <?= $row['role'] === 'Client' ? 'selected' : '' ?>>Client</option>
                        </select>
                        <?php if (!empty($roleErr)) : ?><small class="text-danger"><?= htmlspecialchars($roleErr) ?></small><?php endif; ?>
                    </div>

                    <div class="d-grid">
                        <button type="submit" name="submit" class="btn btn-primary">Mettre à jour</button>
                    </div>
                </form>

                <div class="mt-3 text-center">
                    <a href="userdash.php">← Retour au dashboard</a>
                </div>

            </div>
        </div>
    </div>
</div>

</body>
</html>
