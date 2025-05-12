<?php
require_once __DIR__ . '/../../Controller/ReservationController.php';
$reservationController = new ReservationController();

// Récupérer les paramètres de recherche et de tri
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'default';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Récupérer les réservations filtrées
$reservations = $reservationController->searchReservations($search, $status, $dateFrom, $dateTo);

// Récupérer les statuts distincts pour les filtres
$allStatuses = $reservationController->getDistinctStatuses();

// Tri des réservations
if (!empty($reservations)) {
    switch ($sortBy) {
        case 'date_asc':
            usort($reservations, function($a, $b) {
                return strtotime($a['date_debut']) - strtotime($b['date_debut']);
            });
            break;
        case 'date_desc':
            usort($reservations, function($a, $b) {
                return strtotime($b['date_debut']) - strtotime($a['date_debut']);
            });
            break;
        case 'name_asc':
            usort($reservations, function($a, $b) {
                return strcmp($a['nom_client'], $b['nom_client']);
            });
            break;
        case 'name_desc':
            usort($reservations, function($a, $b) {
                return strcmp($b['nom_client'], $a['nom_client']);
            });
            break;
        case 'status':
            usort($reservations, function($a, $b) {
                return strcmp($a['statut'], $b['statut']);
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
    <title>WoOx Travel - Reservations</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/../../templatemo_580_woox_travel/assets/css/animate.css">
    <link rel="stylesheet" href="/../../templatemo_580_woox_travel/assets/css/fontawesome.css">
    <link rel="stylesheet" href="/../../templatemo_580_woox_travel/assets/css/templatemo-woox-travel.css">
    <link rel="stylesheet" href="/../../templatemo_580_woox_travel/assets/css/owl.css">
    <link rel="stylesheet" href="https://unpkg.com/swiper@7/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">


    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7f7f7;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Header styles */
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
            color: white;
            font-size: 24px;
        }

        /* Page heading */
        .page-heading {
            background-color: #00c4cc;
            color: white;
            padding: 40px 0 20px;
            text-align: center;
            margin-bottom: 20px;
        }

        .page-heading h2 {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .page-heading p {
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
            border-color: #00c4cc;
        }

        .search-bar button {
            background-color: #00c4cc;
            color: white;
            border: none;
            padding: 0 25px;
            border-radius: 0 50px 50px 0;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 16px;
        }

        .search-bar button:hover {
            background-color: #00a3a9;
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
            border-color: #00c4cc;
        }

        .date-range {
            display: flex;
            gap: 10px;
        }

        .date-range input {
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
            background-color: #00c4cc;
            color: white;
        }

        .btn-apply:hover {
            background-color: #00a3a9;
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
            color: #00c4cc;
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
            border-color: #00c4cc;
        }

        /* Container principal et tableau */
        .reservations-container {
            max-width: 1300px;
            margin: 0 auto 60px;
            padding: 0 20px;
        }

        /* Style moderne pour le tableau */
        .modern-table {
            width: 100%;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .modern-table table {
            width: 100%;
            border-collapse: collapse;
            color: #333; /* Couleur par défaut pour le texte */
        }

        /* En-tête du tableau */
        .modern-table thead th {
            background-color: #00c4cc;
            color: white; /* Texte blanc pour les en-têtes */
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }

        /* Corps du tableau */
        .modern-table tbody td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            color: #333; /* Texte foncé pour les cellules */
        }

        .modern-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Effet hover sur les lignes */
        .modern-table tbody tr:hover {
            background-color: rgba(0, 196, 204, 0.05);
        }

        /* Badges de statut */
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 30px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-confirmée, .status-confirmed {
            background-color: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }

        .status-en.attente, .status-pending {
            background-color: rgba(255, 193, 7, 0.15);
            color: #ffc107;
        }

        .status-annulée, .status-cancelled {
            background-color: rgba(220, 53, 69, 0.15);
            color: #dc3545;
        }

        /* Boutons d'action */
        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }

        .btn-modify, .btn-primary {
            background-color: #00c4cc;
            color: white;
        }

        .btn-delete, .btn-danger {
            background-color: #ff5a5a;
            color: white;
        }

        .btn i {
            margin-right: 6px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
        }

        /* No results found styles */
        .no-results {
            text-align: center;
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

        /* Footer */
        footer {
            background-color: #00c4cc;
            color: white;
            text-align: center;
            padding: 20px 0;
            margin-top: 50px;
        }

        footer p {
            margin: 0;
        }

        footer a {
            color: white;
            text-decoration: none;
            font-weight: 600;
        }

        footer a:hover {
            text-decoration: underline;
        }

        /* Responsive adjustments */
        @media (max-width: 992px) {
            .modern-table {
                overflow-x: auto;
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

            .date-range {
                flex-direction: column;
                gap: 10px;
            }

            .sort-options {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
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

            .action-buttons {
                flex-direction: column;
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
                    </a>
                    <div class="menu-trigger" onclick="toggleMenu()">
                        <i class="fas fa-bars"></i>
                    </div>

                    <!-- Menu -->
                    <ul class="nav">
                        <li><a href="home.php">Home</a></li>
                        <li><a href="listReservationFront.php" class="active">Liste Reservations</a></li>
                        <li><a href="listLogementFront.php">Liste Logements</a></li>
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
        <h2>Manage Your Reservations</h2>
        <p>View, search and organize all your upcoming and past bookings</p>
    </div>
</div>

<!-- Search and Filter Section -->
<div class="search-filters">
    <form action="" method="GET">
        <div class="search-bar">
            <input type="text" name="search" placeholder="Search by name or email..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit"><i class="fas fa-search"></i></button>
        </div>

        <div class="filters">
            <div class="filter-group">
                <label for="status">Status</label>
                <select name="status" id="status">
                    <option value="">All Statuses</option>
                    <?php foreach($allStatuses as $s): ?>
                        <option value="<?= htmlspecialchars($s) ?>" <?= $status === $s ? 'selected' : '' ?>>
                            <?= htmlspecialchars(ucfirst($s)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label>Reservation Date Range</label>
                <div class="date-range">
                    <input type="date" name="date_from" placeholder="From" value="<?= htmlspecialchars($dateFrom) ?>">
                    <input type="date" name="date_to" placeholder="To" value="<?= htmlspecialchars($dateTo) ?>">
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
        <p>Showing <span><?= count($reservations) ?></span> reservations</p>
    </div>

    <div class="sort-dropdown">
        <label for="sort">Sort by:</label>
        <select name="sort" id="sort" onchange="changeSort(this.value)">
            <option value="default" <?= $sortBy === 'default' ? 'selected' : '' ?>>Default</option>
            <option value="date_asc" <?= $sortBy === 'date_asc' ? 'selected' : '' ?>>Date (Oldest First)</option>
            <option value="date_desc" <?= $sortBy === 'date_desc' ? 'selected' : '' ?>>Date (Newest First)</option>
            <option value="name_asc" <?= $sortBy === 'name_asc' ? 'selected' : '' ?>>Name (A-Z)</option>
            <option value="name_desc" <?= $sortBy === 'name_desc' ? 'selected' : '' ?>>Name (Z-A)</option>
            <option value="status" <?= $sortBy === 'status' ? 'selected' : '' ?>>Status</option>
        </select>
    </div>
</div>

<!-- Reservations Table -->
<div class="reservations-container">
    <?php if (empty($reservations)): ?>
        <div class="no-results">
            <h3>No reservations found.</h3>
            <p>Try adjusting your search criteria or filters.</p>
            <a href="listReservationFront.php" class="btn btn-primary">
                <i class="fas fa-redo"></i> Show All Reservations
            </a>
            <a href="addReservationFront.php" class="btn btn-primary" style="margin-left: 10px;">
                <i class="fas fa-plus"></i> Add New Reservation
            </a>
        </div>
    <?php else: ?>
        <div class="modern-table">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Email</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($reservations as $reservation): ?>
                    <?php
                    // Calculate duration
                    $checkIn = new DateTime($reservation['date_debut']);
                    $checkOut = new DateTime($reservation['date_fin']);
                    $duration = $checkIn->diff($checkOut)->days;

                    // Determine status badge class
                    $statusClass = '';
                    if ($reservation['statut'] === 'confirmée') {
                        $statusClass = 'status-confirmée';
                    } elseif ($reservation['statut'] === 'en attente') {
                        $statusClass = 'status-en attente';
                    } elseif ($reservation['statut'] === 'annulée') {
                        $statusClass = 'status-annulée';
                    }
                    ?>
                    <tr>
                        <td>#<?= htmlspecialchars($reservation['id_reservation']) ?></td>
                        <td><?= htmlspecialchars($reservation['nom_client']) ?></td>
                        <td><?= htmlspecialchars($reservation['email_client']) ?></td>
                        <td><?= htmlspecialchars(date('d M Y', strtotime($reservation['date_debut']))) ?></td>
                        <td><?= htmlspecialchars(date('d M Y', strtotime($reservation['date_fin']))) ?></td>
                        <td><?= $duration ?> night<?= $duration > 1 ? 's' : '' ?></td>
                        <td><span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($reservation['statut']) ?></span></td>
                        <td>
                            <div class="action-buttons">
                                <a href="updateReservationFront.php?id=<?= $reservation['id_reservation'] ?>" class="btn btn-modify">
                                    <i class="fas fa-edit"></i> Modify
                                </a>
                                <a href="deleteReservationFront.php?id=<?= $reservation['id_reservation'] ?>" class="btn btn-delete"
                                   onclick="return confirm('Are you sure you want to delete this reservation?')">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Footer -->
<footer>
    <div class="container">
        <p>Copyright © 2025 <a href="#">WoOx Travel</a> Company. All rights reserved.</p>
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
        window.location.href = 'listReservationFront.php';
    }
</script>

</body>
</html>