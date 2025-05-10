<?php
session_start();
if (!isset($_SESSION['user'])) {
    // Pas connecté, rediriger
    header('Location: inscription.php');
    exit();
}
$userData = $_SESSION['user']; // Récupérer les infos utilisateur

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title>Green Stay Web Site</title>
    
    <!-- Bootstrap core CSS -->
    <link href="../../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="../../assets/css/fontawesome.css">
    <link rel="stylesheet" href="../../assets/css/templatemo-woox-travel.css">
    <link rel="stylesheet" href="../../assets/css/owl.css">
    <link rel="stylesheet" href="../../assets/css/animate.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />
    
    <style>
        /* Styles pour les messages d'erreur de validation */
        .error-message {
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
        }
        .is-invalid {
            border-color: #dc3545 !important;
        }
    </style>
</head>

<body>
    <!-- ***** Preloader Start ***** -->
    <div id="js-preloader" class="js-preloader">
        <div class="preloader-inner">
            <span class="dot"></span>
            <div class="dots">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>
    <!-- ***** Preloader End ***** -->

    <header class="header-area header-sticky">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav class="main-nav">
                        <!-- ***** Logo Start ***** -->
                        <a href="index.html" class="logo">
                            <img src="../../assets/images/logo.png.png" alt="">
                        </a>
                        <!-- ***** Logo End ***** -->
                        <!-- ***** Menu Start ***** -->
                        <ul class="nav" style="display: flex; align-items: center; flex-wrap: nowrap;">
                            <li><a href="index.html" class="active">Home</a></li>
                            <li><a href="about.html">About</a></li>
                            <li><a href="/projet/public/index.php?action=front_office">Services</a></li>
                            <li><a href="reservation.html">Reservation</a></li>
                            <li><a href="reservation.html">Book Yours</a></li>
                            <li><a href="listReclamations.php">Réclamations</a></li>
                            <li><a href="updateProfile.php" id="edit-profile-btn"><i class="fas fa-user-edit"></i> Modifier Profil</a></li>
                            <li><a href="../../Controller/logout.php" id="logout" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                        </ul>
                        <a class='menu-trigger'>
                            <span>Menu</span>
                        </a>
                        <!-- ***** Menu End ***** -->
                    </nav>
                </div>
            </div>
        </div>
    </header>
    <!-- ***** Header Area End ***** -->

    <!-- ***** Main Banner Area Start ***** -->
    <section id="section-1">
        <div class="content-slider">
            <input type="radio" id="banner1" class="sec-1-input" name="banner" checked>
            <input type="radio" id="banner2" class="sec-1-input" name="banner">
            <input type="radio" id="banner3" class="sec-1-input" name="banner">
            <input type="radio" id="banner4" class="sec-1-input" name="banner">
            <div class="slider">
                <div id="top-banner-1" class="banner">
                    <div class="banner-inner-wrapper header-text">
                        <div class="main-caption">
                            <h2>Take a Glimpse Into The Beautiful Country Of:</h2>
                            <h1>Caribbean</h1>
                            <div class="border-button"><a href="about.html">Go There</a></div>
                        </div>
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="more-info">
                                        <div class="row">
                                            <div class="col-lg-3 col-sm-6 col-6">
                                                <i class="fa fa-user"></i>
                                                <h4><span>Population:</span><br>44.48 M</h4>
                                            </div>
                                            <div class="col-lg-3 col-sm-6 col-6">
                                                <i class="fa fa-globe"></i>
                                                <h4><span>Territory:</span><br>275.400 KM<em>2</em></h4>
                                            </div>
                                            <div class="col-lg-3 col-sm-6 col-6">
                                                <i class="fa fa-home"></i>
                                                <h4><span>AVG Price:</span><br>$946.000</h4>
                                            </div>
                                            <div class="col-lg-3 col-sm-6 col-6">
                                                <div class="main-button">
                                                    <a href="about.html">Explore More</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="top-banner-2" class="banner">
                    <div class="banner-inner-wrapper header-text">
                        <div class="main-caption">
                            <h2>Take a Glimpse Into The Beautiful Country Of:</h2>
                            <h1>Switzerland</h1>
                            <div class="border-button"><a href="about.html">Go There</a></div>
                        </div>
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="more-info">
                                        <div class="row">
                                            <div class="col-lg-3 col-sm-6 col-6">
                                                <i class="fa fa-user"></i>
                                                <h4><span>Population:</span><br>8.66 M</h4>
                                            </div>
                                            <div class="col-lg-3 col-sm-6 col-6">
                                                <i class="fa fa-globe"></i>
                                                <h4><span>Territory:</span><br>41.290 KM<em>2</em></h4>
                                            </div>
                                            <div class="col-lg-3 col-sm-6 col-6">
                                                <i class="fa fa-home"></i>
                                                <h4><span>AVG Price:</span><br>$1.100.200</h4>
                                            </div>
                                            <div class="col-lg-3 col-sm-6 col-6">
                                                <div class="main-button">
                                                    <a href="about.html">Explore More</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="top-banner-3" class="banner">
                    <div class="banner-inner-wrapper header-text">
                        <div class="main-caption">
                            <h2>Take a Glimpse Into The Beautiful Country Of:</h2>
                            <h1>France</h1>
                            <div class="border-button"><a href="about.html">Go There</a></div>
                        </div>
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="more-info">
                                        <div class="row">
                                            <div class="col-lg-3 col-sm-6 col-6">
                                                <i class="fa fa-user"></i>
                                                <h4><span>Population:</span><br>67.41 M</h4>
                                            </div>
                                            <div class="col-lg-3 col-sm-6 col-6">
                                                <i class="fa fa-globe"></i>
                                                <h4><span>Territory:</span><br>551.500 KM<em>2</em></h4>
                                            </div>
                                            <div class="col-lg-3 col-sm-6 col-6">
                                                <i class="fa fa-home"></i>
                                                <h4><span>AVG Price:</span><br>$425.600</h4>
                                            </div>
                                            <div class="col-lg-3 col-sm-6 col-6">
                                                <div class="main-button">
                                                    <a href="about.html">Explore More</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="top-banner-4" class="banner">
                    <div class="banner-inner-wrapper header-text">
                        <div class="main-caption">
                            <h2>Take a Glimpse Into The Beautiful Country Of:</h2>
                            <h1>Thailand</h1>
                            <div class="border-button"><a href="about.html">Go There</a></div>
                        </div>
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="more-info">
                                        <div class="row">
                                            <div class="col-lg-3 col-sm-6 col-6">
                                                <i class="fa fa-user"></i>
                                                <h4><span>Population:</span><br>69.86 M</h4>
                                            </div>
                                            <div class="col-lg-3 col-sm-6 col-6">
                                                <i class="fa fa-globe"></i>
                                                <h4><span>Territory:</span><br>513.120 KM<em>2</em></h4>
                                            </div>
                                            <div class="col-lg-3 col-sm-6 col-6">
                                                <i class="fa fa-home"></i>
                                                <h4><span>AVG Price:</span><br>$165.450</h4>
                                            </div>
                                            <div class="col-lg-3 col-sm-6 col-6">
                                                <div class="main-button">
                                                    <a href="about.html">Explore More</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <nav>
                <div class="controls">
                    <label for="banner1"><span class="progressbar"><span class="progressbar-fill"></span></span><span
                            class="text">1</span></label>
                    <label for="banner2"><span class="progressbar"><span class="progressbar-fill"></span></span><span
                            class="text">2</span></label>
                    <label for="banner3"><span class="progressbar"><span class="progressbar-fill"></span></span><span
                            class="text">3</span></label>
                    <label for="banner4"><span class="progressbar"><span class="progressbar-fill"></span></span><span
                            class="text">4</span></label>
                </div>
            </nav>
        </div>
    </section>
    <!-- ***** Main Banner Area End ***** -->

    <div class="visit-country">
        <div class="container">
            <div class="row">
                <div class="col-lg-5">
                    <div class="section-heading">
                        <h2>Visit One Of Our Countries Now</h2>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                            labore.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8">
                    <div class="items">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="item">
                                    <div class="row">
                                        <div class="col-lg-4 col-sm-5">
                                            <div class="image">
                                                <img src="../../assets/images/country-01.jpg" alt="">
                                            </div>
                                        </div>
                                        <div class="col-lg-8 col-sm-7">
                                            <div class="right-content">
                                                <h4>SWITZERLAND</h4>
                                                <span>Europe</span>
                                                <div class="main-button">
                                                    <a href="about.html">Explore More</a>
                                                </div>
                                                <p>Woox Travel is a professional Bootstrap 5 theme HTML CSS layout for
                                                    your website. You can use this layout for your commercial work.</p>
                                                <ul class="info">
                                                    <li><i class="fa fa-user"></i> 8.66 Mil People</li>
                                                    <li><i class="fa fa-globe"></i> 41.290 km2</li>
                                                    <li><i class="fa fa-home"></i> $1.100.200</li>
                                                </ul>
                                                <div class="text-button">
                                                    <a href="about.html">Need Directions ? <i
                                                            class="fa fa-arrow-right"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="item">
                                    <div class="row">
                                        <div class="col-lg-4 col-sm-5">
                                            <div class="image">
                                                <img src="../../assets/images/country-02.jpg" alt="">
                                            </div>
                                        </div>
                                        <div class="col-lg-8 col-sm-7">
                                            <div class="right-content">
                                                <h4>CARIBBEAN</h4>
                                                <span>North America</span>
                                                <div class="main-button">
                                                    <a href="about.html">Explore More</a>
                                                </div>
                                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
                                                    eiusmod tempor incididunt ut labore dolor sit amet, consectetur
                                                    adipiscing elit, sed do eiusmod.</p>
                                                <ul class="info">
                                                    <li><i class="fa fa-user"></i> 44.48 Mil People</li>
                                                    <li><i class="fa fa-globe"></i> 275.400 km2</li>
                                                    <li><i class="fa fa-home"></i> $946.000</li>
                                                </ul>
                                                <div class="text-button">
                                                    <a href="about.html">Need Directions ? <i
                                                            class="fa fa-arrow-right"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="item last-item">
                                    <div class="row">
                                        <div class="col-lg-4 col-sm-5">
                                            <div class="image">
                                                <img src="../../assets/images/country-03.jpg" alt="">
                                            </div>
                                        </div>
                                        <div class="col-lg-8 col-sm-7">
                                            <div class="right-content">
                                                <h4>FRANCE</h4>
                                                <span>Europe</span>
                                                <div class="main-button">
                                                    <a href="about.html">Explore More</a>
                                                </div>
                                                <p>We hope this WoOx template is useful for you, please support us a <a
                                                        href="https://paypal.me/templatemo" target="_blank">small amount
                                                        of PayPal</a> to info [at] templatemo.com for our survival. We
                                                    really appreciate your contribution.</p>
                                                <ul class="info">
                                                    <li><i class="fa fa-user"></i> 67.41 Mil People</li>
                                                    <li><i class="fa fa-globe"></i> 551.500 km2</li>
                                                    <li><i class="fa fa-home"></i> $425.600</li>
                                                </ul>
                                                <div class="text-button">
                                                    <a href="about.html">Need Directions ? <i
                                                            class="fa fa-arrow-right"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <ul class="page-numbers">
                                    <li><a href="#"><i class="fa fa-arrow-left"></i></a></li>
                                    <li><a href="#">1</a></li>
                                    <li class="active"><a href="#">2</a></li>
                                    <li><a href="#">3</a></li>
                                    <li><a href="#"><i class="fa fa-arrow-right"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="side-bar-map">
                        <div class="row">
                            <div class="col-lg-12">
                                <div id="map">
                                    <iframe
                                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12469.776493332698!2d-80.14036379941481!3d25.907788681148624!2m3!1f357.26927939317244!2f20.870722720054623!3f0!3m2!1i1024!2i768!4f35!3m3!1m2!1s0x88d9add4b4ac788f%3A0xe77469d09480fcdb!2sSunny%20Isles%20Beach!5e1!3m2!1sen!2sth!4v1642869952544!5m2!1sen!2sth"
                                        width="100%" height="550px" frameborder="0"
                                        style="border:0; border-radius: 23px; " allowfullscreen=""></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="call-to-action">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <h2>Are You Looking To Travel ?</h2>
                    <h4>Make A Reservation By Clicking The Button</h4>
                </div>
                <div class="col-lg-4">
                    <div class="border-button">
                        <a href="reservation.html">Book Yours Now</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <p>Copyright © 2036 <a href="#">Green Stay</a> Company. All rights reserved.
                        <br>Design: <a href="https://templatemo.com" target="_blank"
                            title="free CSS templates">TemplateMo</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>


    <!-- Scripts -->
    <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

    <script src="../../assets/js/isotope.min.js"></script>
    <script src="../../assets/js/owl-carousel.js"></script>
    <script src="../../assets/js/wow.js"></script>
    <script src="../../assets/js/tabs.js"></script>
    <script src="../../assets/js/popup.js"></script>
    <script src="../../assets/js/custom.js"></script>

    <script>
        function bannerSwitcher() {
            next = $('.sec-1-input').filter(':checked').next('.sec-1-input');
            if (next.length) next.prop('checked', true);
            else $('.sec-1-input').first().prop('checked', true);
        }

        var bannerTimer = setInterval(bannerSwitcher, 5000);

        $('nav .controls label').click(function () {
            clearInterval(bannerTimer);
            bannerTimer = setInterval(bannerSwitcher, 5000)
        });
    </script>
    <!-- filepath: c:\xampp\htdocs\Projet\Projet\View\FrontOffice\home.php -->
