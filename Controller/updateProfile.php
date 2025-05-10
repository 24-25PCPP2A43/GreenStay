<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

// Vérification de la connexion de l'utilisateur
if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Non connecté']);
    exit();
}

$userData = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];

    // Récupération et validation des champs
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validation du nom
    if (empty($nom)) {
        $errors['nom'] = "Le nom est obligatoire";
    } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\-]{2,50}$/', $nom)) {
        $errors['nom'] = "Le nom doit contenir entre 2 et 50 caractères alphabétiques";
    }

    // Validation du prénom
    if (empty($prenom)) {
        $errors['prenom'] = "Le prénom est obligatoire";
    } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s\-]{2,50}$/', $prenom)) {
        $errors['prenom'] = "Le prénom doit contenir entre 2 et 50 caractères alphabétiques";
    }

    // Validation de l'email
    if (empty($email)) {
        $errors['email'] = "L'email est obligatoire";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Format d'email invalide";
    }

    // Validation du téléphone
    if (empty($telephone)) {
        $errors['telephone'] = "Le téléphone est obligatoire";
    } elseif (!preg_match('/^[0-9]{8,15}$/', $telephone)) {
        $errors['telephone'] = "Le téléphone doit contenir entre 8 et 15 chiffres";
    }

    // Validation du mot de passe (si fourni)
    if (!empty($password) && strlen($password) < 8) {
        $errors['password'] = "Le mot de passe doit contenir au moins 8 caractères";
    }

   // Si des erreurs sont trouvées, retournez-les en JSON
if (!empty($errors)) {
    echo json_encode([
        'success' => false, 
        'message' => 'Des erreurs de validation sont survenues',
        'errors' => $errors
    ]);
    exit();
}


    try {
        // Connexion à la base de données
        $db = new PDO('mysql:host=localhost;dbname=ecotech', 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Vérification de l'unicité de l'email
        $stmt = $db->prepare("SELECT id FROM utilisateurs WHERE email = ? AND id != ?");
        $stmt->execute([$email, $userData['id']]);
        if ($stmt->fetch()) {
            echo json_encode([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la mise à jour',
                'errors' => ['email' => 'Cet email est déjà utilisé']
            ]);
            exit();
        }

        // Mise à jour des données de l'utilisateur
        $sql = "UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, telephone = ? WHERE id = ?";
        $params = [$nom, $prenom, $email, $telephone, $userData['id']];

        if (!empty($password)) {
            $sql .= ", password = ?";
            $params[] = password_hash($password, PASSWORD_DEFAULT);
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);

        // Mise à jour de la session utilisateur avec les nouvelles données
        $_SESSION['user']['nom'] = $nom;
        $_SESSION['user']['prenom'] = $prenom;
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['telephone'] = $telephone;

        // Retour de la réponse JSON avec succès
        echo json_encode([
            'success' => true,
            'message' => 'Profil mis à jour avec succès',
            'user' => [
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'telephone' => $telephone
            ]
        ]);

    } catch (PDOException $e) {
        // Gestion des erreurs de base de données
        echo json_encode([
            'success' => false,
            'message' => 'Une erreur est survenue lors de la mise à jour',
            'errors' => ['global' => 'Erreur de base de données']
        ]);
    }
}
?>
