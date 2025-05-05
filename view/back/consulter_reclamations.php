<?php
include '../../config/database.php';

// Liste des statuts possibles pour le filtre
$statuts = [
    'Nouveau' => 'Nouveau',
    'En cours de traitement' => 'En cours de traitement',
    'Traité' => 'Traité',
    'Résolu' => 'Résolu', // Added based on screenshot
];

// Filtre par statut (si sélectionné)
$filter_statut = isset($_GET['statut']) ? $_GET['statut'] : '';

// Requête pour récupérer les réclamations avec la réponse (si disponible)
$sql = "SELECT r.id, r.sujet, r.message AS reclamation_message, r.statut, c.nom, c.prenom, re.date_reservation,
                rep.message AS reponse_message, rep.date_reponse
        FROM reclamations r
        JOIN clients c ON r.client_id = c.id
        JOIN reservations re ON r.reservation_id = re.id
        LEFT JOIN reponse rep ON r.id = rep.id_reclamation";

// Ajouter la condition de filtre sur le statut si nécessaire
if ($filter_statut !== '') {
    $sql .= " WHERE r.statut = :statut";
}

try {
    $stmt = $conn->prepare($sql);
    if ($filter_statut !== '') {
        $stmt->bindParam(':statut', $filter_statut, PDO::PARAM_STR);
    }
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de la requête : " . $e->getMessage());
}

// Debug: Log unique status values to identify mismatches
$unique_statuses = array_unique(array_column($result, 'statut'));
error_log("Unique statuses in database: " . json_encode($unique_statuses));

// Préparation des couleurs pour chaque statut
$color_palette = [
    'Nouveau' => '#FF0000',  // Red
    'En cours de traitement' => '#10B981',  // Emerald Green
    'Traité' => '#3B82F6',  // Bright Blue
    'Résolu' => '#6B7280',  // Gray (as seen in screenshot, can adjust to a more vibrant color if desired)
];

// Function to generate PDF file
/*function generatePdfFile($data) {
    require_once('../../vendor/autoload.php'); // Adjust path to autoload.php if needed

    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'orientation' => 'L' // Landscape orientation
    ]);

    $mpdf->SetTitle('Reclamations');

    // CSS Styles (you can customize these)
    $stylesheet = '
    body { font-family: sans-serif; font-size: 10pt; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #888; padding: 6px; text-align: left; }
    th { background-color: #ddd; font-weight: bold; }
    .badge {
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 0.9rem;
        font-weight: 500;
        color: white !important;
        display: inline-block;
        min-width: 80px;
        text-align: center;
    }
    ';

    $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::STYLESHEET);

    // HTML Table
    $html = '<h1>Liste des Réclamations</h1>';
    $html .= '<table>';
    $html .= '<thead><tr>';
    $html .= '<th>ID</th>';
    $html .= '<th>Sujet</th>';
    $html .= '<th>Message</th>';
    $html .= '<th>Statut</th>';
    $html .= '<th>Client</th>';
    $html .= '<th>Date Réservation</th>';
    $html .= '<th>Réponse</th>';
    $html .= '</tr></thead><tbody>';

    foreach ($data as $row) {
        $html .= '<tr>';
        $html .= '<td>' . htmlspecialchars($row['id']) . '</td>';
        $html .= '<td>' . htmlspecialchars($row['sujet']) . '</td>';
        $html .= '<td>' . nl2br(htmlspecialchars($row['reclamation_message'])) . '</td>';
        $html .= '<td><span class="badge" style="background-color: ' . (isset($color_palette[$row['statut']]) ? htmlspecialchars($color_palette[$row['statut']]) : '#6b7280') . ';">' . htmlspecialchars($row['statut']) . '</span></td>';
        $html .= '<td>' . htmlspecialchars($row['nom'] . ' ' . $row['prenom']) . '</td>';
        $html .= '<td>' . date("d/m/Y", strtotime($row['date_reservation'])) . '</td>';
        $html .= '<td>';
        if ($row['reponse_message']) {
            $html .= nl2br(htmlspecialchars($row['reponse_message'])) . '<br><small>Le ' . date("d/m/Y H:i", strtotime($row['date_reponse'])) . '</small>';
        } else {
            $html .= 'Pas encore répondu';
        }
        $html .= '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';

    $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

    // Output the PDF
    $mpdf->Output('reclamations.pdf', 'D'); // 'D' = Force Download
    exit;
}*/


