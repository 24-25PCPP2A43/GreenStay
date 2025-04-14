<?php
include '../../Controller/UserController.php';

$registrationError = false;

if (array_key_exists('save', $_POST)) {
    $x = new UserController();
    $x->testAndSave();
    // Check if form was invalid
    $registrationError = !$GLOBALS['validForm'];
}
if (array_key_exists('connect', $_POST)) {
    $x = new UserController();
    $x->testAndConnect();

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://kit.fontawesome.com/64d58efce2.js" crossorigin="anonymous"></script>
    <link href="../../assets/css/intlTelInput.css" rel="stylesheet">
    <link href="../../assets/css/inscription.css" rel="stylesheet" />


    <title>Formulaire de connexion & d'inscription</title>
</head>

<body>
    <div class="container <?php echo $registrationError ? 'sign-up-mode' : ''; ?>">
        <div class="forms-container">
            <div class="signin-signup">
                <form action="" method="POST" class="sign-in-form">
                    <h2 class="title">Se connecter</h2>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input name="email" type="text" placeholder="Adresse e-mail" />
                    </div>

                    <div class="error"><?php if (!empty($emailErr))
                        echo $emailErr; ?></div>

                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input name="password" type="password" id="password" placeholder="Mot de passe" />
                    </div>


                    <div class="error"><?php if (!empty($passwordErr))
                        echo $passwordErr; ?></div>
                    <div class="error"><?php if (!empty($loginErr))
                        echo $loginErr; ?></div>

                    <div class="g-recaptcha" data-sitekey="6LcHI80pAAAAADQR7ipJGR6WA17Kmnf3J-hbMJBN"></div>

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
                    <h2 class="title">S'inscrire</h2>

                    <h6 style="color: #1C4771FF;">Nom</h6>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="text" placeholder="Nom" name="nom" value="<?php echo isset($_POST['nom']) ? $_POST['nom'] : '' ?>"/>
                    </div>
                    <div class="error"><?php if (!empty($nomErr))
                        echo $nomErr; ?></div>

                    <h6 style="color: #1C4771FF;">Prenom</h6>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="text" placeholder="prenom" name="prenom" value="<?php echo isset($_POST['prenom']) ? $_POST['prenom'] : '' ?>"/>

                    </div>
                    <div class="error"><?php if (!empty($prenomErr))
                        echo $prenomErr; ?></div>

                    <h6 style="color: #1C4771FF;">Email</h6>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="text" placeholder="Adresse e-mail" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : '' ?>"/>

                    </div>
                    <div class="error"><?php if (!empty($emailErr))
                        echo $emailErr; ?></div>

                    <h6 style="color: #1C4771FF;">N° de telephone</h6>
                    <div class="input-field">
                        <i class="fas fa-phone"></i>
                        <input type="tel" placeholder="N° de telephone" name="telephone" value="<?php echo isset($_POST['telephone']) ? $_POST['telephone'] : '' ?>"/>

                    </div>
                    <div class="error"><?php if (!empty($telephoneErr))
                        echo $telephoneErr; ?></div>

                    <h6 style="color: #1C4771FF;">Mot de passe</h6>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input name="password" type="password" placeholder="Mot de passe" value="<?php echo isset($_POST['password']) ? $_POST['password'] : '' ?>"/>
                    </div>
                    <div class="error"><?php if (!empty($passwordErr))
                        echo $passwordErr; ?></div>


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
                    <!-- <img height="80px" src="" alt="logo"> -->
                    <p>
                        Bienvenue sur la page du site web de GreenStay . Connectez-vous pour accéder au
                        site web.
                    </p>
                    <button class="btn transparent" id="sign-up-btn">
                        S'inscrire
                    </button>
                </div>
                <img src="../../assets/images/hero-img.png" class="image" alt="" />
            </div>
            <div class="panel right-panel">
                <div class="content">
                    <!-- <img height="80px" src="../images/bleu1.png" alt="logo"> -->
                    <p>
                        Bienvenue sur la page du site web de GreenStay . Complétez votre inscription pour accéder au site web.
                    </p>
                    <button class="btn transparent" id="sign-in-btn">
                        Se connecter
                    </button>
                </div>
                <img src="../../assets/images/hero-img.png" class="image" alt="" />
            </div>
        </div>
    </div>

    <script src="../../assets/js/intlTelInput.js"></script>
    <script>
        var input = document.querySelector("#telephone");
        window.intlTelInput(input, {});
    </script>

    <script>
        const sign_in_btn = document.querySelector("#sign-in-btn");
        const sign_up_btn = document.querySelector("#sign-up-btn");
        const container = document.querySelector(".container");

        sign_up_btn.addEventListener("click", () => {
            container.classList.add("sign-up-mode");
        });

        sign_in_btn.addEventListener("click", () => {
            container.classList.remove("sign-up-mode");
        });
    </script>

</body>

</html>