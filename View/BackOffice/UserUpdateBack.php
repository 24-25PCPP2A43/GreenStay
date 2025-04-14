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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
          integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>

    <title>Users Dashboard</title>
</head>

<body style="background-color: #1D548DFF;">
<nav class="navbar navbar-light justify-content-center fs-3 mb-5"
     style="background-color:rgb(38, 145, 152); color: #1D548DFF;">
    Users Dashboard
</nav>

<div class="container">
    <div class="text-center mb-4">
        <h3 style="color:rgb(90, 251, 216);">Modifier les coordonné d'utilisateur</h3>
    </div>


    <?php
    $conn = Database::connect();

    $sql = "SELECT * FROM `utilisateurs` WHERE id = :id LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>


<div class="container d-flex justify-content-center">
    <form action="" method="post" style="width:50vw; min-width:300px;">
        <div class="mb-3">
            <label class="form-label" style="color: rgb(90, 251, 216);">Nom:</label>
            <input type="text" class="form-control" name="nom" 
                   value="<?= htmlspecialchars($_POST['nom'] ?? $row['nom'] ?? '') ?>">
            <div class="text-danger"><?= $nomErr ?></div>
        </div>

        <div class="mb-3">
            <label class="form-label" style="color: rgb(90, 251, 216);">Prénom:</label>
            <input type="text" class="form-control" name="prenom" 
                   value="<?= htmlspecialchars($_POST['prenom'] ?? $row['prenom'] ?? '') ?>">
            <div class="text-danger"><?= $prenomErr ?></div>
        </div>

        <div class="mb-3">
            <label class="form-label" style="color: rgb(90, 251, 216);">Email:</label>
            <input type="email" class="form-control" name="email" 
                   value="<?= htmlspecialchars($_POST['email'] ?? $row['email'] ?? '') ?>">
            <div class="text-danger">
                <?= $emailErr ?>
                <?= $emailExistErr ?>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label" style="color: rgb(90, 251, 216);">Mot de passe:</label>
            <input type="password" class="form-control" name="password" 
                   value="<?= htmlspecialchars($_POST['password'] ?? '') ?>">
            <div class="text-danger"><?= $passwordErr ?></div>
        </div>

        <div class="mb-3">
            <label class="form-label" style="color: rgb(90, 251, 216);">Téléphone:</label>
            <input type="text" class="form-control" name="telephone" 
                   value="<?= htmlspecialchars($_POST['telephone'] ?? $row['telephone'] ?? '') ?>">
            <div class="text-danger"><?= $telephoneErr ?></div>
        </div>

        <div class="mb-3">
            <label style="color:rgb(90, 251, 216);">Role:</label>
            <div class="text-danger"><?= $roleErr ?></div>
            <div>
                <input type="radio" class="form-check-input" name="role" id="admin" value="Admin"
                    <?= (isset($_POST['role']) && $_POST['role'] === 'Admin') ? 'checked' : (($row['role'] ?? '') === 'Admin' ? 'checked' : '') ?>>
                <label for="admin" class="form-input-label" style="color: rgb(90, 251, 216);">Admin</label>
                
                <input type="radio" class="form-check-input" name="role" id="client" value="Client"
                    <?= (isset($_POST['role']) && $_POST['role'] === 'Client') ? 'checked' : (($row['role'] ?? '') === 'Client' ? 'checked' : '') ?>>
                <label for="client" class="form-input-label" style="color:rgb(90, 251, 216);">Client</label>
            </div>
        </div>

        <div>
            <button type="submit" class="btn btn-success" name="submit">Mise a jour</button>
            <a href="userdash.php" class="btn btn-danger">Annuler</a>
        </div>
    </form>
</div>

</div>


</body>

</html>