// Generate PDF file if requested
/*if (isset($_GET['export_pdf'])) {
    generatePdfFile($result);
}*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoTravel - Reclamations Dashboard</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap CSS (for table and form) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <style>
        /* Sidebar transparent dark */
        .sidebar {
            background-color: rgba(0, 0, 0, 0.6) !important;
        }

        /* First row titles in black */
        .table th {
            background-color: #000;
            color: white;
        }

        /* Sidebar and Navbar Transparent */
        header {
            position: relative; /* Required for positioning the search bar */
        }
        footer {
            background-color: transparent !important;
        }

        /* Sidebar Animation */
        .sidebar {
            transition: width 0.3s ease;
        }
        .sidebar-collapsed {
            width: 80px;
        }
        .sidebar-expanded {
            width: 250px;
        }
        .sidebar-collapsed .menu-text, .sidebar-collapsed .logo-text {
            display: none;
        }
        .sidebar-menu li:hover {
            background-color: #1f2937;
            transform: translateX(5px);
            transition: all 0.2s ease;
        }
        /* Gradient Background */
        body {
            background: url('../../assets/images/best-01.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Poppins', sans-serif;
        }
        /* Container Styling */
        .content-container {
            background: rgba(255, 255, 255, 0.7);
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        /* Button Animation */
        .btn-custom {
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        /* Table Styling */
        .table th, .table td {
            vertical-align: middle;
        }
        /* Badge Styling */
        .badge {
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 500;
            color: white !important;
            display: inline-block;
            min-width: 80px;
            text-align: center;
        }
        /* Form Styling */
        .form-select {
            border: 2px solid #a855f7; /* Purple border */
            transition: border-color 0.3s ease;
        }
        .form-select:focus {
            border-color: #3b82f6; /* Blue focus */
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
        }
        /* Icon Alignment */
        .label-with-icon {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.1rem;
            color: #1e40af; /* Dark blue for labels */
        }
        .label-with-icon i {
            color: #8b5cf6; /* Vibrant purple for icons */
        }
        /* Chart Container */
        .chart-container {
            position: relative;
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            padding: 10px;
        }
        #statistiquesChart {
            width: 100% !important;
            height: 300px !important;
        }
        /* Statistics Section */
        #statistiques {
            display: none;
            transition: opacity 0.3s ease;
        }
        #statistiques.visible {
            display: block;
            opacity: 1;
        }
        /* Alert Styling */
        .alert-success {
            background-color: #10b981; /* Green */
            color: white;
            border: none;
        }

        /* Search Bar Styling */
        .search-bar {
            display: flex;
            align-items: center;
            padding: 5px 10px;
            border-radius: 20px;
        }

        .search-input {
            padding: 8px 12px;
            border: 1px solid #fff;
            border-radius: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            color: #333;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            background-color: #fff;
        }

        /* Header Transparent Background */
        .header-bg {
            background-color: rgba(0, 0, 0, 0.6);
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Style pour masquer le tableau par défaut */
        #tendancesTable {
            display: none;
        }

        /* Style pour le conteneur du tableau (plus large) */
        #tendancesContainer {
            width: 100%; /* Utilise toute la largeur disponible */
            max-width: 1000px; /* Ajustez cette valeur si nécessaire */
            margin: 0 auto; /* Centre le tableau horizontalement */
        }
    </style>
