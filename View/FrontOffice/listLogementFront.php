<?php
// listLogementFront.php
require_once __DIR__ . '/../../Controller/LogementController.php';
$logementController = new LogementController();

// Récupérer les paramètres de recherche et de tri
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'default';
$type = isset($_GET['type']) ? $_GET['type'] : '';
$ville = isset($_GET['ville']) ? $_GET['ville'] : '';
$disponibilite = isset($_GET['disponibilite']) ? $_GET['disponibilite'] : '';
$minPrice = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$maxPrice = isset($_GET['max_price']) ? $_GET['max_price'] : '';

// Récupérer les logements filtrés
$logements = $logementController->searchLogements($search, $type, $ville, $disponibilite, $minPrice, $maxPrice);

// Récupérer les types et villes distincts pour les filtres
$allTypes = $logementController->getDistinctTypes();
$allVilles = $logementController->getDistinctVilles();

// Tri des logements
if (!empty($logements)) {
    switch ($sortBy) {
        case 'price_asc':
            usort($logements, function($a, $b) {
                return $a['prix_par_nuit'] - $b['prix_par_nuit'];
            });
            break;
        case 'price_desc':
            usort($logements, function($a, $b) {
                return $b['prix_par_nuit'] - $a['prix_par_nuit'];
            });
            break;
        case 'capacity':
            usort($logements, function($a, $b) {
                return $b['capacite'] - $a['capacite'];
            });
            break;
        case 'newest':
            // Supposant qu'il y a un champ 'date_ajout' dans votre table
            usort($logements, function($a, $b) {
                return strtotime($b['date_ajout'] ?? 'now') - strtotime($a['date_ajout'] ?? 'now');
            });
            break;
        // Pas de 'default' car on garde l'ordre original
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>WoOx Travel - Accommodations</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/../../templatemo_580_woox_travel/assets/css/animate.css">
    <link rel="stylesheet" href="/../../templatemo_580_woox_travel/assets/css/fontawesome.css">
    <link rel="stylesheet" href="/../../templatemo_580_woox_travel/assets/css/templatemo-woox-travel.css">
    <link rel="stylesheet" href="/../../templatemo_580_woox_travel/assets/css/owl.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />

    <style>
        /* Main container styles */
        .page-heading {
            background-color: #f9f9f9;
            padding: 40px 0 20px;
            text-align: center;
            margin-bottom: 20px; /* Reduced margin */
        }

        .page-heading h2 {
            color: #22b3c1;
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .page-heading p {
            color: #666;
            font-size: 16px;
            max-width: 800px;
            margin: 0 auto;
        }

        /* Search and filter section */
        .search-filters {
            background-color: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin: 0 auto 30px;
            max-width: 1260px;
        }

        .search-bar {
            display: flex;
            margin-bottom: 20px;
        }

        .search-bar input {
            flex-grow: 1;
            padding: 12px 20px;
            border: 1px solid #ddd;
            border-radius: 50px 0 0 50px;
            font-size: 16px;
            outline: none;
            transition: border-color 0.3s;
        }

        .search-bar input:focus {
            border-color: #22b3c1;
        }

        .search-bar button {
            background-color: #22b3c1;
            color: white;
            border: none;
            padding: 0 25px;
            border-radius: 0 50px 50px 0;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 16px;
        }

        .search-bar button:hover {
            background-color: #1a8a94;
        }

        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 15px;
        }

        .filter-group {
            flex: 1;
            min-width: 180px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #444;
            font-size: 14px;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 14px;
            color: #444;
            background-color: #fff;
            outline: none;
            transition: border-color 0.3s;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            border-color: #22b3c1;
        }

        .price-range {
            display: flex;
            gap: 10px;
        }

        .price-range input {
            width: 100%;
        }

        .filter-buttons {
            display: flex;
            justify-content: flex-end;
            margin-top: 15px;
            gap: 10px;
        }

        .filter-buttons button {
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-apply {
            background-color: #22b3c1;
            color: white;
        }

        .btn-apply:hover {
            background-color: #1a8a94;
        }

        .btn-reset {
            background-color: #f1f1f1;
            color: #444;
        }

        .btn-reset:hover {
            background-color: #e0e0e0;
        }

        /* Sort options */
        .sort-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
            margin-bottom: 20px;
            max-width: 1300px;
            margin-left: auto;
            margin-right: auto;
        }

        .results-count {
            font-size: 16px;
            color: #444;
            font-weight: 500;
        }

        .results-count span {
            color: #22b3c1;
            font-weight: 700;
        }

        .sort-dropdown {
            display: flex;
            align-items: center;
        }

        .sort-dropdown label {
            margin-right: 10px;
            font-weight: 500;
            color: #444;
        }

        .sort-dropdown select {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 14px;
            color: #444;
            background-color: #fff;
            outline: none;
            transition: border-color 0.3s;
        }

        .sort-dropdown select:focus {
            border-color: #22b3c1;
        }

        .logement-list-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            padding: 0 20px;
            max-width: 1300px;
            margin: 0 auto 60px;
        }

        /* Improved card styles */
        .logement-card {
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-color: white;
            display: flex;
            flex-direction: column;
            height: 100%;
            border: none;
            position: relative;
        }

        .logement-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(34, 179, 193, 0.2);
        }

        .image-container {
            position: relative;
            height: 220px;
            overflow: hidden;
        }

        .logement-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .logement-card:hover img {
            transform: scale(1.05);
        }

        .availability-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            z-index: 1;
        }

        .available {
            background-color: rgba(46, 204, 113, 0.9);
            color: white;
        }

        .not-available {
            background-color: rgba(231, 76, 60, 0.9);
            color: white;
        }

        .logement-card-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .logement-type {
            color: #22b3c1;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .logement-card-body h4 {
            font-size: 20px;
            color: #444;
            margin-bottom: 12px;
            font-weight: 700;
            line-height: 1.3;
        }

        .location {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            color: #666;
        }

        .location i {
            color: #22b3c1;
            margin-right: 8px;
        }

        .features {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
            margin-bottom: 15px;
        }

        .feature {
            display: flex;
            align-items: center;
            font-size: 14px;
            color: #666;
        }

        .feature i {
            color: #22b3c1;
            margin-right: 5px;
        }

        .price-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            padding-top: 15px;
        }

        .price {
            font-size: 22px;
            color: #22b3c1;
            font-weight: 700;
        }

        .price span {
            font-size: 14px;
            color: #888;
            font-weight: 400;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn {
            padding: 10px 18px;
            border-radius: 50px;
            font-size: 14px;
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

        .btn-primary {
            background-color: #22b3c1;
            color: white;
        }

        .btn-primary:hover {
            background-color: #1a8a94;
        }

        .btn-danger {
            background-color: #e74c3c;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .btn i {
            margin-right: 6px;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .logement-list-container {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .page-heading {
                padding: 30px 15px 15px;
            }

            .page-heading h2 {
                font-size: 28px;
            }

            .search-filters {
                padding: 15px;
            }

            .filters {
                flex-direction: column;
                gap: 10px;
            }

            .filter-group {
                min-width: 100%;
            }

            .price-range {
                flex-direction: column;
                gap: 10px;
            }

            .sort-options {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .logement-list-container {
                padding: 0 15px;
                gap: 20px;
            }

            .action-buttons {
                flex-direction: column;
                gap: 8px;
            }

            .btn {
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            .logement-list-container {
                grid-template-columns: 1fr;
                max-width: 400px;
            }
        }

        /* Header styling */
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
            background-color: #22b3c1;
            bottom: 0;
            left: 0;
            transition: width 0.3s ease;
        }

        header .main-nav ul li a:hover {
            color: #22b3c1;
        }

        header .main-nav ul li a:hover:after,
        header .main-nav ul li a.active:after {
            width: 100%;
        }

        header .main-nav ul li a.active {
            color: #22b3c1;
        }

        header .menu-trigger {
            display: none;
            cursor: pointer;
            font-size: 24px;
            color: #444;
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
        }

        /* Footer styling */
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
            color: #22b3c1;
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }

        /* No results found styles */
        .no-results {
            text-align: center;
            grid-column: 1 / -1;
            padding: 50px 30px;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .no-results h3 {
            color: #444;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .no-results p {
            color: #666;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .no-results .btn {
            display: inline-block;
            margin-top: 10px;
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
                        <li><a href="home.php">Home</a></li>
                        <li><a href="listReservationFront.php">Liste Reservations</a></li>
                        <li><a href="listLogementFront.php" class="active">Liste Logements</a></li>
                        <li><a href="addReservationFront.php">Ajouter une Reservation</a></li>
                        <li><a href="addLogementFront.php">Ajouter un Logement</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</header>

<!-- Page Heading -->
<div class="page-heading">
    <div class="container">
        <h2>Find Your Perfect Stay</h2>
        <p>Discover our selection of carefully curated accommodations to make your journey unforgettable</p>
    </div>
</div>

<!-- Search and Filter Section -->
<div class="search-filters">
    <form action="" method="GET">
        <div class="search-bar">
            <input type="text" name="search" placeholder="Search for accommodations..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit"><i class="fas fa-search"></i></button>
        </div>

        <div class="filters">
            <div class="filter-group">
                <label for="type">Type</label>
                <select name="type" id="type">
                    <option value="">All Types</option>
                    <?php foreach($allTypes as $t): ?>
                        <option value="<?= htmlspecialchars($t) ?>" <?= $type === $t ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="ville">City</label>
                <select name="ville" id="ville">
                    <option value="">All Cities</option>
                    <?php foreach($allVilles as $v): ?>
                        <option value="<?= htmlspecialchars($v) ?>" <?= $ville === $v ? 'selected' : '' ?>>
                            <?= htmlspecialchars($v) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label for="disponibilite">Availability</label>
                <select name="disponibilite" id="disponibilite">
                    <option value="">All</option>
                    <option value="1" <?= $disponibilite === '1' ? 'selected' : '' ?>>Available</option>
                    <option value="0" <?= $disponibilite === '0' ? 'selected' : '' ?>>Booked</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Price Range (€)</label>
                <div class="price-range">
                    <input type="number" name="min_price" placeholder="Min" value="<?= htmlspecialchars($minPrice) ?>">
                    <input type="number" name="max_price" placeholder="Max" value="<?= htmlspecialchars($maxPrice) ?>">
                </div>
            </div>
        </div>

        <div class="filter-buttons">
            <button type="button" class="btn-reset" onclick="resetFilters()">Reset Filters</button>
            <button type="submit" class="btn-apply">Apply Filters</button>
        </div>
    </form>
</div>

<!-- Sort Options -->
<div class="sort-options">
    <div class="results-count">
        <p>Showing <span><?= count($logements) ?></span> accommodations</p>
    </div>

    <div class="sort-dropdown">
        <label for="sort">Sort by:</label>
        <select name="sort" id="sort" onchange="changeSort(this.value)">
            <option value="default" <?= $sortBy === 'default' ? 'selected' : '' ?>>Default</option>
            <option value="price_asc" <?= $sortBy === 'price_asc' ? 'selected' : '' ?>>Price (Low to High)</option>
            <option value="price_desc" <?= $sortBy === 'price_desc' ? 'selected' : '' ?>>Price (High to Low)</option>
            <option value="capacity" <?= $sortBy === 'capacity' ? 'selected' : '' ?>>Capacity</option>
            <option value="newest" <?= $sortBy === 'newest' ? 'selected' : '' ?>>Newest First</option>
        </select>
    </div>
</div>

<!-- List Logement Cards -->
<div class="logement-list-container">
    <?php if (empty($logements)): ?>
        <div class="no-results">
            <h3>No accommodations found.</h3>
            <p>Try adjusting your search criteria or filters.</p>
            <a href="listLogementFront.php" class="btn btn-primary">
                <i class="fas fa-redo"></i> Show All Accommodations
            </a>
            <a href="addLogementFront.php" class="btn btn-primary" style="margin-left: 10px;">
                <i class="fas fa-plus"></i> Add New Accommodation
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($logements as $logement): ?>
            <div class="logement-card">
                <div class="image-container">
                    <img src="<?= htmlspecialchars($logement['image']) ?>" alt="<?= htmlspecialchars($logement['titre']) ?>">
                    <div class="availability-badge <?= $logement['disponibilite'] ? 'available' : 'not-available' ?>">
                        <?= $logement['disponibilite'] ? 'Available' : 'Booked' ?>
                    </div>
                </div>
                <div class="logement-card-body">
                    <div class="logement-type"><?= htmlspecialchars($logement['type']) ?></div>
                    <h4><?= htmlspecialchars($logement['titre']) ?></h4>
                    <div class="location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><?= htmlspecialchars($logement['ville']) ?></span>
                    </div>

                    <div class="features">
                        <div class="feature">
                            <i class="fas fa-users"></i>
                            <span><?= htmlspecialchars($logement['capacite']) ?> guests</span>
                        </div>
                        <?php if(isset($logement['chambres'])): ?>
                            <div class="feature">
                                <i class="fas fa-bed"></i>
                                <span><?= htmlspecialchars($logement['chambres']) ?> rooms</span>
                            </div>
                        <?php endif; ?>
                        <?php if(isset($logement['salles_de_bain'])): ?>
                            <div class="feature">
                                <i class="fas fa-bath"></i>
                                <span><?= htmlspecialchars($logement['salles_de_bain']) ?> baths</span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="price-container">
                        <div class="price">
                            <?= htmlspecialchars($logement['prix_par_nuit']) ?> € <span>/ night</span>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <a href="updateLogementFront.php?id=<?= $logement['id_logement'] ?>" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Modify
                        </a>
                        <a href="deleteLogementFront.php?id=<?= $logement['id_logement'] ?>" class="btn btn-danger"
                           onclick="return confirm('Are you sure you want to delete this accommodation?')">
                            <i class="fas fa-trash-alt"></i> Delete
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
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
<script src="/../../templatemo_580_woox_travel/vendor/jquery/jquery.min.js"></script>
<script src="/../../templatemo_580_woox_travel/vendor/bootstrap/js/bootstrap.min.js"></script>
<script src="/../../templatemo_580_woox_travel/assets/js/isotope.min.js"></script>
<script src="/../../templatemo_580_woox_travel/assets/js/owl-carousel.js"></script>
<script src="/../../templatemo_580_woox_travel/assets/js/wow.js"></script>
<script src="/../../templatemo_580_woox_travel/assets/js/tabs.js"></script>
<script src="/../../templatemo_580_woox_travel/assets/js/popup.js"></script>
<script src="/../../templatemo_580_woox_travel/assets/js/custom.js"></script>

<script>
    function toggleMenu() {
        document.querySelector('.main-nav').classList.toggle('active');
    }

    // Handle sorting
    function changeSort(value) {
        // Get current URL
        const url = new URL(window.location.href);

        // Update or add sort parameter
        url.searchParams.set('sort', value);

        // Redirect to new URL
        window.location.href = url.toString();
    }

    // Reset all filters
    function resetFilters() {
        window.location.href = 'listLogementFront.php';
    }
</script>

</body>
</html>