<!-- ...existing code... -->
    <div id="edit-profile-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2>Modifier Profil</h2>
            <form id="edit-profile-form">
                <input type="hidden" id="id" name="id" value="<?php echo htmlspecialchars($userData['id']); ?>">
                
                <div class="form-group">
                    <label for="nom">Nom :</label>
                    <input type="text" id="nom" name="nom" class="form-control" 
                        value="<?php echo htmlspecialchars($userData['nom']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="prenom">Prénom :</label>
                    <input type="text" id="prenom" name="prenom" class="form-control" 
                        value="<?php echo htmlspecialchars($userData['prenom']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email :</label>
                    <input type="email" id="email" name="email" class="form-control" 
                        value="<?php echo htmlspecialchars($userData['email']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe :</label>
                    <input type="password" id="password" name="password" class="form-control" 
                        placeholder="Laissez vide pour ne pas changer">
                </div>
                
                <div class="form-group">
                    <label for="telephone">Téléphone :</label>
                    <input type="text" id="telephone" name="telephone" class="form-control" 
                        value="<?php echo htmlspecialchars($userData['telephone']); ?>" required>
                </div>

                <div style="display: flex; justify-content: space-between; margin-top: 20px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Message de succès -->
    <div id="success-message" style="display: none; position: fixed; top: 20px; right: 20px; background: #4CAF50; color: white; padding: 15px; border-radius: 5px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); animation: slideIn 0.5s ease-out;">
        <span style="margin-right: 15px;">✅</span>
        <span id="success-text">Profil mis à jour avec succès !</span>
    </div>

    <!-- Message d'erreur -->
    <div id="error-message" style="display: none; position: fixed; top: 20px; right: 20px; background: #f44336; color: white; padding: 15px; border-radius: 5px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); animation: slideIn 0.5s ease-out;">
        <span style="margin-right: 15px;">❌</span>
        <span id="error-text">Une erreur est survenue !</span>
    </div>

