<?php
include '../../config/database.php';

try {
    // Récupérer toutes les réponses avec info sur la réclamation
    $sql = "SELECT r.id_reponse, r.message AS texte_reponse, r.date_reponse, 
                   rec.id AS id_reclamation, rec.sujet
            FROM reponse r
            INNER JOIN reclamations rec ON r.id_reclamation = rec.id";
    $stmt = $conn->query($sql);
    $reponses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoTravel - Responses Dashboard</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap CSS (for table) -->
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
        .table th {
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
            border: 2px solid #008080; /* Teal border */
            transition: border-color 0.3s ease;
        }
        .form-select:focus {
            border-color: #2cae72; /* Teal focus */
            box-shadow: 0 0 0 0.2rem rgba(44, 174, 114, 0.25);
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
        /* Alert Styling */
        .alert-danger {
            background-color: #ef4444; /* Red */
            color: white;
            border: none;
        }

         /* Header Transparent Background */
        .header-bg {
            background-color: rgba(0, 0, 0, 0.6);
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            <!-- Add these lines -->
            <li class="p-4 flex items-center space-x-2 cursor-pointer">
                <i class="fas fa-chart-bar"></i>
                <a href="statistiques.php" class="menu-text">Statistiques</a>
            </li>
            <li class="p-4 flex items-center space-x-2 cursor-pointer">
                <i class="fas fa-chart-pie"></i>
                <a href="consulter_tendances.php" class="menu-text">Tendances</a>
            </li>
            <!-- End of added lines -->
            <li class="p-4 flex items-center space-x-2 cursor-pointer">
                <i class="fas fa-users"></i>
                <span class="menu-text">Users</span>
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
                        <input type="text" id="search-input" class="search-input" placeholder="Rechercher par sujet...">
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
                        <i class="fas fa-comment mr-2"></i> Liste des Réponses
                    </h2>

                    <!-- Success Message -->
                    <?php if (isset($_GET['success'])) : ?>
                        <div class="bg-green-100 text-green-700 p-4 rounded mb-4 text-center">
                            <i class="fas fa-check-circle mr-2"></i> Opération réussie !
                        </div>
                    <?php endif; ?>

                    <!-- Accueil Button -->
                    <div class="mb-4 text-start">
                        <a href="../../index.php" class="btn btn-custom" style="background-color: rgba(128, 128, 128, 0.5); color: white; px-4 py-2 rounded hover:bg-blue-600">
                            <i class="fas fa-home mr-2"></i> Accueil
                        </a>
                    </div>

                    <!-- Responses Table -->
                    <?php if (empty($reponses)) : ?>
                        <p class="text-center text-gray-600">Aucune réponse trouvée.</p>
                    <?php else : ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered text-center" id="responses-table">
                                <thead class="table-primary">
                                    <tr>
                                        <th>ID Réponse</th>
                                        <th>ID Réclamation</th>
                                        <th>Sujet Réclamation</th>
                                        <th>Message Réponse</th>
                                        <th>Date Réponse</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reponses as $reponse) : ?>
                                        <tr>
                                            <td><?= htmlspecialchars($reponse['id_reponse']) ?></td>
                                            <td><?= htmlspecialchars($reponse['id_reclamation']) ?></td>
                                            <td class="subject"><?= htmlspecialchars($reponse['sujet']) ?></td>
                                            <td><?= nl2br(htmlspecialchars($reponse['texte_reponse'])) ?></td>
                                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($reponse['date_reponse']))) ?></td>
                                            <td>
                                                <a href="modifierReponse.php?id_reponse=<?= $reponse['id_reponse'] ?>" class="btn btn-custom" style="background-color: #20B2AA; color: white; btn-warning btn-sm m-1">
                                                    <i class="fas fa-edit mr-1"></i> Modifier
                                                </a>
                                                <a href="supprimerReponse.php?id_reponse=<?= $reponse['id_reponse'] ?>" class="btn btn-custom" style="background-color: #dc3545; color: white; btn-danger btn-sm m-1" onclick="return confirm('Confirmer la suppression ?')">
                                                    <i class="fas fa-trash mr-1"></i> Supprimer
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-transparent text-center p-4 text-gray-600">
            © <?= date('Y') ?> EcoTravel. All rights reserved.
        </footer>
    </div>

    <!-- Scripts -->
    <!-- Bootstrap JS (for table) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Sidebar Toggle -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const toggleButton = document.getElementById('toggle-sidebar');
        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-expanded');
            sidebar.classList.toggle('sidebar-collapsed');
        });

        // Dynamic Search
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const table = document.getElementById('responses-table');
            const tableRows = table.getElementsByTagName('tr');

            searchInput.addEventListener('keyup', function() {
                const searchTerm = searchInput.value.toLowerCase();

                for (let i = 1; i < tableRows.length; i++) { // Start from 1 to skip the header row
                    const subjectColumn = tableRows[i].getElementsByClassName('subject')[0];
                    if (subjectColumn) {
                        const subjectText = subjectColumn.textContent.toLowerCase();
                        if (subjectText.indexOf(searchTerm) > -1) {
                            tableRows[i].style.display = ''; // Show the row
                        } else {
                            tableRows[i].style.display = 'none'; // Hide the row
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>