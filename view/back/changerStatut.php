<?php
include '../../config/database.php'; // Inclure la connexion à la base de données

// Vérifier si l'ID de la réclamation est passé dans l'URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Récupérer les informations de la réclamation
    $sql = "SELECT * FROM reclamations WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $reclamation = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier si la réclamation existe
    if (!$reclamation) {
        echo "Réclamation non trouvée.";
        exit;
    }

    // Vérifier si le formulaire est soumis
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nouveauStatut = $_POST['statut']; // Récupérer le statut sélectionné

        // Requête SQL pour mettre à jour le statut
        $sql = "UPDATE reclamations SET statut = :statut WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':statut', $nouveauStatut, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        // Exécuter la mise à jour
        if ($stmt->execute()) {
            echo "Statut mis à jour avec succès.";
            header('Location: consulter_reclamations.php?success=true'); // Redirige vers la page des réclamations avec un message de succès
            exit();
        } else {
            $error = "Erreur lors de la mise à jour du statut.";
        }
    }
} else {
    echo "Aucun ID de réclamation trouvé.";
    exit;
}

// Liste des statuts possibles pour le filtre
$statuts = [
    'Nouveau' => 'Nouveau',
    'En cours de traitement' => 'En cours de traitement',
    'Traité' => 'Traité',
    'Résolu' => 'Résolu',
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoTravel - Changer le Statut</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap CSS (for form) -->
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
                        <i class="fas fa-edit mr-2"></i> Changer le statut de la réclamation #<?= htmlspecialchars($reclamation['id']) ?>
                    </h2>

                    <!-- Affichage d'erreur éventuelle -->
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger text-center">
                            <i class="fas fa-exclamation-circle mr-2"></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Formulaire pour changer le statut -->
                    <form method="POST" action="" class="max-w-lg mx-auto">
                        <div class="mb-4">
                            <label for="statut" class="label-with-icon font-medium" style="color: #008080;">
                                <i class="fas fa-tag" style="color: #008080;"></i> Statut :
                            </label>
                            <select name="statut" id="statut" class="form-select rounded-lg p-2 w-full" required>
                                <?php foreach ($statuts as $key => $value): ?>
                                    <option value="<?= htmlspecialchars($key) ?>" <?= $reclamation['statut'] == $key ? 'selected' : '' ?>><?= htmlspecialchars($value) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-custom" style="background-color: darkred; color: white; px-5 py-2 rounded hover:bg-red-600">
                                <i class="fas fa-check mr-2"></i> Changer le statut
                            </button>
                        </div>
                    </form>

                    <!-- Retour Button -->
                    <div class="text-center mt-4">
                        <a href="consulter_reclamations.php" class="btn btn-custom" style="background-color: blue; color: white; px-4 py-2 rounded hover:bg-blue-600">
                            <i class="fas fa-arrow-left mr-2"></i> Retour
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