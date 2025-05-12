<?php
// Include necessary controllers
require_once __DIR__ . '/../../Controller/LogementController.php';
require_once __DIR__ . '/../../Controller/ReservationController.php';

$logementController = new LogementController();
$reservationController = new ReservationController();
$logements = $logementController->listAvailableLogements();

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si les dates sont disponibles pour ce logement
    $isAvailable = $reservationController->checkAvailability(
        $_POST['id_logement'],
        $_POST['date_debut'],
        $_POST['date_fin']
    );

    if ($isAvailable) {
        $reservation = new Reservation(
            null,
            $_POST['id_logement'],
            $_POST['nom_client'],
            $_POST['email_client'],
            $_POST['date_debut'],
            $_POST['date_fin'],
            $_POST['statut']
        );

        $reservationController->addReservation($reservation);

        header('Location: listReservationFront.php');
        exit();
    } else {
        $error = "Ce logement n'est pas disponible pour les dates sélectionnées.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>WoOx Travel Reservation Page</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/../../templatemo_580_woox_travel/assets/css/animate.css">
    <link rel="stylesheet" href="/../../templatemo_580_woox_travel/assets/css/fontawesome.css">
    <link rel="stylesheet" href="/../../templatemo_580_woox_travel/assets/css/templatemo-woox-travel.css">
    <link rel="stylesheet" href="/../../templatemo_580_woox_travel/assets/css/owl.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />
    <style>
        header {
            background-color: #00c4cc;
            padding: 15px 0;
        }

        header .main-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
        }

        header .main-nav .logo img {
            max-width: 150px;
            height: auto;
        }

        header .main-nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        header .main-nav ul li {
            margin-left: 30px;
        }

        header .main-nav ul li a {
            text-decoration: none;
            color: white;
            font-weight: 600;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        header .main-nav ul li a:hover {
            color: #f9f9f9;
        }

        /* Add a hover effect for the menu trigger */
        header .menu-trigger {
            display: none;
            cursor: pointer;
        }

        /* For smaller screens */
        @media (max-width: 768px) {
            header .main-nav ul {
                display: none;
                width: 100%;
                flex-direction: column;
                align-items: center;
            }

            header .main-nav ul li {
                margin: 10px 0;
            }

            header .menu-trigger {
                display: block;
                font-size: 30px;
                color: white;
            }

            header .main-nav.active ul {
                display: flex;
            }
        }
    </style>
</head>
<body>


<!-- Preloader -->
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

<!-- Header Area -->
<!-- Header Area -->
<header class="header-area header-sticky">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="main-nav">
                    <!-- Logo -->
                    <a href="" class="logo">
                        <img src="../templatemo_580_woox_travel/assets/images/logo.png" alt="WoOx Logo">
                    </a>

                    <!-- Menu -->
                    <ul class="nav">
                        <li><a href="home.php">Home</a></li>
                        <li><a href="listReservationFront.php">Liste Reservations</a></li>
                        <li><a href="listLogementFront.php" class="active">Liste Logements</a></li>
                        <li><a href="addReservationFront.php">Ajouter une Reservation</a></li>
                        <li><a href="addLogementFront.php">Ajouter un Logement</a></li>
                    </ul>

                    <!-- Mobile Menu Trigger -->
                    <a class="menu-trigger" onclick="toggleMenu()">
                        <span>Menu</span>
                    </a>
                </nav>
            </div>
        </div>
    </div>
</header>

<!-- Navbar Script -->
<script>
    function toggleMenu() {
        document.querySelector('.main-nav').classList.toggle('active');
    }
</script>


<!-- Reservation Form Section -->
<div class="second-page-heading">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h4>Book Prefered Deal Here</h4>
                <h2>Make Your Reservation</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt uttersi labore et dolore magna aliqua is ipsum suspendisse ultrices gravida</p>
                <div class="main-button"><a href="addReservationFront.php">Discover More</a></div>
            </div>
        </div>
    </div>
</div>

<!-- Reservation Form -->
<div class="reservation-form">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <form id="reservation-form" name="reservation-form" method="POST" action="addReservationFront.php">
                    <div class="row">
                        <div class="col-lg-12">
                            <h4>Make Your <em>Reservation</em> Through This <em>Form</em></h4>
                        </div>
                        <div class="col-lg-6">
                            <fieldset>
                                <label for="Name" class="form-label">Your Name</label>
                                <input type="text" name="nom_client" class="Name" placeholder="Ex. John Smithee" autocomplete="on" required>
                            </fieldset>
                        </div>
                        <div class="col-lg-6">
                            <fieldset>
                                <label for="Number" class="form-label">Your Email</label>
                                <input type="email" name="email_client" class="Number" placeholder="Ex. john@example.com" autocomplete="on" required>
                            </fieldset>
                        </div>
                        <div class="col-lg-6">
                            <fieldset>
                                <label for="chooseGuests" class="form-label">Number of Guests</label>
                                <select name="Guests" class="form-select" aria-label="Default select example" id="chooseGuests" required>
                                    <option selected>ex. 3 or 4 or 5</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4+">4+</option>
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-lg-6">
                            <fieldset>
                                <label for="date" class="form-label">Check-in Date</label>
                                <input type="date" name="date_debut" class="date" required>
                            </fieldset>
                        </div>
                        <div class="col-lg-6">
                            <fieldset>
                                <label for="date_fin" class="form-label">Check-out Date</label>
                                <input type="date" name="date_fin" class="date" required>
                            </fieldset>
                        </div>
                        <div class="col-lg-12">
                            <fieldset>
                                <label for="chooseLogement" class="form-label">Choose Logement</label>
                                <select name="id_logement" class="form-select" aria-label="Default select example" id="chooseLogement" required>
                                    <option selected>Select a logement</option>
                                    <?php foreach ($logements as $logement): ?>
                                        <option value="<?= $logement['id_logement'] ?>"><?= $logement['titre'] ?> - <?= $logement['ville'] ?> (<?= $logement['prix_par_nuit'] ?> €/night)</option>
                                    <?php endforeach; ?>
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-lg-12">
                            <fieldset>
                                <label for="statut" class="form-label">Reservation Status</label>
                                <select name="statut" class="form-select" required>
                                    <option value="en attente">En Attente</option>
                                    <option value="confirmée">Confirmée</option>
                                    <option value="annulée">Annulée</option>
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-lg-12">
                            <fieldset>
                                <button type="submit" class="main-button">Make Your Reservation Now</button>
                            </fieldset>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<footer>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <p>Copyright © 2036 <a href="#">WoOx Travel</a> Company. All rights reserved.
                    <br>Design: <a href="https://templatemo.com" target="_blank" title="free CSS templates">TemplateMo</a></p>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="/../../templatemo_580_woox_travel/vendor/jquery/jquery.min.js"></script>
<script src="/../../templatemo_580_woox_travel/vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="/../../templatemo_580_woox_travel/assets/js/isotope.min.js"></script>
<script src="/../../templatemo_580_woox_travel/assets/js/owl-carousel.js"></script>
<script src="/../../templatemo_580_woox_travel/assets/js/wow.js"></script>
<script src="/../../templatemo_580_woox_travel/assets/js/tabs.js"></script>
<script src="/../../templatemo_580_woox_travel/assets/js/popup.js"></script>
<script src="/../../templatemo_580_woox_travel/assets/js/custom.js"></script>

</body>
</html>