<style>
    h2 {
        text-align: center;
    }

    @keyframes slideIn {
        from { transform: translateX(100%); }
        to { transform: translateX(0); }
    }

    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }

    #success-message {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #4CAF50;
        color: white;
        padding: 15px;
        border-radius: 5px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        animation: slideIn 0.5s ease-out;
        z-index: 1000;
    }

    #error-message {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #f44336;
        color: white;
        padding: 15px;
        border-radius: 5px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        animation: slideIn 0.5s ease-out;
        z-index: 1000;
    }

    #success-message.fade-out, #error-message.fade-out {
        animation: fadeOut 1s ease-out forwards;
    }

    .modal {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal-content {
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        width: 450px;
        max-width: 90%;
        position: relative;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        animation: fadeIn 0.3s ease-in-out;
    }

    .close-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 22px;
        font-weight: bold;
        color: #333;
        cursor: pointer;
        transition: color 0.3s;
    }

    .close-btn:hover {
        color: #ff0000;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-control {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 16px;
    }

    .btn-primary {
        width: 100%;
        background-color: rgb(0, 175, 194);
        border: none;
        padding: 12px;
        color: white;
        font-size: 16px;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }
</style>

<script>
    const editProfileBtn = document.getElementById('edit-profile-btn');
    const editProfileModal = document.getElementById('edit-profile-modal');
    const closeBtn = document.querySelector('.close-btn');
    const successMessage = document.getElementById('success-message');
    const errorMessage = document.getElementById('error-message');
    const successText = document.getElementById('success-text');
    const errorText = document.getElementById('error-text');
    const editProfileForm = document.getElementById('edit-profile-form');

    editProfileBtn.addEventListener('click', function (e) {
        e.preventDefault();
        editProfileModal.style.display = 'flex';
    });

    closeBtn.addEventListener('click', function () {
        closeModal();
    });

    window.addEventListener('click', function (e) {
        if (e.target === editProfileModal) {
            closeModal();
        }
    });

    function closeModal() {
        editProfileModal.style.display = 'none';
    }

    // Soumission du formulaire sans validation
