<?php
require_once '../Controller/LogementController.php';
require_once(__DIR__ . '/../Model/Config.php');

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Gestion de l'upload d'image
    $imagePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "../uploads/";  // Dossier de destination
        // Créer le dossier s'il n'existe pas
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $imagePath = $targetDir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);
    }

    $logement = new Logement(
        null,
        $_POST['titre'],
        $_POST['description'],
        $_POST['adresse'],
        $_POST['ville'],
        $_POST['type'],
        $_POST['prix_par_nuit'],
        $_POST['capacite'],
        $imagePath,
        isset($_POST['disponibilite']) ? 1 : 0
    );

    $logementController = new LogementController();
    $logementController->addLogement($logement);

    header('Location: listLogements.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un logement</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- CSS externes -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />
    <link href="../../assets/css/dashboard.css" rel="stylesheet" />

    <style>
        /* Sidebar Styles */
        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            z-index: 1;
            top: 0;
            left: 0;
            background-color: #343a40;
            overflow-x: hidden;
            padding-top: 60px;
            transition: 0.3s;
        }

        .sidebar a {
            padding: 15px 25px;
            text-decoration: none;
            font-size: 16px;
            color: #f1f1f1;
            display: block;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background-color: #4c555e;
        }

        .sidebar.hidden {
            width: 0;
            padding-top: 0;
        }

        .content {
            margin-left: 250px;
            padding: 0;
            transition: 0.3s;
        }

        .toggle-btn {
            position: fixed;
            left: 250px;
            top: 10px;
            z-index: 2;
            transition: 0.3s;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Form Styles */
        .form-section {
            max-width: 700px;
            margin: 0 auto;
        }

        .form-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 20px;
        }

        .page-title {
            color: #343a40;
            text-align: center;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .form-label {
            font-weight: 500;
        }

        .btn-submit {
            background-color: #28a745;
            border: none;
            padding: 10px 0;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-submit:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>
<!-- Bouton menu -->
<button class="btn btn-light toggle-btn" id="toggle-btn" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>

<!-- Menu latéral -->
<div class="sidebar" id="sidebar">
    <a href="../FrontOffice/home.php"><i class="fa-solid fa-house-user"></i> Home Page</a>
    <a href="addLogement.php" class="active"><i class="fas fa-plus-circle"></i> Ajouter un logement</a>
    <a href="listLogements.php"><i class="fas fa-building"></i> Logements</a>
    <a href="listReservations.php"><i class="fas fa-clipboard-list"></i> Réservations</a>
    <a href="statistics.php"><i class="fas fa-chart-line"></i> Statistiques</a>
    <a href="#"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
</div>

<!-- Contenu principal -->
<div class="content">
    <!-- Barre de navigation -->
    <nav class="navbar navbar-expand-lg shadow">
        <div class="container-fluid">
            <h1 class="navbar-brand">
                <img src="../../assets/svg/dash-cryptocurrency-coin-svgrepo-com.svg" class="action-icon" alt="Dashboard" />
                Gestion des Logements
            </h1>
            <form class="d-flex">
                <input class="form-control me-2" type="search" placeholder="Recherche..." aria-label="Search">
            </form>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="form-container">
                    <h3 class="page-title">Ajouter un nouveau logement</h3>

                    <form method="POST" enctype="multipart/form-data" class="form-section" id="addLogementForm">
                        <div class="mb-3">
                            <label for="titre" class="form-label">Titre</label>
                            <input type="text" class="form-control" id="titre" name="titre" required minlength="3" maxlength="100" />
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required minlength="10" maxlength="1000"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="adresse" class="form-label">Adresse</label>
                            <input type="text" class="form-control" id="adresse" name="adresse" required maxlength="255" />
                        </div>

                        <div class="mb-3">
                            <label for="ville" class="form-label">Ville</label>
                            <input type="text" class="form-control" id="ville" name="ville" required maxlength="100" />
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Type de logement</label>
                            <select class="form-control" id="type" name="type" required>
                                <option value="">-- Choisissez un type --</option>
                                <option value="Appartement">Appartement</option>
                                <option value="Maison">Maison</option>
                                <option value="Villa">Villa</option>
                                <option value="Studio">Studio</option>
                                <option value="Chambre">Chambre</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="prix_par_nuit" class="form-label">Prix par nuit (€)</label>
                            <input type="number" class="form-control" id="prix_par_nuit" name="prix_par_nuit" min="1" step="0.01" required />
                        </div>

                        <div class="mb-3">
                            <label for="capacite" class="form-label">Capacité (nombre de personnes)</label>
                            <input type="number" class="form-control" id="capacite" name="capacite" min="1" required />
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">Photo du logement</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*" />
                        </div>

                        <div class="form-check mb-4">
                            <input type="checkbox" class="form-check-input" id="disponibilite" name="disponibilite" checked />
                            <label class="form-check-label" for="disponibilite">Disponible</label>
                        </div>

                        <button type="submit" class="btn btn-success w-100 btn-submit">Ajouter le logement</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light mt-5">
        <div class="container py-3 text-center">
            <p class="mb-0">© 2025 WoOx Travel Company. Tous droits réservés.</p>
        </div>
    </footer>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const content = document.querySelector('.content');
        const toggleBtn = document.getElementById('toggle-btn');

        sidebar.classList.toggle('hidden');
        const isHidden = sidebar.classList.contains('hidden');

        content.style.marginLeft = isHidden ? '0' : '250px';
        toggleBtn.style.left = isHidden ? '0' : '250px';
    }

    document.getElementById("addLogementForm").addEventListener("submit", function(event) {
        let formValid = true;
        const titre = document.getElementById("titre");
        if (titre.value.length < 3 || titre.value.length > 100) {
            Swal.fire({
                icon: 'error',
                title: 'Erreur de validation',
                text: 'Le titre doit avoir entre 3 et 100 caractères.'
            });
            formValid = false;
        }
        const description = document.getElementById("description");
        if (description.value.length < 10 || description.value.length > 1000) {
            Swal.fire({
                icon: 'error',
                title: 'Erreur de validation',
                text: 'La description doit avoir entre 10 et 1000 caractères.'
            });
            formValid = false;
        }
        const prixParNuit = document.getElementById("prix_par_nuit");
        if (prixParNuit.value <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Erreur de validation',
                text: 'Le prix par nuit doit être supérieur à 0.'
            });
            formValid = false;
        }
        const capacite = document.getElementById("capacite");
        if (capacite.value <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Erreur de validation',
                text: 'La capacité doit être supérieure à 0.'
            });
            formValid = false;
        }
        if (!formValid) {
            event.preventDefault();
        }
    });
</script>
</body>
</html>