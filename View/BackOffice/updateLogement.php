<?php
// View/updateLogement.php
require_once __DIR__ . '/../../Controller/LogementController.php';
$logementController = new LogementController();

if (isset($_GET['id'])) {
    $logement = $logementController->showLogement($_GET['id']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $targetDir = __DIR__ . "/../uploads/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
        $imagePath = $targetDir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);
    } else {
        $imagePath = $_POST['existing_image'];
    }

    $logementController->updateLogement(
        $_POST['id_logement'],
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
    header('Location: listLogements.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin – Modifier Logement</title>

    <!-- Bootstrap & icons & SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        :root { --brand-teal: #00c4cc; }

        /* Sidebar */
        .sidebar {
            height: 100%; width: 250px; position: fixed; top:0; left:0;
            background:#343a40; padding-top:60px; transition:0.3s;
        }
        .sidebar a {
            color:#f1f1f1; padding:15px 25px; display:block; text-decoration:none;
        }
        .sidebar a:hover { background:#4c555e }
        .sidebar.hidden { width:0; padding-top:0 }

        /* Content shifts with sidebar */
        .content { margin-left:250px; transition:0.3s; }
        .toggle-btn {
            position: fixed; top:10px; left:250px; z-index:2;
            width:40px; height:40px; border-radius:50%;
            display:flex; align-items:center; justify-content:center; transition:0.3s;
        }

        /* Top nav */
        .navbar.shadow { z-index:1 }

        /* Page banner */
        .page-heading {
            background: var(--brand-teal); color:#fff;
            padding:1.5rem 0; text-align:center;
        }
        .page-heading h2 { margin:0; font-weight:600; }
        .page-heading .main-button { margin-top:.75rem; }

        /* Form styling */
        .form-card { max-width:800px; margin:2rem auto; }
        .form-card .card { border-radius:.5rem; box-shadow:0 2px 8px rgba(0,0,0,0.1); }
        .card-header { background:var(--bs-light); border-bottom:1px solid rgba(0,0,0,0.125); }
    </style>
</head>
<body>

<!-- Toggle sidebar -->
<button class="btn btn-light toggle-btn" id="toggle-btn" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">

<!-- Lien vers export des statistiques PDF -->
    <a href="../FrontOffice/home.php"><i class="fa-solid fa-house-user"></i> Home Page</a>
    <a href="userdash.php" onclick="toggleAddForm(); return false;"><i class="fas fa-user-plus"></i> Ajouter un utilisateur</a>
    <a href="userdash.php" onclick="toggleUserTable(); return false;"><i class="fas fa-users"></i> Afficher les utilisateurs</a>

    <a href="userdash.php" onclick="toggleAccountManagement(); return false;"><i class="fas fa-user-shield"></i> Gestion des comptes</a>
    
    <a href="statistics.php"><i class="fas fa-chart-line"></i> Statistiques</a>
    <a href="listReservations.php"><i class="fas fa-clipboard-list"></i> Gestion des Réservations</a>
    <a href="listLogements.php" class="active"><i class="fas fa-building"></i> Gestion des Logements</a>
    <a href="#reclamations" onclick="toggleReclamations(); return false;">
        <i class="fas fa-envelope-open-text"></i> Réclamations
    </a>
   <a href="http://localhost/projet/View/BackOffice/consulter_tendances.php">tendance reclamation</a>
   <a href="http://localhost/projet/View/BackOffice/consulter_stat.php">stat reclamation</a>
   
    



    <!-- Nouveau bouton vers la page services -->
    <a href="/projet/public/index.php?action=back_office">Services</a>
    
    

</div>

<div class="content">
    <!-- Top navbar -->
    <nav class="navbar navbar-expand-lg shadow bg-white">
        <div class="container-fluid">
            <h1 class="navbar-brand">
                <i class="fas fa-building"></i> Gestion des Logements
            </h1>
        </div>
    </nav>

    <!-- Page banner + buttons -->
    <section class="page-heading">
        <div class="container">
            <h2>Modifier un logement</h2>
            <div class="main-button">
                <a href="listLogements.php" class="btn btn-light">
                    <i class="fas fa-arrow-left"></i> Retour à la liste
                </a>
            </div>
        </div>
    </section>

    <!-- Form Card -->
    <div class="container py-4">
        <div class="form-card">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Détails du logement</h4>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id_logement" value="<?= $logement['id_logement'] ?>"/>
                        <input type="hidden" name="existing_image" value="<?= htmlspecialchars($logement['image']) ?>"/>

                        <div class="mb-3">
                            <label class="form-label">Titre</label>
                            <input type="text" name="titre" class="form-control"
                                   value="<?= htmlspecialchars($logement['titre']) ?>" required/>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($logement['description']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Adresse</label>
                            <input type="text" name="adresse" class="form-control"
                                   value="<?= htmlspecialchars($logement['adresse']) ?>" required/>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Ville</label>
                                <input type="text" name="ville" class="form-control"
                                       value="<?= htmlspecialchars($logement['ville']) ?>" required/>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Type</label>
                                <select name="type" class="form-select" required>
                                    <?php foreach (['Appartement','Maison','Villa','Studio','Chambre'] as $type): ?>
                                        <option value="<?= $type ?>" <?= $logement['type']==$type?'selected':'' ?>>
                                            <?= $type ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Prix / nuit (€)</label>
                                <input type="number" name="prix_par_nuit" step="0.01" class="form-control"
                                       value="<?= htmlspecialchars($logement['prix_par_nuit']) ?>" required/>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Capacité</label>
                                <input type="number" name="capacite" class="form-control"
                                       value="<?= htmlspecialchars($logement['capacite']) ?>" required/>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Disponibilité</label>
                                <div class="form-check mt-2">
                                    <input type="checkbox" class="form-check-input" name="disponibilite" id="disponibilite" <?= $logement['disponibilite']?'checked':'' ?>/>
                                    <label class="form-check-label" for="disponibilite">Disponible</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Image</label>
                            <input type="file" name="image" class="form-control"/>
                            <?php if ($logement['image']): ?>
                                <div class="mt-2 border rounded p-2 text-center bg-light">
                                    <p class="small text-muted mb-1">Image actuelle:</p>
                                    <img src="<?= htmlspecialchars($logement['image']) ?>" class="img-fluid" style="max-height:200px;">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="py-3 text-center">
        © <?= date('Y') ?> WoOx Travel. Tous droits réservés.
    </footer>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function toggleSidebar() {
        const sb = document.getElementById('sidebar');
        const content = document.querySelector('.content');
        const btn = document.getElementById('toggle-btn');
        sb.classList.toggle('hidden');
        const hidden = sb.classList.contains('hidden');
        content.style.marginLeft = hidden?'0':'250px';
        btn.style.left = hidden?'0':'250px';
    }
</script>
</body>
</html>