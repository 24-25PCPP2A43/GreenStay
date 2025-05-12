<?php
// View/statistics.php
require_once __DIR__ . '/../../Controller/LogementController.php';
require_once __DIR__ . '/../../Controller/ReservationController.php';
require_once __DIR__ . '/../../Controller/StatisticsController.php';

$statsController = new StatisticsController();

// Récupérer les statistiques générales
$totalLogements = $statsController->getTotalLogements();
$totalReservations = $statsController->getTotalReservations();
$occupancyRate = $statsController->getOccupancyRate();
$averageStayDuration = $statsController->getAverageStayDuration();
$mostPopularCity = $statsController->getMostPopularCity();
$revenueByMonth = $statsController->getRevenueByMonth();
$reservationsByStatus = $statsController->getReservationsByStatus();
$topLogements = $statsController->getTopLogements(5);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin – Statistiques</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet" />

    <!-- Bootstrap core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="../../assets/css/dashboard.css" rel="stylesheet" />

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root { --brand-teal: #00c4cc; }

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

        /* Stats cards */
        .stats-card {
            background: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            height: 100%;
            transition: transform .2s;
        }
        .stats-card:hover { transform: translateY(-5px); }
        .stats-card .icon {
            font-size: 2.5rem;
            color: var(--brand-teal);
            margin-bottom: 1rem;
        }
        .stats-card .title {
            font-size: 1rem;
            color: #666;
            margin-bottom: 0.5rem;
        }
        .stats-card .value {
            font-size: 1.8rem;
            font-weight: 600;
            color: #333;
        }

        /* Chart containers */
        .chart-container {
            background: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .chart-container h3 {
            margin-top: 0;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
            color: #444;
        }

        /* Top logements table */
        .top-table {
            background: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .top-table h3 {
            margin-top: 0;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
            color: #444;
        }
        .top-table table {
            width: 100%;
        }
        .top-table th {
            color: #555;
            font-weight: 600;
        }
        .top-table td, .top-table th {
            padding: 0.75rem;
            vertical-align: middle;
        }
        .top-table tbody tr:nth-child(odd) {
            background-color: rgba(0, 196, 204, 0.05);
        }
        .top-table tbody tr:hover {
            background-color: rgba(0, 196, 204, 0.1);
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

<!-- Contenu principal -->
<div class="content">
    <!-- Barre de navigation -->
    <nav class="navbar navbar-expand-lg shadow">
        <div class="container-fluid">
            <h1 class="navbar-brand">
                <img src="../../assets/svg/dash-cryptocurrency-coin-svgrepo-com.svg" class="action-icon" alt="Dashboard" />
                Tableau de bord statistique
            </h1>
        </div>
    </nav>

    <!-- Page Banner -->
    <section class="page-heading">
        <div class="container">
            <h2>Statistiques WoOx Travel</h2>
        </div>
    </section>
    <div class="container mt-4">
        <div class="d-flex justify-content-end mb-4">
            <a href="exportStatisticsPDF.php" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> Exporter en PDF
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="container my-4">
        <div class="row g-4">
            <!-- Card 1 -->
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="title">Total des logements</div>
                    <div class="value"><?= $totalLogements ?></div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="title">Total des réservations</div>
                    <div class="value"><?= $totalReservations ?></div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="icon">
                        <i class="fas fa-percentage"></i>
                    </div>
                    <div class="title">Taux d'occupation</div>
                    <div class="value"><?= number_format($occupancyRate, 1) ?>%</div>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="col-md-3">
                <div class="stats-card text-center">
                    <div class="icon">
                        <i class="fas fa-bed"></i>
                    </div>
                    <div class="title">Durée moyenne de séjour</div>
                    <div class="value"><?= number_format($averageStayDuration, 1) ?> jours</div>
                </div>
            </div>

            <!-- Card 5 -->
            <div class="col-md-6">
                <div class="stats-card text-center">
                    <div class="icon">
                        <i class="fas fa-city"></i>
                    </div>
                    <div class="title">Ville la plus populaire</div>
                    <div class="value"><?= htmlspecialchars($mostPopularCity['ville']) ?></div>
                    <div class="small text-muted mt-2"><?= $mostPopularCity['count'] ?> réservations</div>
                </div>
            </div>

            <!-- Card 6 -->
            <div class="col-md-6">
                <div class="stats-card text-center">
                    <div class="icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="title">Revenu total estimé</div>
                    <div class="value"><?= number_format($statsController->getTotalRevenue(), 2, ',', ' ') ?> €</div>
                    <div class="small text-muted mt-2">Basé sur les réservations confirmées</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="container my-4">
        <div class="row">
            <!-- Revenue by Month Chart -->
            <div class="col-md-6">
                <div class="chart-container">
                    <h3>Revenus par mois</h3>
                    <!-- Ajout d'un div avec une hauteur fixe pour contenir le graphique -->
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Reservations by Status Chart -->
            <div class="col-md-6">
                <div class="chart-container">
                    <h3>Réservations par statut</h3>
                    <!-- Ajout d'un div avec une hauteur fixe pour contenir le graphique -->
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Logements Table -->
    <div class="container my-4">
        <div class="top-table">
            <h3>Top 5 des logements les plus réservés</h3>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Rang</th>
                        <th>Logement</th>
                        <th>Ville</th>
                        <th>Réservations</th>
                        <th>Revenus générés</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $rank = 1; foreach($topLogements as $logement): ?>
                        <tr>
                            <td><?= $rank++ ?></td>
                            <td><?= htmlspecialchars($logement['titre']) ?></td>
                            <td><?= htmlspecialchars($logement['ville']) ?></td>
                            <td><?= $logement['reservation_count'] ?></td>
                            <td><?= number_format($logement['revenue'], 2, ',', ' ') ?> €</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
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
    // Toggle Sidebar (code inchangé)
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const content = document.querySelector('.content');
        const toggleBtn = document.getElementById('toggle-btn');

        sidebar.classList.toggle('hidden');
        const isHidden = sidebar.classList.contains('hidden');

        content.style.marginLeft = isHidden ? '0' : '250px';
        toggleBtn.style.left = isHidden ? '0' : '250px';
    }

    // Revenue Chart avec options de redimensionnement améliorées
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueChart = new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($revenueByMonth, 'month_name')) ?>,
            datasets: [{
                label: 'Revenus (€)',
                data: <?= json_encode(array_column($revenueByMonth, 'total_revenue')) ?>,
                backgroundColor: 'rgba(0, 196, 204, 0.7)',
                borderColor: 'rgba(0, 196, 204, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // Important pour contrôler la hauteur
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' €';
                        }
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45, // Rotation des labels pour éviter le chevauchement
                        minRotation: 45  // Forcer la rotation minimale
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Status Chart avec options de redimensionnement améliorées
    const statusData = <?= json_encode($reservationsByStatus) ?>;
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: statusData.map(item => item.statut),
            datasets: [{
                data: statusData.map(item => item.count),
                backgroundColor: [
                    'rgba(255, 193, 7, 0.8)', // en attente
                    'rgba(40, 167, 69, 0.8)',  // confirmée
                    'rgba(220, 53, 69, 0.8)'   // annulée
                ],
                borderColor: [
                    'rgba(255, 193, 7, 1)',
                    'rgba(40, 167, 69, 1)',
                    'rgba(220, 53, 69, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // Important pour contrôler la hauteur
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 12, // Réduire la taille des boîtes de légende
                        padding: 10   // Augmenter l'espacement entre les labels
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((value / total) * 100);
                            return `${label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
</script>

</body>
</html>