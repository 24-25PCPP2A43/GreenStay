<?php
require_once __DIR__ . '/../../Controller/ReponseController.php';
require_once __DIR__ . '/../../Model/Reponse.php';

// Création de l'instance du contrôleur
$reponseController = new ReponseController();

// Vérification si l'ID de la réponse est passé dans l'URL
if (isset($_GET['id_reponse'])) {
    $id_reponse = intval($_GET['id_reponse']);
    $reponseData = $reponseController->afficherReponseParId($id_reponse);

    if (!$reponseData) {
        echo "Réponse introuvable.";
        exit();
    }
} else {
    echo "ID réponse manquant.";
    exit();
}

// Traitement de la modification après soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_reponse'], $_POST['id_reclamation'], $_POST['message'], $_POST['date_reponse'])) {
        $id_reponse = intval($_POST['id_reponse']);
        $id_reclamation = intval($_POST['id_reclamation']);
        $message = trim($_POST['message']);
        $date_reponse = $_POST['date_reponse'];

        $reponse = new Reponse($id_reclamation, $message, $date_reponse, $id_reponse);
        $reponseController->modifierReponse($reponse);

        header('Location: consulter_reponses.php?success=true');
        exit();
    }
}

// Set default date to today if not provided
$defaultDate = isset($reponseData['date_reponse']) ? date('Y-m-d', strtotime($reponseData['date_reponse'])) : date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoTravel - Modifier Réponse</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap CSS (for form styling) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
        .form-control {
            border: 2px solid #008080; /* Teal border */
            transition: border-color 0.3s ease;
        }
        .form-control:focus {
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
        /* Flatpickr Customization */
        .flatpickr-calendar {
            background: #ffffff;
            border: 2px solid #008080;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange {
            background: #2cae72; /* Teal selection */
            border-color: #2cae72;
            color: white;
        }
        .flatpickr-day.today {
            border-color: #008080; /* Teal for today */
            color: #008080;
        }
        .flatpickr-monthDropdown-months, .flatpickr-current-month span.cur-month {
            color: #1e40af;
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
                        <i class="fas fa-edit mr-2"></i> Modifier une Réponse
                    </h2>

                    <!-- Form -->
                    <form method="POST" action="">
                        <input type="hidden" name="id_reponse" value="<?= htmlspecialchars($reponseData['id_reponse']) ?>">

                        <div class="mb-4">
                            <label for="id_reclamation" class="label-with-icon font-medium mb-2" style="color: #008080;">
                                <i class="fas fa-hashtag" style="color: #008080;"></i> ID Réclamation :
                            </label>
                            <input type="number" class="form-control w-full rounded-lg p-2" id="id_reclamation" name="id_reclamation" value="<?= htmlspecialchars($reponseData['id_reclamation']) ?>" required>
                        </div>

                        <div class="mb-4">
                            <label for="message" class="label-with-icon font-medium mb-2" style="color: #008080;">
                                <i class="fas fa-comment" style="color: #008080;"></i> Message :
                            </label>
                            <textarea class="form-control w-full rounded-lg p-2" id="message" name="message" rows="5" required><?= htmlspecialchars($reponseData['message']) ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="date_reponse" class="label-with-icon font-medium mb-2" style="color: #008080;">
                                <i class="fas fa-calendar" style="color: #008080;"></i> Date Réponse :
                            </label>
                            <input type="text" class="form-control w-full rounded-lg p-2 flatpickr" id="date_reponse" name="date_reponse" value="<?= htmlspecialchars($defaultDate) ?>" required>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-custom" style="background-color: darkred; color: white; px-6 py-3 rounded-lg hover:bg-red-600">
                                <i class="fas fa-save mr-2"></i> Mettre à jour
                            </button>
                        </div>
                    </form>

                    <!-- Retour Button -->
                    <div class="text-center mt-4">
                        <a href="consulter_reponses.php" class="btn btn-custom" style="background-color: blue; color: white; px-4 py-2 rounded hover:bg-blue-600">
                            <i class="fas fa-arrow-left mr-2"></i> Retour aux Réponses
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
    <!-- Bootstrap JS (for form) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- Sidebar Toggle -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const toggleButton = document.getElementById('toggle-sidebar');
        toggleButton.addEventListener('click', () => {
            sidebar.classList.toggle('sidebar-expanded');
            sidebar.classList.toggle('sidebar-collapsed');
        });

        // Initialize Flatpickr
        flatpickr('.flatpickr', {
            dateFormat: 'Y-m-d',
            defaultDate: '<?= htmlspecialchars($defaultDate) ?>',
            enableTime: false,
            locale: {
                firstDayOfWeek: 1 // Start week on Monday
            }
        });
    </script>
</body>
</html>