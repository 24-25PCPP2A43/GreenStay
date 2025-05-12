<?php
// View/listReservations.php
require_once __DIR__ . '/../Controller/ReservationController.php';
$ctrl    = new ReservationController();

// Récupérer les paramètres de tri depuis l'URL
$sortBy  = isset($_GET['sort_by']) ? $_GET['sort_by'] : null;
$sortDir = isset($_GET['sort_dir']) ? $_GET['sort_dir'] : 'ASC';

$liste = $ctrl->listReservations($sortBy, $sortDir);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin – Liste des réservations</title>

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

        /* Table styling */
        #reservationTable th, #reservationTable td {
            vertical-align: middle;
        }
    </style>
</head>
<body>

<!-- Toggle sidebar -->
<button class="btn btn-light toggle-btn" id="toggle-btn" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <a href="../FrontOffice/home.php"><i class="fa-solid fa-house-user"></i> Home Page</a>
    <a href="addReservation.php"><i class="fas fa-plus-circle"></i> Ajouter une réservation</a>
    <a href="listLogements.php"><i class="fas fa-building"></i> Logements</a>
    <a href="listReservations.php" class="active"><i class="fas fa-clipboard-list"></i> Réservations</a>
    <a href="statistics.php"><i class="fas fa-chart-line"></i> Statistiques</a>
    <a href="#"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
</div>

<div class="content">
    <!-- Top navbar + search filter -->
    <nav class="navbar navbar-expand-lg shadow bg-white">
        <div class="container-fluid">
            <h1 class="navbar-brand">
                <i class="fas fa-clipboard-list"></i> Gestion des Réservations
            </h1>
            <form class="d-flex ms-auto">
                <input class="form-control me-2" type="search" id="searchInput"
                       placeholder="Recherche..." onkeyup="filterTable()">
            </form>
        </div>
    </nav>

    <!-- Page banner + buttons + sort form -->
    <section class="page-heading">
        <div class="container">
            <h2>Liste des réservations</h2>
            <div class="main-button">
                <a href="addReservation.php" class="btn btn-light">
                    <i class="fas fa-plus"></i> Ajouter
                </a>
                <a href="exportReservationPDF.php" class="btn btn-warning">
                    <i class="fas fa-file-pdf"></i> Exporter PDF
                </a>
            </div>
            <!-- Sort form -->
            <form method="GET" class="row justify-content-center g-2 mt-3">
                <div class="col-auto">
                    <select name="sort_by" class="form-select form-select-sm">
                        <option value="">Trier par…</option>
                        <option value="date_debut" <?= $sortBy==='date_debut'  ? 'selected':''?>>Date début</option>
                        <option value="date_fin"   <?= $sortBy==='date_fin'    ? 'selected':''?>>Date fin</option>
                        <option value="statut"     <?= $sortBy==='statut'      ? 'selected':''?>>Statut</option>
                    </select>
                </div>
                <div class="col-auto">
                    <select name="sort_dir" class="form-select form-select-sm">
                        <option value="ASC"  <?= strtoupper($sortDir)==='ASC'  ? 'selected':''?>>Ascendant</option>
                        <option value="DESC" <?= strtoupper($sortDir)==='DESC' ? 'selected':''?>>Descendant</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button class="btn btn-light btn-sm">Appliquer</button>
                </div>
            </form>
        </div>
    </section>

    <!-- Table -->
    <div class="container py-4">
        <div class="table-responsive">
            <table class="table table-bordered" id="reservationTable">
                <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Logement</th>
                    <th>Client</th>
                    <th>Email</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($liste as $r): ?>
                    <?php
                    $cls = match(strtolower($r['statut'])) {
                        'en attente' => 'bg-warning text-dark',
                        'confirmée'  => 'bg-success text-white',
                        'annulée'    => 'bg-danger text-white',
                        default      => 'bg-secondary text-white'
                    };
                    ?>
                    <tr>
                        <td><?= $r['id_reservation'] ?></td>
                        <td><?= htmlspecialchars($r['titre_logement']) ?></td>
                        <td><?= htmlspecialchars($r['nom_client']) ?></td>
                        <td><?= htmlspecialchars($r['email_client']) ?></td>
                        <td><?= htmlspecialchars($r['date_debut']) ?></td>
                        <td><?= htmlspecialchars($r['date_fin']) ?></td>
                        <td><span class="badge <?= $cls ?>"><?= htmlspecialchars($r['statut']) ?></span></td>
                        <td>
                            <a href="updateReservation.php?id=<?= $r['id_reservation'] ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <button class="btn btn-outline-danger btn-sm" onclick="confirmDelete(<?= $r['id_reservation'] ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
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
    function filterTable() {
        const f = document.getElementById('searchInput').value.toLowerCase();
        document
            .querySelectorAll('#reservationTable tbody tr')
            .forEach(tr => {
                const txt = tr.textContent.toLowerCase();
                tr.style.display = txt.includes(f)?'':'none';
            });
    }
    function confirmDelete(id) {
        Swal.fire({
            title: 'Etes-vous sûr ?',
            text: "Cette action est irréversible !",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer !',
            cancelButtonText: 'Annuler'
        }).then(res => {
            if (res.isConfirmed) {
                window.location.href = 'deleteReservations.php?id=' + id;
            }
        });
    }
</script>
</body>
</html>
