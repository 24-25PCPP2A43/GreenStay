<?php
// View/listLogements.php
require_once __DIR__ . '/../../Controller/LogementController.php';
$logementController = new LogementController();
$liste = $logementController->listLogements();
$ctrl   = new LogementController();

// Récupérer les paramètres de tri depuis l’URL
$sortBy  = isset($_GET['sort_by']) ? $_GET['sort_by'] : null;
$sortDir = isset($_GET['sort_dir']) ? $_GET['sort_dir'] : 'ASC';

$liste = $ctrl->listLogements($sortBy, $sortDir);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin – Liste des logements</title>

    <!-- Google Fonts -->
    <link
            href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
            rel="stylesheet"
    />

    <!-- Bootstrap core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet" />

    <style>
        :root { --brand-teal: #00c4cc; }

        /* Sidebar Styles from the first code */
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

        /* Page banner */
        .page-heading {
            background-color: var(--brand-teal);
            color: #fff;
            padding: 1.5rem 0;
            text-align: center;
        }
        .page-heading h2 {
            margin: 0;
            font-weight: 600;
        }
        .page-heading .main-button {
            margin-top: 0.75rem;
        }

        /* Grid of cards */
        .listings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 2rem;
            padding: 2rem 0;
        }
        .listing-card {
            background: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform .2s;
        }
        .listing-card:hover { transform: translateY(-4px); }
        .listing-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        .listing-body {
            padding: 1rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .listing-title { font-size: 1.2rem; font-weight: 600; margin-bottom: 0.25rem; }
        .listing-meta { font-size: 0.9rem; color: #666; margin-bottom: 0.5rem; }
        .listing-price { color: var(--brand-teal); font-weight: 600; margin-bottom: 0.5rem; }
        .badge-available { background: #28c76f; color: #fff; }
        .badge-unavailable { background: #ea5455; color: #fff; }
        .action-btns {
            margin-top: auto;
            display: flex;
            gap: 0.5rem;
        }
        .action-btns .btn { flex: 1; }
    </style>
</head>
<body>

<!-- Bouton menu -->
<button class="btn btn-light toggle-btn" id="toggle-btn" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>

<!-- Menu latéral -->
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






    <a href="../../Controller/logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
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
                <input class="form-control me-2" type="search" id="searchInput" placeholder="Recherche..." aria-label="Search" onkeyup="filterCards()">
            </form>
        </div>
    </nav>

    <!-- Page Banner -->
    <section class="page-heading">
        <div class="container">
            <h2>Liste des logements</h2>
            <div class="main-button">
                <a href="addLogement.php" class="btn btn-light"><i class="fas fa-plus"></i> Ajouter un logement</a>
                <a href="exportLogementsPDF.php" class="btn btn-warning">
                    <i class="fas fa-file-pdf"></i> Exporter en PDF
                </a>

            </div>
            <div class="container">


                <!-- Formulaire de tri -->
                <form method="GET" class="d-flex justify-content-center gap-2 mt-3">
                    <label class="text-white mb-0">Trier par :</label>
                    <select name="sort_by" class="form-select w-auto">
                        <option value="">—</option>
                        <option value="titre"         <?= $sortBy==='titre'         ? 'selected':'' ?>>Titre</option>
                        <option value="ville"         <?= $sortBy==='ville'         ? 'selected':'' ?>>Ville</option>
                        <option value="type"          <?= $sortBy==='type'          ? 'selected':'' ?>>Type</option>
                        <option value="prix_par_nuit" <?= $sortBy==='prix_par_nuit' ? 'selected':'' ?>>Prix</option>
                        <option value="capacite"      <?= $sortBy==='capacite'      ? 'selected':'' ?>>Capacité</option>
                    </select>
                    <select name="sort_dir" class="form-select w-auto">
                        <option value="ASC"  <?= strtoupper($sortDir)==='ASC'  ? 'selected':'' ?>>Ascendant</option>
                        <option value="DESC" <?= strtoupper($sortDir)==='DESC' ? 'selected':'' ?>>Descendant</option>
                    </select>
                    <button type="submit" class="btn btn-light btn-sm">Appliquer</button>
                </form>
            </div>

        </div>
    </section>


    <!-- Cards Grid -->
    <div class="main-content">
        <div class="container">
            <div class="listings-grid">
                <?php foreach($liste as $lgm): ?>
                    <div class="listing-card">
                        <?php if($lgm['image']): ?>
                            <img src="<?= htmlspecialchars($lgm['image']) ?>" class="listing-image" alt="">
                        <?php else: ?>
                            <img src="../templatemo_580_woox_travel/assets/images/placeholder.jpg" class="listing-image" alt="No image">
                        <?php endif; ?>

                        <div class="listing-body">
                            <div class="listing-title"><?= htmlspecialchars($lgm['titre']) ?></div>
                            <div class="listing-meta">
                                <?= htmlspecialchars($lgm['ville']) ?> | <?= htmlspecialchars($lgm['type']) ?>
                            </div>
                            <div class="listing-price">
                                <?= number_format($lgm['prix_par_nuit'], 2, ',', ' ') ?> € / nuit
                            </div>
                            <small>Capacité : <?= htmlspecialchars($lgm['capacite']) ?> personnes</small>
                            <div class="mt-2">
                    <span class="badge <?= $lgm['disponibilite'] ? 'badge-available' : 'badge-unavailable' ?>">
                      <?= $lgm['disponibilite'] ? 'Disponible' : 'Indisponible' ?>
                    </span>
                            </div>

                            <div class="action-btns">
                                <a href="updateLogement.php?id=<?= $lgm['id_logement'] ?>"
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-pencil-alt"></i> Modifier
                                </a>
                                <button class="btn btn-outline-danger"
                                        onclick="confirmDelete(<?= $lgm['id_logement']; ?>)">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container text-center py-3">
            © 2025 WoOx Travel Company. Tous droits réservés.
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

    function filterCards() {
        const filter = document.getElementById("searchInput").value.toLowerCase();
        const cards = document.querySelectorAll(".listing-card");

        cards.forEach(card => {
            const title = card.querySelector(".listing-title").textContent.toLowerCase();
            const meta = card.querySelector(".listing-meta").textContent.toLowerCase();
            const visible = title.includes(filter) || meta.includes(filter);
            card.style.display = visible ? "" : "none";
        });
    }

    function confirmDelete(logementId) {
        Swal.fire({
            title: 'Etes-vous sûr ?',
            text: "Cette action est irréversible !",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer !',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'deleteLogement.php?id=' + logementId;
            }
        });
    }
</script>

</body>
</html>