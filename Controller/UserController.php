<?php
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Model/User.php';
$nomErr = "";
$prenomErr = "";
$emailErr = "";
$passwordErr = "";
$telephoneErr = "";
$validForm = true;

class UserController
{
    function testAndSave()
    {
        $GLOBALS['validForm'] = true;
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (empty($_POST["nom"])) {
                $GLOBALS['nomErr'] = "Nom est requis";
                $GLOBALS['validForm'] = false;
            } else {
                $nom = htmlspecialchars($_POST["nom"]);
                if (!preg_match("/^[a-zA-Z-' ]*$/", $nom)) {
                    $GLOBALS['nomErr'] = "Only letters and white space allowed";
                    $GLOBALS['validForm'] = false;
                }
            }

            if (empty($_POST["prenom"])) {
                $GLOBALS['prenomErr'] = "Prenom est requis";
                $GLOBALS['validForm'] = false;
            } else {
                $prenom = htmlspecialchars($_POST["prenom"]);
                if (!preg_match("/^[a-zA-Z-' ]*$/", $prenom)) {
                    $GLOBALS['prenomErr'] = "Only letters and white space allowed";
                    $GLOBALS['validForm'] = false;
                }
            }

            if (empty($_POST["email"])) {
                $GLOBALS['emailErr'] = "Email est requis";
                $GLOBALS['validForm'] = false;
            } else {
                $email = htmlspecialchars($_POST["email"]);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $GLOBALS['emailErr'] = "Email format est invalide";
                    $GLOBALS['validForm'] = false;
                }
            }

            if (empty($_POST["telephone"])) {
                $GLOBALS['telephoneErr'] = "Numero de telephone est requis";
                $GLOBALS['validForm'] = false;
            } else {
                $telephone = htmlspecialchars($_POST["telephone"]);
                if (!preg_match('/^\+?[0-9]{8,15}$/', $telephone)) {
                    $GLOBALS['telephoneErr'] = "Invalid telephone number format";
                    $GLOBALS['validForm'] = false;
                }
            }

            if (empty($_POST["password"])) {
                $GLOBALS['passwordErr'] = "Mot de passe est requis";
                $GLOBALS['validForm'] = false;
            } else {
                $password = htmlspecialchars($_POST["password"]);
                if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/', $password)) {
                    $GLOBALS['passwordErr'] = "Le mot de passe doit contenir une majuscule, une minuscule, un chiffre et 8 caractères minimum";                              
                    $GLOBALS['validForm'] = false;
                }
            }

            if (isset($_POST['email']) && $GLOBALS['validForm']) {
                $db = Database::connect();

                // Vérifier s'il y a déjà des utilisateurs
                $stmtCount = $db->query("SELECT COUNT(*) FROM utilisateurs");
                $count = $stmtCount->fetchColumn();

                // Attribuer le rôle
                $role = ($count == 0) ? 'Admin' : 'Client';

                $sql = "INSERT INTO utilisateurs (nom, prenom, email, password, telephone, role)
                        VALUES (:nom, :prenom, :email, :password, :telephone, :role)
                        ON DUPLICATE KEY UPDATE password = VALUES(password), telephone = VALUES(telephone), role = VALUES(role)";

                $stmt = $db->prepare($sql);
                $utilisateur = new utilisateurs($_POST["nom"], $_POST["prenom"], $_POST["email"], $_POST["password"], $_POST["telephone"]);

                $stmt->execute([
                    ':nom' => $utilisateur->getNom(),
                    ':prenom' => $utilisateur->getPrenom(),
                    ':email' => $utilisateur->getEmail(),
                    ':password' => $utilisateur->getPassword(),
                    ':telephone' => $utilisateur->getTelephone(),
                    ':role' => $role
                ]);

                $stmt = null;
                // REDIRECTION après enregistrement pour éviter le doublon
                header("Location: inscription.php?success=1");
                exit();
            }

        }
    }

    function testAndConnect()
    {
        $GLOBALS['validForm'] = true;
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (empty($_POST["email"])) {
                $GLOBALS['emailErr'] = "Email is required";
                $GLOBALS['validForm'] = false;
            } else {
                $email = htmlspecialchars($_POST["email"]);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $GLOBALS['emailErr'] = "Invalid email format";
                    $GLOBALS['validForm'] = false;
                }
            }

            if (empty($_POST["password"])) {
                $GLOBALS['passwordErr'] = "Password is required";
                $GLOBALS['validForm'] = false;
            } else {
                $password = htmlspecialchars($_POST["password"]);
                if (strlen($password) < 8) {
                    $GLOBALS['passwordErr'] = "Password must be at least 8 characters long";
                    $GLOBALS['validForm'] = false;
                }
            }
            if ($GLOBALS['validForm']) {
                $sql = "SELECT id, email, password, role FROM utilisateurs WHERE email = :email AND password = :password";
                $stmt = Database::connect()->prepare($sql);
                $stmt->execute([':email' => $_POST["email"], ':password' => $_POST["password"]]);
                $user = $stmt->fetch();

                if ($user) {
                    // Récupérer l'ID et le rôle de l'utilisateur
                    $id = $user['id'];
                    $role = $user['role'];

                    // Redirection en fonction du rôle avec l'ID dans l'URL
                    if ($role == 'Admin') {
                        header("Location: ../BackOffice/userdash.php?id=$id");
                        exit();
                    } elseif ($role == 'Client') {
                        header("Location: ../FrontOffice/home.php?id=$id");
                        exit();
                    } else {
                        // Si le rôle n'est ni "Admin" ni "Client", vous pouvez rediriger vers une page par défaut ou afficher un message d'erreur.
                        $GLOBALS['loginErr'] = "Role not recognized";
                    }
                } else {
                    $GLOBALS['loginErr'] = "Incorrect email or password";
                }
                $stmt = null;
            }
        }
    }

    function showUtilisateur($id)
    {
        $sql = "SELECT * FROM utilisateurs WHERE id = :id";
        $db = Database::connect();
        try {
            $query = $db->prepare($sql);
            $query->execute(['id' => $id]);

            $utilisateurs = $query->fetch();
            return $utilisateurs;
        } catch (Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }
    public function deleteUser($id)
    {
        try {
            $db = Database::connect();
            
            $stmt = $db->prepare("DELETE FROM utilisateurs WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            header("Location: ../BackOffice/userdash.php");
            exit();
            
        } catch (PDOException $e) {
            die("Erreur de suppression : " . $e->getMessage());
        }
    }

}

?>