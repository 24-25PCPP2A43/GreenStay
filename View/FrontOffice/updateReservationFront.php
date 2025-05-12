<?php
require_once '../Controller/ReservationController.php';
require_once '../Controller/LogementController.php';

$reservationController = new ReservationController();
$logementController = new LogementController();

// Check if ID is provided
if (isset($_GET['id'])) {
    $reservation = $reservationController->showReservation($_GET['id']);
    $logements = $logementController->listLogements();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update reservation with new data
    $reservationController->updateReservation(
        $_POST['id_reservation'],
        $_POST['id_logement'],
        $_POST['nom_client'],
        $_POST['email_client'],
        $_POST['date_debut'],
        $_POST['date_fin'],
        $_POST['statut']
    );

    header('Location: listReservationFront.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>WoOx Travel Update Reservation</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../templatemo_580_woox_travel/assets/css/animate.css">
    <link rel="stylesheet" href="../templatemo_580_woox_travel/assets/css/fontawesome.css">
    <link rel="stylesheet" href="../templatemo_580_woox_travel/assets/css/templatemo-woox-travel.css">
    <link rel="stylesheet" href="../templatemo_580_woox_travel/assets/css/owl.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />

    <!-- Custom Styles for Update Reservation -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7f7f7;
        }

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

        header .menu-trigger {
            display: none;
            cursor: pointer;
        }

        /* Mobile Menu */
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

        /* Form Styles */
        .reservation-form {
            background-color: #ffffff;
            padding: 50px 0;
            margin-top: 40px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .reservation-form h4 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .reservation-form .row {
            margin-bottom: 20px;
        }

        .reservation-form .col-lg-6 {
            margin-bottom: 15px;
        }

        .reservation-form .form-label {
            font-weight: bold;
        }

        .reservation-form input, .reservation-form select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .reservation-form button {
            background-color: #1fbdc0;
            color: white;
            padding: 15px 40px;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            width: 100%;
            margin-top: 20px;
        }

        .reservation-form button:hover {
            background-color: #00a1a3;
        }

        footer {
            background-color: #00c4cc;
            color: white;
            text-align: center;
            padding: 15px;
            margin-top: 40px;
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
<header class="header-area header-sticky">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="main-nav">
                    <a href="index.html" class="logo">
                        <img src="../templatemo_580_woox_travel/assets/images/logo.png" alt="WoOx Logo">
                    </a>
                    <ul class="nav">
                        <li><a href="">Home</a></li>
                        <li><a href="listReservationFront.php">Liste Reservations</a></li>
                        <li><a href="listLogementFront.php" class="active">Liste Logements</a></li>
                        <li><a href="addReservationFront.php">Ajouter une Reservation</a></li>
                        <li><a href="addLogementFront.php">Ajouter un Logement</a></li>
                    </ul>
                    <a class='menu-trigger' onclick="toggleMenu()">
                        <span>Menu</span>
                    </a>
                </nav>
            </div>
        </div>
    </div>
</header>

<!-- Update Reservation Form -->
<div class="reservation-form">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <form id="update-reservation-form" method="POST" action="updateReservationFront.php">
                    <input type="hidden" name="id_reservation" value="<?= htmlspecialchars($reservation['id_reservation']) ?>">
                    <input type="hidden" name="id_logement" value="<?= htmlspecialchars($reservation['id_logement']) ?>" />

                    <div class="row">
                        <div class="col-lg-12">
                            <h4>Update Reservation</h4>
                        </div>
                        <div class="col-lg-6">
                            <fieldset>
                                <label for="nom_client" class="form-label">Client Name</label>
                                <input type="text" name="nom_client" value="<?= htmlspecialchars($reservation['nom_client']) ?>" required>
                            </fieldset>
                        </div>
                        <div class="col-lg-6">
                            <fieldset>
                                <label for="email_client" class="form-label">Client Email</label>
                                <input type="email" name="email_client" value="<?= htmlspecialchars($reservation['email_client']) ?>" required>
                            </fieldset>
                        </div>
                        <div class="col-lg-6">
                            <fieldset>
                                <label for="date_debut" class="form-label">Start Date</label>
                                <input type="date" name="date_debut" value="<?= htmlspecialchars($reservation['date_debut']) ?>" required>
                            </fieldset>
                        </div>
                        <div class="col-lg-6">
                            <fieldset>
                                <label for="date_fin" class="form-label">End Date</label>
                                <input type="date" name="date_fin" value="<?= htmlspecialchars($reservation['date_fin']) ?>" required>
                            </fieldset>
                        </div>
                        <div class="col-lg-12">
                            <fieldset>
                                <label for="statut" class="form-label">Status</label>
                                <select name="statut" required>
                                    <option value="en attente" <?= ($reservation['statut'] == 'en attente') ? 'selected' : '' ?>>Pending</option>
                                    <option value="confirmée" <?= ($reservation['statut'] == 'confirmée') ? 'selected' : '' ?>>Confirmed</option>
                                    <option value="annulée" <?= ($reservation['statut'] == 'annulée') ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-lg-12">
                            <fieldset>
                                <button class="main-button">Update Reservation</button>
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
                <p>Copyright © 2036 <a href="#">WoOx Travel</a> Company. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="../templatemo_580_woox_travel/vendor/jquery/jquery.min.js"></script>
<script src="../templatemo_580_woox_travel/vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="../templatemo_580_woox_travel/assets/js/isotope.min.js"></script>
<script src="../templatemo_580_woox_travel/assets/js/owl-carousel.js"></script>
<script src="../templatemo_580_woox_travel/assets/js/wow.js"></script>
<script src="../templatemo_580_woox_travel/assets/js/tabs.js"></script>
<script src="../templatemo_580_woox_travel/assets/js/popup.js"></script>
<script src="../templatemo_580_woox_travel/assets/js/custom.js"></script>

</body>
</html>
