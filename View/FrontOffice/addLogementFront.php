<?php
// AddLogement.php (Controller for adding a logement)
require_once '../Controller/LogementController.php';
$logementController = new LogementController();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handling image upload if exists
    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "../uploads/";  // Directory to store uploaded images
        // Create directory if it doesn't exist
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $imagePath = $targetDir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);
    }

    // Create logement object
    $logement = new Logement(
        null,  // id_logement is null since it's a new logement
        $_POST['titre'],
        $_POST['description'],
        $_POST['adresse'],
        $_POST['ville'],
        $_POST['type'],
        $_POST['prix_par_nuit'],
        $_POST['capacite'],
        $imagePath,  // Image path
        isset($_POST['disponibilite']) ? 1 : 0  // Availability
    );

    // Add logement to the database
    $logementController->addLogement($logement);

    // Redirect after adding logement
    header('Location: listLogementFront.php');
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>WoOx Travel Add Logement Page</title>

    <!-- CSS for better design -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../templatemo_580_woox_travel/assets/css/animate.css">
    <link rel="stylesheet" href="../templatemo_580_woox_travel/assets/css/fontawesome.css">
    <link rel="stylesheet" href="../templatemo_580_woox_travel/assets/css/templatemo-woox-travel.css">
    <link rel="stylesheet" href="../templatemo_580_woox_travel/assets/css/owl.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fc;
            color: #333;
        }

        .header-area {
            background-color: #00c4cc;
            padding: 20px 0;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .header-area .logo img {
            max-width: 150px;
        }

        .header-area .nav {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .header-area .nav li {
            margin: 0 15px;
        }

        .header-area .nav li a {
            text-decoration: none;
            color: white;
            font-size: 16px;
            font-weight: 500;
        }

        .header-area .nav li a:hover {
            color: #FFD700;
        }

        .second-page-heading {
            background-color: #00c4cc;
            padding: 40px 0;
            text-align: center;
            color: white;
        }

        .second-page-heading h4 {
            font-size: 22px;
            margin-bottom: 10px;
        }

        .second-page-heading h2 {
            font-size: 36px;
            margin-bottom: 20px;
        }

        .main-button {
            margin-top: 20px;
        }

        .reservation-form {
            background-color: #fff;
            padding: 40px 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-top: 40px;
        }

        .reservation-form h4 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .reservation-form .form-label {
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        .reservation-form input, .reservation-form select, .reservation-form textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }

        .reservation-form input[type="file"] {
            padding: 8px;
            border: 1px solid #ddd;
            background-color: #f7f7f7;
        }

        .reservation-form input:focus, .reservation-form select:focus, .reservation-form textarea:focus {
            border-color: #00c4cc;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 196, 204, 0.5);
        }

        .reservation-form textarea {
            resize: vertical;
            height: 150px;
        }

        .reservation-form .main-button {
            background-color: #00c4cc;
            color: white;
            padding: 15px 30px;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            text-align: center;
        }

        .reservation-form .main-button:hover {
            background-color: #008c8a;
        }

        footer {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
        }

        footer a {
            color: #FFD700;
            text-decoration: none;
        }

        footer a:hover {
            color: #00c4cc;
        }

        @media (max-width: 768px) {
            .reservation-form {
                padding: 20px;
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
<header class="header-area header-sticky">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="main-nav">
                    <a href="index.html" class="logo">
                        <img src="../templatemo_580_woox_travel/assets/images/logo.png" alt="">
                    </a>
                    <ul class="nav">
                        <li><a href="">Home</a></li>
                        <li><a href="listReservationFront.php">Liste Reservations</a></li>
                        <li><a href="listLogementFront.php" class="active">Liste Logements</a></li>
                        <li><a href="addReservationFront.php">Ajouter une Reservation</a></li>
                        <li><a href="addLogementFront.php">Ajouter un Logement</a></li>
                    </ul>
                    <a class='menu-trigger'>
                        <span>Menu</span>
                    </a>
                </nav>
            </div>
        </div>
    </div>
</header>

<!-- Add Logement Form Section -->
<div class="second-page-heading">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <h4>Add a New Logement</h4>
                <h2>Enter Logement Details</h2>
                <p>Fill out the form to add a new logement to the system.</p>
                <div class="main-button"><a href="">Discover More</a></div>
            </div>
        </div>
    </div>
</div>

<!-- Add Logement Form -->
<div class="reservation-form">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <form id="add-logement-form" name="add-logement-form" method="POST" enctype="multipart/form-data" action="addLogementFront.php">
                    <div class="row">
                        <div class="col-lg-12">
                            <h4>Add a New <em>Logement</em> through This <em>Form</em></h4>
                        </div>
                        <div class="col-lg-6">
                            <fieldset>
                                <label for="titre" class="form-label">Logement Title</label>
                                <input type="text" name="titre" class="titre" placeholder="Ex. Luxury Villa" autocomplete="on" required>
                            </fieldset>
                        </div>
                        <div class="col-lg-6">
                            <fieldset>
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" class="description" placeholder="Ex. A beautiful luxury villa..." required></textarea>
                            </fieldset>
                        </div>
                        <div class="col-lg-6">
                            <fieldset>
                                <label for="adresse" class="form-label">Address</label>
                                <input type="text" name="adresse" class="adresse" placeholder="Ex. 123 Villa Street, Miami" autocomplete="on" required>
                            </fieldset>
                        </div>
                        <div class="col-lg-6">
                            <fieldset>
                                <label for="ville" class="form-label">City</label>
                                <input type="text" name="ville" class="ville" placeholder="Ex. Miami" autocomplete="on" required>
                            </fieldset>
                        </div>
                        <div class="col-lg-6">
                            <fieldset>
                                <label for="type" class="form-label">Type</label>
                                <select name="type" class="form-select" required>
                                    <option selected>Choose a Type</option>
                                    <option value="Appartement">Appartement</option>
                                    <option value="Maison">Maison</option>
                                    <option value="Villa">Villa</option>
                                    <option value="Studio">Studio</option>
                                    <option value="Chambre">Chambre</option>
                                </select>
                            </fieldset>
                        </div>
                        <div class="col-lg-6">
                            <fieldset>
                                <label for="prix_par_nuit" class="form-label">Price Per Night (€)</label>
                                <input type="number" name="prix_par_nuit" class="prix_par_nuit" placeholder="Ex. 100" required>
                            </fieldset>
                        </div>
                        <div class="col-lg-6">
                            <fieldset>
                                <label for="capacite" class="form-label">Capacity (People)</label>
                                <input type="number" name="capacite" class="capacite" placeholder="Ex. 4" required>
                            </fieldset>
                        </div>
                        <div class="col-lg-6">
                            <fieldset>
                                <label for="image" class="form-label">Image</label>
                                <input type="file" name="image" class="image" required>
                            </fieldset>
                        </div>
                        <div class="col-lg-6">
                            <fieldset>
                                <label for="disponibilite" class="form-label">Availability</label>
                                <input type="checkbox" name="disponibilite" class="disponibilite" checked>
                            </fieldset>
                        </div>
                        <div class="col-lg-12">
                            <fieldset>
                                <button class="main-button">Add Logement Now</button>
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
