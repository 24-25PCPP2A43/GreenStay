<?php
// View/updateReservation.php
require_once __DIR__ . '/../../Controller/ReservationController.php';
require_once __DIR__ . '/../../Controller/LogementController.php';

$reservationController = new ReservationController();
$logementController   = new LogementController();

// Load existing reservation and all logements
if (!isset($_GET['id'])) {
    header('Location: listReservations.php');
    exit;
}
$reservation = $reservationController->showReservation($_GET['id']);
$logements   = $logementController->listLogements();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Only re‐check availability if logement or dates changed
    $needCheck = (
        $_POST['id_logement']   != $reservation['id_logement'] ||
        $_POST['date_debut']    != $reservation['date_debut'] ||
        $_POST['date_fin']      != $reservation['date_fin']
    );
    $canUpdate = true;
    if ($needCheck) {
        $canUpdate = $reservationController->checkAvailability(
            $_POST['id_logement'],
            $_POST['date_debut'],
            $_POST['date_fin']
        );
    }
    if ($canUpdate) {
        $reservationController->updateReservation(
            $_POST['id_reservation'],
            $_POST['id_logement'],
            $_POST['nom_client'],
            $_POST['email_client'],
            $_POST['date_debut'],
            $_POST['date_fin'],
            $_POST['statut']
        );
        header('Location: listReservations.php');
        exit;
    } else {
        $error = "Ce logement n'est pas disponible pour les dates sélectionnées.";
        // reload fresh data
        $reservation = $reservationController->showReservation($_POST['id_reservation']);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title>Modifier une réservation</title>

    <!-- Google Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>

    <!-- Bootstrap & SweetAlert -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet"/>

    <style>
        :root { --brand-teal: #00c4cc; }

        /* Sidebar */
        .sidebar {
            height:100%; width:250px; position:fixed; top:0; left:0;
            background:#343a40; padding-top:60px; transition:left .3s;
        }
        .sidebar.hidden { left:-250px; }
        .sidebar a {
            display:block; color:#f1f1f1; padding:15px 25px;
            text-decoration:none; transition:background .3s;
        }
        .sidebar a:hover { background:#4c555e; }

        /* Content */
        .content { margin-left:250px; transition:margin-left .3s; }
        .content.full { margin-left:0; }

        /* Toggle */
        .toggle-btn {
            position:fixed; top:10px; left:250px; z-index:2;
            width:40px; height:40px; border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            transition:left .3s;
        }

        /* Page-heading */
        .page-heading {
            background:var(--brand-teal); color:#fff;
            text-align:center; padding:1.5rem 0;
        }
        .page-heading h2 { margin:0; font-weight:600; }
        .page-heading .main-button { margin-top:.75rem; }

        /* Form Card */
        .form-card {
            background:#fff; border-radius:.5rem;
            box-shadow:0 2px 8px rgba(0,0,0,.1);
            overflow:hidden; margin-top:2rem;
        }
        .form-card .card-header {
            background:var(--brand-teal); color:#fff;
            text-align:center; font-size:1.25rem; padding:1rem;
        }
        .form-card .card-body { padding:2rem; }
    </style>
</head>
<body>

<!-- Hamburger -->
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

<!-- Main area -->
<div class="content" id="content">

    <!-- Top navbar -->
    <nav class="navbar navbar-expand-lg shadow">
        <div class="container-fluid">
            <h1 class="navbar-brand">
                Gestion des Réservations
            </h1>
        </div>
    </nav>

    <!-- Heading -->
    <section class="page-heading">
        <div class="container">
            <h2>Modifier une réservation</h2>
            <div class="main-button">
                <a href="listReservations.php" class="btn btn-light">
                    <i class="fas fa-arrow-left"></i> Retour à la liste
                </a>
            </div>
        </div>
    </section>

    <!-- Form card -->
    <div class="container">
        <div class="form-card">
            <div class="card-header">Détails de la réservation</div>
            <div class="card-body">

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" id="updateReservationForm" novalidate>
                    <input type="hidden" name="id_reservation" value="<?= $reservation['id_reservation'] ?>">

                    <div class="mb-3">
                        <label for="id_logement" class="form-label">Logement</label>
                        <select name="id_logement" id="id_logement" class="form-select" required>
                            <?php foreach($logements as $l): ?>
                                <option value="<?= $l['id_logement'] ?>"
                                    <?= $l['id_logement']==$reservation['id_logement']?'selected':'' ?>>
                                    <?= htmlspecialchars($l['titre']) ?> —
                                    <?= htmlspecialchars($l['ville']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom_client" class="form-label">Nom du client</label>
                            <input type="text" name="nom_client" id="nom_client"
                                   class="form-control"
                                   value="<?= htmlspecialchars($reservation['nom_client']) ?>"
                                   minlength="3" maxlength="100" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email_client" class="form-label">Email du client</label>
                            <input type="email" name="email_client" id="email_client"
                                   class="form-control"
                                   value="<?= htmlspecialchars($reservation['email_client']) ?>"
                                   required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date_debut" class="form-label">Date d'arrivée</label>
                            <input type="date" name="date_debut" id="date_debut"
                                   class="form-control"
                                   value="<?= $reservation['date_debut'] ?>"
                                   required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date_fin" class="form-label">Date de départ</label>
                            <input type="date" name="date_fin" id="date_fin"
                                   class="form-control"
                                   value="<?= $reservation['date_fin'] ?>"
                                   required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="statut" class="form-label">Statut</label>
                        <select name="statut" id="statut" class="form-select" required>
                            <?php foreach(['en attente','confirmée','annulée'] as $st): ?>
                                <option value="<?= $st ?>"
                                    <?= $reservation['statut']==$st?'selected':'' ?>>
                                    <?= ucfirst($st) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-warning w-100">
                        <i class="fas fa-pencil-alt"></i> Modifier
                    </button>
                </form>

            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-3">
        © 2025 WoOx Travel Company. Tous droits réservés.
    </footer>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Sidebar toggle
    function toggleSidebar(){
        const sb=document.getElementById('sidebar'),
            ct=document.getElementById('content'),
            btn=document.getElementById('toggle-btn');
        sb.classList.toggle('hidden');
        ct.classList.toggle('full');
        btn.style.left = sb.classList.contains('hidden')?'0':'250px';
    }
    // Client validation
    document.getElementById('updateReservationForm')
        .addEventListener('submit', function(e){
            const nom = document.getElementById('nom_client'),
                mail = document.getElementById('email_client'),
                d0 = new Date(document.getElementById('date_debut').value),
                d1 = new Date(document.getElementById('date_fin').value);
            if(nom.value.length<3||nom.value.length>100){
                Swal.fire('Erreur','Nom entre 3 et 100 caractères','error'); e.preventDefault(); return;
            }
            if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(mail.value)){
                Swal.fire('Erreur','Email invalide','error'); e.preventDefault(); return;
            }
            if(d1<d0){
                Swal.fire('Erreur','Date départ après date arrivée','error'); e.preventDefault(); return;
            }
        });
</script>
</body>
</html>
