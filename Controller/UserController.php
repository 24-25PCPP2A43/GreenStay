<?php
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Model/User.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../vendor/autoload.php';

if (!class_exists('UserController')) {
    class UserController
    {
        private $db;

        public $nomErr = "";
        public $prenomErr = "";
        public $emailErr = "";
        public $passwordErr = "";
        public $telephoneErr = "";
        public $loginErr = "";
        public $validForm = true;

        public function __construct()
        {
            $this->db = Database::connect();
        }

        public function testAndSave()
        {
            $this->validForm = true;

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Validation des champs
                $this->validateNom();
                $this->validatePrenom();
                $this->validateEmail();
                $this->validateTelephone();
                $this->validatePassword();

                if ($this->validForm) {
                    $this->processRegistration();
                }
            }
        }

        private function validateNom()
        {
            if (empty($_POST["nom"])) {
                $this->nomErr = "Nom est requis";
                $this->validForm = false;
            } else {
                $nom = trim($_POST["nom"]);
                if (!preg_match("/^[\p{L}\s'-]+$/u", $nom)) {
                    $this->nomErr = "Caractères non autorisés dans le nom";
                    $this->validForm = false;
                }
            }
        }

        private function validatePrenom()
        {
            if (empty($_POST["prenom"])) {
                $this->prenomErr = "Prénom est requis";
                $this->validForm = false;
            } else {
                $prenom = trim($_POST["prenom"]);
                if (!preg_match("/^[\p{L}\s'-]+$/u", $prenom)) {
                    $this->prenomErr = "Caractères non autorisés dans le prénom";
                    $this->validForm = false;
                }
            }
        }

        private function validateEmail()
        {
            if (empty($_POST["email"])) {
                $this->emailErr = "Email est requis";
                $this->validForm = false;
            } else {
                $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $this->emailErr = "Format d'email invalide";
                    $this->validForm = false;
                } else {
                    $stmt = $this->db->prepare("SELECT id FROM utilisateurs WHERE email = ?");
                    $stmt->execute([$email]);
                    if ($stmt->rowCount() > 0) {
                        $this->emailErr = "Cet email est déjà utilisé";
                        $this->validForm = false;
                    }
                }
            }
        }

        private function validateTelephone()
        {
            if (empty($_POST["telephone"])) {
                $this->telephoneErr = "Numéro de téléphone est requis";
                $this->validForm = false;
            } else {
                $telephone = preg_replace('/[^0-9+]/', '', $_POST["telephone"]);
                if (!preg_match('/^\+?[0-9]{8,15}$/', $telephone)) {
                    $this->telephoneErr = "Format de téléphone invalide (ex: +21612345678)";
                    $this->validForm = false;
                }
            }
        }

        private function validatePassword()
        {
            if (empty($_POST["password"])) {
                $this->passwordErr = "Mot de passe est requis";
                $this->validForm = false;
            } else {
                $password = trim($_POST["password"]);
                if (strlen($password) < 8) {
                    $this->passwordErr = "Le mot de passe doit contenir au moins 8 caractères";
                    $this->validForm = false;
                } elseif (!preg_match('/[A-Z]/', $password) || 
                          !preg_match('/[a-z]/', $password) || 
                          !preg_match('/[0-9]/', $password)) {
                    $this->passwordErr = "Le mot de passe doit contenir au moins une majuscule, une minuscule et un chiffre";
                    $this->validForm = false;
                }
            }
        }

        private function processRegistration()
        {
            $verificationToken = bin2hex(random_bytes(32));
            $hashedPassword = password_hash($_POST["password"], PASSWORD_BCRYPT);

            try {
                $this->db->beginTransaction();

                // Vérifier si c'est le premier utilisateur
                $stmtCount = $this->db->query("SELECT COUNT(*) FROM utilisateurs");
                $count = $stmtCount->fetchColumn();
                $role = ($count == 0) ? 'Admin' : 'Client';

                $stmt = $this->db->prepare("
                    INSERT INTO utilisateurs (nom, prenom, email, telephone, password, activation_code, is_active, role)
                    VALUES (?, ?, ?, ?, ?, ?, 0, ?)
                ");
                
                $stmt->execute([
                    trim($_POST["nom"]),
                    trim($_POST["prenom"]),
                    filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL),
                    preg_replace('/[^0-9+]/', '', $_POST["telephone"]),
                    $hashedPassword,
                    $verificationToken,
                    $role
                ]);

                // Debug: Vérifier l'insertion
                $lastId = $this->db->lastInsertId();
                error_log("Nouvel utilisateur ID: $lastId");

                if ($this->sendVerificationEmail($_POST["email"], $verificationToken)) {
                    $this->db->commit();
                    $_SESSION['registration_success'] = "Un email de vérification a été envoyé. Veuillez vérifier votre boîte mail.";
                } else {
                    $this->db->rollBack();
                    $_SESSION['registration_error'] = "Erreur lors de l'envoi de l'email de vérification.";
                }
            } catch (PDOException $e) {
                $this->db->rollBack();
                $_SESSION['registration_error'] = "Erreur lors de l'inscription: " . $e->getMessage();
                error_log("Database error: " . $e->getMessage());
            }
        }

        public function testAndConnect() 
        {
            $this->validForm = true;

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $this->validateLoginEmail();
                $this->validateLoginPassword();

                if ($this->validForm) {
                    $this->authenticateUser();
                }
            }
        }

        private function validateLoginEmail()
        {
            if (empty($_POST["email"])) {
                $this->emailErr = "Email est requis";
                $this->validForm = false;
            } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
                $this->emailErr = "Format d'email invalide";
                $this->validForm = false;
            }
        }

        private function validateLoginPassword()
        {
            if (empty($_POST["password"])) {
                $this->passwordErr = "Mot de passe est requis";
                $this->validForm = false;
            }
        }

        private function authenticateUser()
        {
            $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
            $stmt = $this->db->prepare("SELECT * FROM utilisateurs WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                if (password_verify($_POST["password"], $user['password'])) {
                    if ($user['is_active'] == 0) {
                        $this->loginErr = "Compte non activé. <a href='resend_verification.php?email=".urlencode($user['email'])."'>Renvoyer l'email d'activation</a>";
                    } else {
                        // Régénération de l'ID de session
                        session_regenerate_id(true);
                        $_SESSION['user'] = $user;
                        $this->redirectUser($user);
                    }
                } else {
                    $this->loginErr = "Identifiants incorrects";
                    error_log("Failed login attempt for email: $email");
                }
            } else {
                $this->loginErr = "Identifiants incorrects";
            }
        }

        private function redirectUser($user)
        {
            $location = ($user['role'] == 'Admin') 
                ? "../BackOffice/userdash.php?id=".$user['id']
                : "../FrontOffice/home.php?id=".$user['id'];
            
            header("Location: $location");
            exit();
        }

        public function showUtilisateur($id)
        {
            $stmt = $this->db->prepare("SELECT * FROM utilisateurs WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        }

        public function deleteUser($id)
        {
            try {
                $this->db->beginTransaction();
                $stmt = $this->db->prepare("DELETE FROM utilisateurs WHERE id = ?");
                $stmt->execute([$id]);
                $this->db->commit();
                header("Location: ../BackOffice/userdash.php");
                exit();
            } catch (PDOException $e) {
                $this->db->rollBack();
                error_log("Delete user error: " . $e->getMessage());
                $_SESSION['error_message'] = "Erreur lors de la suppression de l'utilisateur";
                header("Location: ../BackOffice/userdash.php");
                exit();
            }
        }

        public function sendVerificationEmail($email, $verificationToken) 
        {
            $mail = new PHPMailer(true);
            
            // Configuration des logs
            $logsDir = __DIR__.'/../logs';
            $this->ensureLogsDirectoryExists($logsDir);

            try {
                // Configuration SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'iramtrabelsi30@gmail.com';
                $mail->Password = 'hfey eyju rkzj uotl';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->CharSet = 'UTF-8';
                
                // Debugging
                $mail->SMTPDebug = 2;
                $mail->Debugoutput = function($str, $level) use ($logsDir) {
                    $this->writeToLog($logsDir.'/smtp_debug_'.date('Y-m-d').'.log', 
                        date('Y-m-d H:i:s')." [$level]: $str");
                };

                // Expéditeur
                $mail->setFrom('iramtrabelsi30@gmail.com', 'GreenStay', 0);
                $mail->addAddress($email);
                
                // Lien de vérification
                $verificationLink = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . 
                    $_SERVER['HTTP_HOST'] . 
                    '/Projet/Projet/View/FrontOffice/verify.php?token=' . urlencode($verificationToken);

                // Contenu email
                $mail->isHTML(true);
                $mail->Subject = 'Activation de votre compte GreenStay';
                $mail->Body = $this->getEmailHtmlTemplate($verificationLink);
                $mail->AltBody = $this->getEmailTextTemplate($verificationLink);
                
                // Envoi
                if (!$mail->send()) {
                    throw new Exception("Échec d'envoi: ".$mail->ErrorInfo);
                }
                
                // Log succès
                $this->writeToLog($logsDir.'/email_success_'.date('Y-m-d').'.log',
                    date('Y-m-d H:i:s')." - Email envoyé à $email");
                
                return true;
                
            } catch (Exception $e) {
                $this->writeToLog($logsDir.'/email_errors_'.date('Y-m-d').'.log',
                    date('Y-m-d H:i:s')." - ERREUR pour $email: ".$e->getMessage());
                return false;
            }
        }

        private function ensureLogsDirectoryExists($path)
        {
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            if (!is_writable($path)) {
                chmod($path, 0777);
            }
        }

        private function writeToLog($filePath, $content)
        {
            try {
                file_put_contents($filePath, $content.PHP_EOL, FILE_APPEND);
            } catch (Exception $e) {
                error_log("Erreur d'écriture de log: ".$e->getMessage());
            }
        }

        private function getEmailHtmlTemplate($link)
        {
            return "<div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto'>
                <h2 style='color:#1C4771'>Activation de compte</h2>
                <p>Cliquez <a href='$link'>ici</a> pour activer votre compte</p>
            </div>";
        }

        public function getActivatedUserEmailByCode($token)
        {
            try {
                $stmt = $this->db->prepare("SELECT email FROM utilisateurs WHERE activation_code = ?");
                $stmt->execute([$token]);
                $user = $stmt->fetch();
                return $user ? $user['email'] : false;
            } catch (PDOException $e) {
                error_log("Get user email error: " . $e->getMessage());
                return false;
            }
        }

        public function sendConfirmationEmail($email)
        {
            $mail = new PHPMailer(true);
            
            try {
                // Configuration SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'iramtrabelsi30@gmail.com';
                $mail->Password = 'hfey eyju rkzj uotl';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->CharSet = 'UTF-8';
                
                // Expéditeur
                $mail->setFrom('iramtrabelsi30@gmail.com', 'GreenStay', 0);
                $mail->addAddress($email);
                
                // Contenu email
                $mail->isHTML(true);
                $mail->Subject = 'Compte GreenStay activé';
                $mail->Body = $this->getConfirmationEmailHtmlTemplate();
                $mail->AltBody = $this->getConfirmationEmailTextTemplate();
                
                return $mail->send();
                
            } catch (Exception $e) {
                error_log("Confirmation email error: ".$e->getMessage());
                return false;
            }
        }

        private function getConfirmationEmailHtmlTemplate()
        {
            return "<div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto'>
                <h2 style='color:#1C4771'>Compte activé avec succès</h2>
                <p>Votre compte GreenStay a été activé avec succès. Vous pouvez maintenant vous connecter.</p>
            </div>";
        }

        private function getConfirmationEmailTextTemplate()
        {
            return "Compte GreenStay activé\n\nVotre compte a été activé avec succès. Vous pouvez maintenant vous connecter.";
        }

        private function getEmailTextTemplate($link)
        {
            return "Activation de compte GreenStay\n\nLien: $link";
        }

        public function verifyAccount($token) 
        {
            try {
                $this->db->beginTransaction();
                
                $stmt = $this->db->prepare("SELECT id FROM utilisateurs WHERE activation_code = ? AND is_active = 0");
                $stmt->execute([$token]);
                
                if ($stmt->rowCount() > 0) {
                    $user = $stmt->fetch();
                    $updateStmt = $this->db->prepare("UPDATE utilisateurs SET is_active = 1, activation_code = NULL WHERE id = ?");
                    $updateStmt->execute([$user['id']]);
                    $this->db->commit();
                    return true;
                }
                
                $this->db->rollBack();
                return false;
            } catch (PDOException $e) {
                $this->db->rollBack();
                error_log("Verify account error: " . $e->getMessage());
                return false;
            }
        }

        public function activateAccount($activationCode) {
            return $this->verifyAccount($activationCode);
        }

        public function isUserBanned($email) {
            try {
                $stmt = $this->db->prepare("SELECT is_banned, ban_until FROM utilisateurs WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                
                if ($user && $user['is_banned'] == 1) {
                    if ($user['ban_until'] && strtotime($user['ban_until']) > time()) {
                        return "Compte banni jusqu'au ".date('d/m/Y H:i', strtotime($user['ban_until']));
                    } elseif ($user['ban_until'] === null) {
                        return "Compte banni définitivement";
                    }
                }
                return false;
            } catch (PDOException $e) {
                error_log("Ban check error: ".$e->getMessage());
                return false;
            }
        }

        public function resendVerificationEmail($email)
        {
            try {
                $stmt = $this->db->prepare("SELECT activation_code FROM utilisateurs WHERE email = ? AND is_active = 0");
                $stmt->execute([filter_var(trim($email), FILTER_SANITIZE_EMAIL)]);
                $user = $stmt->fetch();

                if ($user) {
                    return $this->sendVerificationEmail($email, $user['activation_code']);
                }
                return false;
            } catch (PDOException $e) {
                error_log("Resend verification error: " . $e->getMessage());
                return false;
            }
        }
        public function suggestNames() {
            header('Content-Type: application/json');
            
            $term = $_GET['q'] ?? '';
            if (strlen($term) < 2) {
                echo json_encode([]);
                return;
            }
    
            // Connexion à la base de données
            $conn = Database::connect();
            
            try {
                // Recherche dans la base de données
                $stmt = $conn->prepare("SELECT DISTINCT nom FROM utilisateurs WHERE nom LIKE ? LIMIT 5");
                $termLike = $term . '%';
                $stmt->execute([$termLike]);
                $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                echo json_encode($results);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Database error']);
            }
        }
    }
}
?>