</head>
<body class="flex h-screen">
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar sidebar-expanded bg-gray-800 text-white flex flex-col">
        <div class="p-4 flex items-center space-x-2">
            <i class="fas fa-leaf text-2xl"></i>
            <span class="logo-text text-xl font-bold">EcoTravel</span>
        </div>
        <ul class="sidebar-menu flex-1">
            <li class="p-4 flex items-center space-x-2 cursor-pointer">
                <i class="fas fa-home"></i>
                <a href="../../index.php" class="menu-text">Home</a>
            </li>
            <li class="p-4 flex items-center space-x-2 cursor-pointer">
                <i class="fas fa-file-alt"></i>
                <a href="consulter_reclamations.php" class="menu-text">Reclamations</a>
            </li>
            <li class="p-4 flex items-center space-x-2 cursor-pointer">
                <i class="fas fa-comment"></i>
                <a href="consulter_reponses.php" class="menu-text">Responses</a>
            </li>
            <li class="p-4 flex items-center space-x-2 cursor-pointer">
                <i class="fas fa-users"></i>
                <span class="menu-text">Users</span>
            </li>
             <li class="p-4 flex items-center space-x-2 cursor-pointer">
                <i class="fas fa-chart-bar"></i>
                <a href="consulter_stat.php" class="menu-text">Statistiques</a>
            </li>
            <li class="p-4 flex items-center space-x-2 cursor-pointer">
                <i class="fas fa-chart-line"></i>
                <a href="consulter_tendances.php" class="menu-text">Tendances</a>
            </li>
            <li class="p-4 flex items-center space-x-2 cursor-pointer">
                <i class="fas fa-cog"></i>
                <span class="menu-text">Settings</span>
            </li>
        </ul>
        <div class="p-4">
            <button id="toggle-sidebar" class="text-white focus:outline-none">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <!-- Header -->
        <header class="bg-transparent shadow p-4 flex justify-between items-center">
            <div class="header-bg" style="width: 100%;">
                <h1 class="text-2xl font-semibold" style="color: white;">EcoTravel Admin</h1>

                <div style="display: flex; align-items: center;">
                    <!-- Search Bar -->
                    <div class="search-bar">
                        <input type="text" class="search-input" placeholder="Rechercher...">
                    </div>

                    <div>
                        <a href="logout.php" class="btn btn-red px-4 py-2 rounded hover:bg-red-600" style="color: lightgray;">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Section -->
        <main class="p-6 flex-1 overflow-y-auto">
            <div class="container mx-auto">
                <div class="content-container p-5">
                    <h2 class="text-center text-2xl font-semibold mb-4" style="color: black;">
                        <i class="fas fa-file-alt mr-2"></i> Liste des Réclamations
                    </h2>

                    <!-- Success Message -->
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success text-center">
                            <i class="fas fa-check-circle mr-2"></i> Opération réussie !
                        </div>
                    <?php endif; ?>

                     <!-- Export to PDF Button -->
                     <div class="mb-4 text-right">
                        <button onclick="window.print()" class="btn btn-success text-white rounded-lg px-4 py-2 hover:bg-green-600">
                            <i class="fas fa-print mr-2"></i> Imprimer vers PDF
                        </button>
                    </div>

                    <!-- Filter Form -->
                    <form action="consulter_reclamations.php" method="GET" class="mb-4 flex justify-center">
                        <div class="flex items-center gap-4 w-50">
                            <label for="statut" class="label-with-icon font-medium" style="color: #008080;">
                                <i class="fas fa-filter" style="color: #008080;"></i> Filtrer par statut :
                            </label>
                            <select name="statut" id="statut" class="form-select rounded-lg p-2" style="border-color: #008080;">
                                <option value="">Tous les statuts</option>
                                <?php foreach ($statuts as $key => $value): ?>
                                    <option value="<?= htmlspecialchars($key) ?>" <?= ($filter_statut == $key) ? 'selected' : '' ?>><?= htmlspecialchars($value) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-blue text-white rounded-lg px-4 py-2 hover:bg-blue-600">
                                <i class="fas fa-filter mr-2"></i> Filtrer
                            </button>
                        </div>
                    </form>

                    <!-- Reclamations Table -->
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered text-center">
                            <thead class="table-primary">
                                <tr>
                                    <th>ID</th>
                                    <th>Sujet</th>
                                    <th>Message</th>
                                    <th>Statut</th>
                                    <th>Client</th>
                                    <th>Date Réservation</th>
                                    <th>Réponse</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($result) > 0): ?>
                                    <?php foreach ($result as $row): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['id']) ?></td>
                                            <td><?= htmlspecialchars($row['sujet']) ?></td>
                                            <td><?= nl2br(htmlspecialchars($row['reclamation_message'])) ?></td>
                                            <td>
                                                <span class="badge" style="background-color: <?= isset($color_palette[$row['statut']]) ? htmlspecialchars($color_palette[$row['statut']]) : '#6b7280' ?>;">
                                                    <?= htmlspecialchars($row['statut']) ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($row['nom'] . ' ' . $row['prenom']) ?></td>
                                            <td><?= date("d/m/Y", strtotime($row['date_reservation'])) ?></td>
                                            <td>
                                                <?php if ($row['reponse_message']): ?>
                                                    <?= nl2br(htmlspecialchars($row['reponse_message'])) ?>
                                                    <br><small>Le <?= date("d/m/Y H:i", strtotime($row['date_reponse'])) ?></small>';
                                                <?php else: ?>
                                                    Pas encore répondu
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="ajouterReponse.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-blue text-white btn-sm m-1 hover:bg-blue-600">
                                                    <i class="fas fa-comment mr-1"></i> Répondre
                                                </a>
                                                <a href="changerStatut.php?id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-blue text-white btn-sm m-1 hover:bg-blue-600">
                                                    <i class="fas fa-edit mr-1"></i> Changer statut
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="8" class="text-center text-muted">Aucune réclamation trouvée.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Additional Links -->
                    <div class="text-center mt-4">
                        <a href="consulter_reponses.php" class="btn btn-blue text-white px-4 py-2 rounded hover:bg-blue-600 m-2">
                            <i class="fas fa-comment mr-2"></i> Voir les réponses
                        </a>
                        <a href="../../index.php" class="btn btn-red text-white px-4 py-2 rounded hover:bg-red-600 m-2">
                            <i class="fas fa-home mr-2"></i> Retour à l'accueil
                        </a>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-transparent text-center p-4 text-gray-600">
            © <?= date('Y') ?> EcoTravel. All rights reserved.
        </footer>
    </div>

    <!-- Scripts -->
    <!-- Bootstrap JS (for table and form) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Sidebar Toggle -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const toggleButton = document.getElementById('toggle-sidebar');
        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-expanded');
            sidebar.classList.toggle('sidebar-collapsed');
        });
    </script>
</body>
</html>