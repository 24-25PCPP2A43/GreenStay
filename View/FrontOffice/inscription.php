<?php
session_start();
require_once __DIR__ . '/../../Controller/UserController.php';
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Debug complet
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/mail_error.log');

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$userController = new UserController();
$registrationError = false;

if (isset($_POST['save'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['verification_error'] = "Erreur de sécurité. Veuillez réessayer.";
        header('Location: '.$_SERVER['PHP_SELF']);
        exit();
    }

    $activationCode = bin2hex(random_bytes(16));
    $userController->testAndSave($activationCode);
    $registrationError = !$userController->validForm;

    if ($userController->validForm) {
        $email = $_POST['email'];
        if ($userController->sendVerificationEmail($email, $activationCode)) {
            $_SESSION['registration_success'] = "Inscription réussie! Un email d'activation a été envoyé à votre adresse.";
        } else {
            $_SESSION['verification_error'] = "L'inscription a réussi mais l'email d'activation n'a pas pu être envoyé. Contactez l'administrateur.";
        }
        header('Location: '.$_SERVER['PHP_SELF']);
        exit();
    }
}

if (isset($_GET['activation_code'])) {
    $activationCode = $_GET['activation_code'];
    if ($userController->activateAccount($activationCode)) {
        $_SESSION['verification_success'] = "Votre compte a été activé avec succès. Vous pouvez maintenant vous connecter.";
    } else {
        $_SESSION['verification_error'] = "Code d'activation invalide ou compte déjà activé.";
    }
    header('Location: '.strtok($_SERVER['REQUEST_URI'], '?'));
    exit();
}

if (isset($_POST['connect'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['verification_error'] = "Erreur de sécurité. Veuillez réessayer.";
        header('Location: '.$_SERVER['PHP_SELF']);
        exit();
    }

    $captchaResponse = $_POST['g-recaptcha-response'] ?? '';
    $secretKey = '6Ld7ES4rAAAAAOd6A1xIZbKoGAeZa6thBf4TiLyV';

    if (empty($captchaResponse)) {
        $_SESSION['verification_error'] = "Veuillez valider le reCAPTCHA.";
        header('Location: '.$_SERVER['PHP_SELF']);
        exit();
    }

    // Vérification reCAPTCHA avec cURL
    $verifyUrl = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captchaResponse";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $verifyUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $verifyResponse = curl_exec($ch);
    curl_close($ch);
    
    $captchaSuccess = json_decode($verifyResponse);
    if (!$captchaSuccess || !$captchaSuccess->success) {
        $errorMsg = "Échec de la vérification reCAPTCHA. ";
        if (isset($captchaSuccess->{'error-codes'})) {
            $errorMsg .= "Erreurs: ".implode(", ", $captchaSuccess->{'error-codes'});
        }
        error_log("reCAPTCHA Error: " . $errorMsg); // Log dans le fichier
        $_SESSION['verification_error'] = $errorMsg;
        header('Location: '.$_SERVER['PHP_SELF']);
        exit();
    }

    $email = $_POST['email'];
    $banStatus = $userController->isUserBanned($email);

    if ($banStatus !== false) {
        $_SESSION['verification_error'] = $banStatus;
    } else {
        $userData = $userController->testAndConnect();
        if ($userData) {
            if ($userData['is_active'] == 0) {
                $_SESSION['verification_error'] = "Votre compte n'est pas encore activé. Veuillez vérifier votre email pour le lien d'activation.";
            } else {
                $_SESSION['user'] = $userData;
                if ($userData['role'] === 'Admin') {
                    header('Location: ../BackOffice/userdash.php');
                } else {
                    header('Location: home.php');
                }
                exit();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="../../assets/css/intlTelInput.css" rel="stylesheet">
    <link href="../../assets/css/inscription.css" rel="stylesheet" />
    <title>Formulaire de connexion & d'inscription</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css">
    <style>
        .alert-message {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px;
            border-radius: 5px;
            color: white;
            z-index: 1000;
            max-width: 300px;
            animation: fadeIn 0.5s, fadeOut 0.5s 4.5s;
        }
        .alert-success {
            background-color: #4CAF50;
        }
        .alert-error {
            background-color: #f44336;
        }
        @keyframes fadeIn {
            from {opacity: 0;}
            to {opacity: 1;}
        }
        @keyframes fadeOut {
            from {opacity: 1;}
            to {opacity: 0;}
        }
        
        .ban-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.3);
            z-index: 1001;
            text-align: center;
            width: 80%;
            max-width: 400px;
        }
        .ban-popup h3 {
            color: #f44336;
            margin-bottom: 15px;
        }
        .ban-popup p {
            margin-bottom: 20px;
            line-height: 1.5;
        }
        .ban-popup button {
            padding: 10px 20px;
            background: #1C4771;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .ban-popup button:hover {
            background: #153354;
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }
    </style>
</head>

<body>
    <?php if (isset($_SESSION['registration_success'])): ?>
        <div class="alert-message alert-success">
            <?php echo $_SESSION['registration_success']; ?>
            <?php unset($_SESSION['registration_success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['verification_success'])): ?>
        <div class="alert-message alert-success">
            <?php echo $_SESSION['verification_success']; ?>
            <?php unset($_SESSION['verification_success']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['verification_error']) && !strpos($_SESSION['verification_error'], "banni")): ?>
        <div class="alert-message alert-error">
            <?php echo $_SESSION['verification_error']; ?>
            <?php unset($_SESSION['verification_error']); ?>
        </div>
    <?php endif; ?>

    <div class="container <?php echo $registrationError ? 'sign-up-mode' : ''; ?>">
        <div class="forms-container">
            <div class="signin-signup">
                <form action="" method="POST" class="sign-in-form" id="loginForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <h2 class="title">Se connecter</h2>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input name="email" type="text" placeholder="Adresse e-mail" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"/>
                    </div>

                    <div class="error"><?php if (!empty($userController->emailErr)) echo $userController->emailErr; ?></div>

                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input name="password" type="password" id="password" placeholder="Mot de passe" />
                    </div>
                    <div class="forgot-password">
                        <a href="motdepasse_oublie.php">Mot de passe oublié ?</a>
                    </div>

                    <div class="error"><?php if (!empty($userController->passwordErr)) echo $userController->passwordErr; ?></div>
                    <div class="error"><?php if (!empty($userController->loginErr)) echo $userController->loginErr; ?></div>

                    <div class="g-recaptcha" data-sitekey="6Ld7ES4rAAAAAP8fg6NZ4BDWhUgv2Z4OZedsXefd"></div>

                    <input type="submit" name="connect" value="Connexion" class="btn solid" />

                    <p class="social-text">Ou connectez-vous les plateformes sociales</p>
                    <div class="social-media">
                        <a href="#" class="social-icon">
                            <i class="fa fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fa fa-instagram"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fa fa-github"></i>
                        </a>
                    </div>
                </form>

                <form action="" method="POST" class="sign-up-form">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <h2 class="title">S'inscrire</h2>

                    <h6 style="color: #1C4771FF;">Nom</h6>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="text" placeholder="Nom" name="nom" value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : '' ?>"/>
                    </div>
                    <div class="error"><?php if (!empty($userController->nomErr)) echo $userController->nomErr; ?></div>

                    <h6 style="color: #1C4771FF;">Prenom</h6>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="text" placeholder="prenom" name="prenom" value="<?php echo isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : '' ?>"/>
                    </div>
                    <div class="error"><?php if (!empty($userController->prenomErr)) echo $userController->prenomErr; ?></div>

                    <h6 style="color: #1C4771FF;">Email</h6>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" placeholder="Adresse e-mail" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"/>
                    </div>
                    <div class="error"><?php if (!empty($userController->emailErr)) echo $userController->emailErr; ?></div>

                    <h6 style="color: #1C4771FF;">N° de telephone</h6>
                    <div class="input-field">
                        <i class="fas fa-phone"></i>
                        <input type="tel" id="telephone" placeholder="N° de telephone" name="telephone" value="<?php echo isset($_POST['telephone']) ? htmlspecialchars($_POST['telephone']) : '' ?>"/>
                    </div>
                    <div class="error"><?php if (!empty($userController->telephoneErr)) echo $userController->telephoneErr; ?></div>

                    <h6 style="color: #1C4771FF;">Mot de passe</h6>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input name="password" type="password" placeholder="Mot de passe" />
                    </div>
                    <div class="error"><?php if (!empty($userController->passwordErr)) echo $userController->passwordErr; ?></div>

                    <input name="save" type="submit" class="btn" value="S'inscrire" />

                    <p class="social-text">Ou Visitez-vous les plateformes sociales</p>
                    <div class="social-media">
                        <a href="#" class="social-icon">
                            <i class="fa fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fa fa-instagram"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fa fa-github"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="panels-container">
            <div class="panel left-panel">
                <div class="content">
                    <p>
                        Bienvenue sur la page du site web de GreenStay. Connectez-vous pour accéder au site web.
                    </p>
                    <button class="btn transparent" id="sign-up-btn">
                        S'inscrire
                    </button>
                </div>
                <img src="../../assets/images/hero-img.png" class="image" alt="" />
            </div>
            <div class="panel right-panel">
                <div class="content">
                    <p>
                        Bienvenue sur la page du site web de GreenStay. Complétez votre inscription pour accéder au site web.
                    </p>
                    <button class="btn transparent" id="sign-in-btn">
                        Se connecter
                    </button>
                </div>
                <img src="../../assets/images/hero-img.png" class="image" alt="" />
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['verification_error']) && strpos($_SESSION['verification_error'], "banni")): ?>
        <div class="overlay"></div>
        <div class="ban-popup">
            <h3>Compte banni</h3>
            <p><?php echo $_SESSION['verification_error']; ?></p>
            <button onclick="this.parentElement.style.display='none'; document.querySelector('.overlay').style.display='none';">OK</button>
        </div>
        <?php unset($_SESSION['verification_error']); ?>
    <?php endif; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Gestion du formulaire de connexion
            loginForm.addEventListener('submit', function(e) {
    const recaptchaResponse = grecaptcha.getResponse();
    if (recaptchaResponse === '') {
        e.preventDefault();
        const errorDiv = document.querySelector('.recaptcha-error') || document.createElement('div');
        errorDiv.className = 'recaptcha-error';
        errorDiv.textContent = 'Veuillez cocher le reCAPTCHA';
        const recaptchaContainer = document.querySelector('.g-recaptcha');
        if (!document.querySelector('.recaptcha-error')) {
            recaptchaContainer.parentNode.insertBefore(errorDiv, recaptchaContainer.nextSibling);
        }
        return false;
    }
});

            // Initialisation du champ téléphone
            var input = document.querySelector("#telephone");
            if (input && window.intlTelInput) {
                window.intlTelInput(input, {
                    initialCountry: "tn",
                    preferredCountries: ['tn', 'fr', 'us'],
                    separateDialCode: true,
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
                });
            }

            // Gestion des panels
            const sign_in_btn = document.querySelector("#sign-in-btn");
            const sign_up_btn = document.querySelector("#sign-up-btn");
            const container = document.querySelector(".container");

            sign_up_btn.addEventListener("click", () => {
                container.classList.add("sign-up-mode");
            });

            sign_in_btn.addEventListener("click", () => {
                container.classList.remove("sign-up-mode");
            });
        });
    </script>
</body>
</html>