editProfileForm.addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(editProfileForm);

    fetch('/Projet/Projet/Controller/updateProfile.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('nom').value = data.user.nom;
            document.getElementById('prenom').value = data.user.prenom;
            document.getElementById('email').value = data.user.email;
            document.getElementById('telephone').value = data.user.telephone;

            // Message succès
            successText.textContent = "Profil mis à jour avec succès !";
            successMessage.style.display = 'block';

            setTimeout(() => {
                successMessage.classList.add('fade-out');
            }, 3000);

            setTimeout(() => {
                successMessage.style.display = 'none';
                successMessage.classList.remove('fade-out');
            }, 4000);

            closeModal();
        } else {
            // Vérifiez si des erreurs spécifiques sont présentes
            let errorMessages = "";
            if (data.errors) {
                for (const field in data.errors) {
                    errorMessages += data.errors[field] + "<br>";
                }
            }

            // Afficher le message d'erreur spécifique
            errorText.innerHTML = errorMessages || 'Une erreur est survenue lors de la mise à jour.';
            errorMessage.style.display = 'block';

            setTimeout(() => {
                errorMessage.classList.add('fade-out');
            }, 3000);

            setTimeout(() => {
                errorMessage.style.display = 'none';
                errorMessage.classList.remove('fade-out');
            }, 4000);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        errorText.textContent = 'Une erreur est survenue.';
        errorMessage.style.display = 'block';
    });
});


    // Script de déconnexion
    document.getElementById("logout").addEventListener("click", function (e) {
        e.preventDefault();
        if (confirm("Voulez-vous vraiment vous déconnecter ?")) {
            window.location.href = this.href;
        }
    });
</script>
