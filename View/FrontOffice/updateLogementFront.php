<?php
require_once __DIR__ . '/../../Controller/LogementController.php';

$logementController = new LogementController();
$error_message = '';
$success_message = '';

if (isset($_GET['id'])) {
    $logement = $logementController->showLogement($_GET['id']);
    if (!$logement) {
        header('Location: listLogementFront.php');
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Handle file upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $targetDir = "../uploads/";  // Directory to store uploaded images
            // Create directory if it doesn't exist
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            // Generate unique filename to prevent overwriting
            $fileExtension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
            $newFileName = uniqid() . '.' . $fileExtension;
            $imagePath = $targetDir . $newFileName;

            // Check file size (max 5MB)
            if ($_FILES["image"]["size"] > 5000000) {
                throw new Exception("Image is too large. Maximum size is 5MB.");
            }

            // Check if file is an actual image
            $check = getimagesize($_FILES["image"]["tmp_name"]);
            if ($check === false) {
                throw new Exception("File is not an image.");
            }

            // Allow certain file formats
            if ($fileExtension != "jpg" && $fileExtension != "png" && $fileExtension != "jpeg" && $fileExtension != "gif") {
                throw new Exception("Only JPG, JPEG, PNG & GIF files are allowed.");
            }

            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
                throw new Exception("Failed to upload image.");
            }
        } else {
            // If no new image is uploaded, keep the existing one
            $imagePath = $_POST['existing_image'];
        }

        // Input validation
        if (empty($_POST['titre']) || empty($_POST['description']) || empty($_POST['adresse']) ||
            empty($_POST['ville']) || empty($_POST['prix_par_nuit']) || empty($_POST['capacite'])) {
            throw new Exception("All fields are required.");
        }

        if ($_POST['prix_par_nuit'] <= 0) {
            throw new Exception("Price must be greater than zero.");
        }

        if ($_POST['capacite'] <= 0) {
            throw new Exception("Capacity must be greater than zero.");
        }

        // Sanitize inputs
        $titre = htmlspecialchars(trim($_POST['titre']));
        $description = htmlspecialchars(trim($_POST['description']));
        $adresse = htmlspecialchars(trim($_POST['adresse']));
        $ville = htmlspecialchars(trim($_POST['ville']));
        $type = htmlspecialchars($_POST['type']);
        $prix = floatval($_POST['prix_par_nuit']);
        $capacite = intval($_POST['capacite']);
        $chambres = isset($_POST['chambres']) ? intval($_POST['chambres']) : null;
        $salles_de_bain = isset($_POST['salles_de_bain']) ? intval($_POST['salles_de_bain']) : null;
        $disponibilite = isset($_POST['disponibilite']) ? 1 : 0;

        // Update logement with the new image path
        $logementController->updateLogement(
            $_POST['id_logement'],
            $titre,
            $description,
            $adresse,
            $ville,
            $type,
            $prix,
            $capacite,
            $imagePath,
            $disponibilite,
            $chambres,
            $salles_de_bain
        );

        $success_message = "Accommodation updated successfully!";

        // Refresh the logement data after update
        $logement = $logementController->showLogement($_POST['id_logement']);

    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>WoOx Travel - Update Accommodation</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/../../templatemo_580_woox_travel/assets/css/animate.css">
    <link rel="stylesheet" href="/../../templatemo_580_woox_travel/assets/css/fontawesome.css">
    <link rel="stylesheet" href="/../../templatemo_580_woox_travel/assets/css/templatemo-woox-travel.css">
    <link rel="stylesheet" href="/../../templatemo_580_woox_travel/assets/css/owl.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <style>
        :root {
            --primary-color: #22b3c1;
            --secondary-color: #1a8a94;
            --light-color: #f9f9f9;
            --text-color: #444;
            --border-color: #e1e1e1;
            --shadow-color: rgba(0, 0, 0, 0.08);
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fc;
            color: var(--text-color);
            line-height: 1.6;
        }

        /* Header styles */
        header {
            background-color: rgba(255, 255, 255, 0.95);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        header .main-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
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
            color: #444;
            font-weight: 600;
            font-size: 16px;
            transition: color 0.3s ease;
            position: relative;
            padding-bottom: 5px;
        }

        header .main-nav ul li a:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background-color: var(--primary-color);
            bottom: 0;
            left: 0;
            transition: width 0.3s ease;
        }

        header .main-nav ul li a:hover {
            color: var(--primary-color);
        }

        header .main-nav ul li a:hover:after,
        header .main-nav ul li a.active:after {
            width: 100%;
        }

        header .main-nav ul li a.active {
            color: var(--primary-color);
        }

        header .menu-trigger {
            display: none;
            cursor: pointer;
            font-size: 24px;
            color: #444;
        }

        /* Page heading styles */
        .page-heading {
            background-color: var(--light-color);
            padding: 40px 0 20px;
            text-align: center;
            margin-bottom: 40px;
        }

        .page-heading h2 {
            color: var(--primary-color);
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .page-heading p {
            color: #666;
            font-size: 16px;
            max-width: 800px;
            margin: 0 auto;
        }

        /* Form container styles */
        .form-container {
            max-width: 1000px;
            margin: 0 auto 60px;
            padding: 0 20px;
        }

        .update-form {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 5px 25px var(--shadow-color);
            overflow: hidden;
        }

        .form-header {
            background-color: var(--primary-color);
            padding: 25px 30px;
            color: white;
            position: relative;
        }

        .form-header h3 {
            font-size: 24px;
            font-weight: 600;
            margin: 0;
        }

        .form-header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 15px;
        }

        .form-body {
            padding: 30px;
        }

        /* Alert messages */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Form grid layout */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }

        .full-width {
            grid-column: span 2;
        }

        /* Form field styles */
        .form-group {
            margin-bottom: 5px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
            font-size: 15px;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            font-size: 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(34, 179, 193, 0.2);
            outline: none;
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2322b3c1' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            padding-right: 40px;
        }

        /* Image upload styles */
        .image-upload-container {
            position: relative;
            width: 100%;
            margin-bottom: 15px;
        }

        .current-image {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .current-image img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 15px;
            border: 2px solid var(--border-color);
        }

        .current-image p {
            margin: 0;
            font-size: 14px;
            color: #666;
        }

        .custom-file-upload {
            display: block;
            padding: 12px 15px;
            border: 1px dashed var(--border-color);
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s ease;
            background-color: #f9f9f9;
        }

        .custom-file-upload:hover {
            border-color: var(--primary-color);
            background-color: rgba(34, 179, 193, 0.05);
        }

        .custom-file-upload i {
            font-size: 24px;
            color: var(--primary-color);
            margin-bottom: 8px;
        }

        .custom-file-upload span {
            display: block;
            font-size: 14px;
            color: #666;
        }

        .custom-file-upload strong {
            color: var(--primary-color);
        }

        input[type="file"] {
            position: absolute;
            width: 0.1px;
            height: 0.1px;
            opacity: 0;
            overflow: hidden;
            z-index: -1;
        }

        /* Switch/toggle styles */
        .toggle-container {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .toggle-label {
            margin-right: 15px;
            font-weight: 600;
            color: #333;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: var(--primary-color);
        }

        input:focus + .slider {
            box-shadow: 0 0 1px var(--primary-color);
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .status-text {
            margin-left: 10px;
            font-size: 14px;
        }

        /* Button styles */
        .form-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            grid-column: span 2;
        }

        .btn {
            padding: 12px 25px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
        }

        .btn i {
            margin-right: 8px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
        }

        .btn-outline {
            background-color: transparent;
            color: var(--text-color);
            border: 1px solid var(--border-color);
        }

        .btn-outline:hover {
            background-color: #f1f1f1;
        }

        .btn-block {
            width: 100%;
        }

        /* Footer styles */
        footer {
            background-color: #f9f9f9;
            padding: 30px 0;
            text-align: center;
            margin-top: 50px;
        }

        footer p {
            color: #666;
            font-size: 14px;
        }

        footer a {
            color: var(--primary-color);
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }

        /* Responsive styles */
        @media (max-width: 992px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .full-width {
                grid-column: span 1;
            }

            .form-buttons {
                grid-column: span 1;
            }
        }

        @media (max-width: 768px) {
            header .main-nav {
                flex-direction: column;
                padding: 15px;
                position: relative;
            }

            header .main-nav .logo {
                margin-bottom: 15px;
                display: flex;
                justify-content: space-between;
                width: 100%;
                align-items: center;
            }

            header .main-nav ul {
                display: none;
                width: 100%;
                flex-direction: column;
                align-items: center;
                margin-top: 15px;
            }

            header .main-nav ul li {
                margin: 10px 0;
            }

            header .menu-trigger {
                display: block;
            }

            header .main-nav.active ul {
                display: flex;
            }

            .page-heading h2 {
                font-size: 26px;
            }

            .form-header {
                padding: 20px;
            }

            .form-body {
                padding: 20px;
            }

            .form-buttons {
                flex-direction: column;
                gap: 15px;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<!-- Header Area -->
<header class="header-area header-sticky">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav class="main-nav">
                    <!-- Logo -->
                    <a href="" class="logo">
                        <img src="../templatemo_580_woox_travel/assets/images/logo.png" alt="WoOx Logo">
                        <div class="menu-trigger" onclick="toggleMenu()">
                            <i class="fas fa-bars"></i>
                        </div>
                    </a>

                    <!-- Menu -->
                    <ul class="nav">
                        <li><a href="">Home</a></li>
                        <li><a href="listReservationFront.php">Reservations</a></li>
                        <li><a href="listLogementFront.php" class="active">Accommodations</a></li>
                        <li><a href="addReservationFront.php">Make Reservation</a></li>
                        <li><a href="addLogementFront.php">Add Accommodation</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</header>

<!-- Page Heading -->
<div class="page-heading">
    <div class="container">
        <h2>Update Accommodation</h2>
        <p>Modify the details of your property to ensure it's presented at its best</p>
    </div>
</div>

<!-- Update Form -->
<div class="form-container">
    <div class="update-form">
        <div class="form-header">
            <h3>Edit Accommodation Information</h3>
            <p>All fields marked with * are required</p>
        </div>

        <div class="form-body">
            <?php if ($error_message): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= $error_message ?>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= $success_message ?>
                </div>
            <?php endif; ?>

            <form id="update-logement-form" method="POST" enctype="multipart/form-data" action="updateLogementFront.php?id=<?= $logement['id_logement'] ?>">
                <input type="hidden" name="id_logement" value="<?= htmlspecialchars($logement['id_logement']) ?>">
                <input type="hidden" name="existing_image" value="<?= htmlspecialchars($logement['image']) ?>">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="titre" class="form-label">Title *</label>
                        <input type="text" name="titre" id="titre" class="form-control" placeholder="Ex. Luxury Villa by the Beach" value="<?= htmlspecialchars($logement['titre']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="type" class="form-label">Property Type *</label>
                        <select name="type" id="type" class="form-control" required>
                            <option value="Apartment" <?= ($logement['type'] == 'Apartment') ? 'selected' : '' ?>>Apartment</option>
                            <option value="House" <?= ($logement['type'] == 'House') ? 'selected' : '' ?>>House</option>
                            <option value="Villa" <?= ($logement['type'] == 'Villa') ? 'selected' : '' ?>>Villa</option>
                            <option value="Studio" <?= ($logement['type'] == 'Studio') ? 'selected' : '' ?>>Studio</option>
                            <option value="Room" <?= ($logement['type'] == 'Room') ? 'selected' : '' ?>>Room</option>
                            <option value="Cottage" <?= ($logement['type'] == 'Cottage') ? 'selected' : '' ?>>Cottage</option>
                            <option value="Bungalow" <?= ($logement['type'] == 'Bungalow') ? 'selected' : '' ?>>Bungalow</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label for="description" class="form-label">Description *</label>
                        <textarea name="description" id="description" class="form-control" placeholder="Describe your property in detail..." required><?= htmlspecialchars($logement['description']) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="adresse" class="form-label">Address *</label>
                        <input type="text" name="adresse" id="adresse" class="form-control" placeholder="Street address" value="<?= htmlspecialchars($logement['adresse']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="ville" class="form-label">City *</label>
                        <input type="text" name="ville" id="ville" class="form-control" placeholder="City name" value="<?= htmlspecialchars($logement['ville']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="prix_par_nuit" class="form-label">Price Per Night (€) *</label>
                        <input type="number" name="prix_par_nuit" id="prix_par_nuit" class="form-control" placeholder="100" min="1" step="0.01" value="<?= htmlspecialchars($logement['prix_par_nuit']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="capacite" class="form-label">Capacity (People) *</label>
                        <input type="number" name="capacite" id="capacite" class="form-control" placeholder="4" min="1" value="<?= htmlspecialchars($logement['capacite']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="chambres" class="form-label">Bedrooms</label>
                        <input type="number" name="chambres" id="chambres" class="form-control" placeholder="2" min="0" value="<?= isset($logement['chambres']) ? htmlspecialchars($logement['chambres']) : '' ?>">
                    </div>

                    <div class="form-group">
                        <label for="salles_de_bain" class="form-label">Bathrooms</label>
                        <input type="number" name="salles_de_bain" id="salles_de_bain" class="form-control" placeholder="1" min="0" step="0.5" value="<?= isset($logement['salles_de_bain']) ? htmlspecialchars($logement['salles_de_bain']) : '' ?>">
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label">Property Image</label>
                        <div class="image-upload-container">
                            <?php if ($logement['image']): ?>
                                <div class="current-image">
                                    <img src="<?= htmlspecialchars($logement['image']) ?>" alt="Current Property Image">
                                    <p>Current image</p>
                                </div>
                            <?php endif; ?>

                            <label for="image" class="custom-file-upload">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Drag your image here, or <strong>browse</strong></span>
                                <span>Supports: JPG, JPEG, PNG, GIF (Max 5MB)</span>
                            </label>
                            <input type="file" name="image" id="image" accept="image/*">
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <div class="toggle-container">
                            <span class="toggle-label">Availability Status:</span>
                            <label class="toggle-switch">
                                <input type="checkbox" name="disponibilite" id="disponibilite" <?= ($logement['disponibilite']) ? 'checked' : '' ?>>
                                <span class="slider"></span>
                            </label>
                            <span class="status-text" id="status-text"><?= ($logement['disponibilite']) ? 'Available for booking' : 'Not available' ?></span>
                        </div>
                    </div>

                    <div class="form-buttons">
                        <a href="listLogementFront.php" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Accommodation
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Footer -->
<footer>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <p>Copyright © 2025 <a href="#">WoOx Travel</a> Company. All rights reserved.</p>
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

<script>
    // Toggle menu for mobile
    function toggleMenu() {
        document.querySelector('.main-nav').classList.toggle('active');
    }

    // File input preview
    document.getElementById('image').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name;
        if (fileName) {
            const label = this.previousElementSibling;
            label.innerHTML = `<i class="fas fa-file-image"></i><span>${fileName}</span>`;
            label.style.borderColor = '#22b3c1';
        }
    });

    // Availability toggle
    document.getElementById('disponibilite').addEventListener('change', function() {
        const statusText = document.getElementById('status-text');
        if (this.checked) {
            statusText.textContent = 'Available for booking';
        } else {
            statusText.textContent = 'Not available';
        }
    });

    // Form validation
    document.getElementById('update-logement-form').addEventListener('submit', function(e) {
        const requiredFields = ['titre', 'description', 'adresse', 'ville', 'prix_par_nuit', 'capacite'];
        let hasError = false;

        requiredFields.forEach(field => {
            const input = document.getElementById(field);
            const errorMsg = document.getElementById(`${field}-error`);

            if (!input.value.trim()) {
                e.preventDefault();
                hasError = true;
                input.classList.add('is-invalid');
                if (errorMsg) {
                    errorMsg.textContent = 'Ce champ est obligatoire';
                    errorMsg.style.display = 'block';
                }
            } else {
                input.classList.remove('is-invalid');
                if (errorMsg) {
                    errorMsg.textContent = '';
                    errorMsg.style.display = 'none';
                }
            }
        });
</script>