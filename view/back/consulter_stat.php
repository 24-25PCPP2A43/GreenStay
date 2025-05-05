<?php
include '../../config/database.php';

// Requête pour récupérer les statistiques des réclamations par mois, année et statut
$sql_statistiques = "SELECT DATE_FORMAT(r.date_creation, '%Y-%m') AS mois_annee, r.statut, COUNT(r.id) AS nombre_reclamations
                     FROM reclamations r
                     GROUP BY DATE_FORMAT(r.date_creation, '%Y-%m'), r.statut ORDER BY DATE_FORMAT(r.date_creation, '%Y-%m') DESC";

try {
    $stmt_stats = $conn->prepare($sql_statistiques);
    $stmt_stats->execute();
    $stats = $stmt_stats->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de la requête pour les statistiques : " . $e->getMessage());
}

// Préparation des données pour le graphique
$labels = [];
$statistiques_par_statut = [
    'Nouveau' => [],
    'En cours de traitement' => [],
    'Traité' => [],
    'Résolu' => [],
];

// Préparation des couleurs pour chaque statut
$color_palette = [
    'Nouveau' => '#FF0000',  // Red
    'En cours de traitement' => '#10B981',  // Emerald Green
    'Traité' => '#3B82F6',  // Bright Blue
    'Résolu' => '#6B7280',  // Gray (as seen in screenshot, can adjust to a more vibrant color if desired)
];

// Remplissage des statistiques par statut
foreach ($stats as $stat) {
    if (!in_array(date("M Y", strtotime($stat['mois_annee'])), $labels)) {
        $labels[] = date("M Y", strtotime($stat['mois_annee']));
    }
    $statistiques_par_statut[$stat['statut']][] = $stat['nombre_reclamations'];
}

// Ajuster les statistiques pour les mois manquants dans chaque statut
foreach ($statistiques_par_statut as $statut => $data) {
    foreach ($labels as $index => $label) {
        if (!isset($data[$index])) {
            $statistiques_par_statut[$statut][$index] = 0;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoTravel - Reclamations Statistics</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom Styles (You can move these to a separate CSS file) -->
    <style>
        body {
            background: url('../../assets/images/best-01.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Poppins', sans-serif;
        }

        .content-container {
            background: rgba(255, 255, 255, 0.7);
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .chart-container {
            position: relative;
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        #statistiquesChart {
            width: 100% !important;
            height: 300px !important;
        }
    </style>
</head>
<body>
    <div class="container mx-auto h-screen flex flex-col justify-center items-center">
        <div class="content-container">
            <h1 class="text-2xl font-semibold mb-4 text-center">
                <i class="fas fa-chart-bar mr-2"></i> Reclamations Statistics
            </h1>
            <div class="chart-container">
                <canvas id="statistiquesChart"></canvas>
            </div>
            <div class="text-center mt-4">
                <a href="consulter_reclamations.php" class="btn btn-blue text-white px-4 py-2 rounded hover:bg-blue-600">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Reclamations
                </a>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('statistiquesChart').getContext('2d');
        const statistiquesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [
                    {
                        label: 'Nouveau',
                        data: <?= json_encode($statistiques_par_statut['Nouveau']) ?>,
                        backgroundColor: 'rgba(255, 0, 0, 0.5)', // Tomato Red
                        borderColor: '#FF0000',
                        borderWidth: 1
                    },
                    {
                        label: 'En cours de traitement',
                        data: <?= json_encode($statistiques_par_statut['En cours de traitement']) ?>,
                        backgroundColor: 'rgba(16, 185, 129, 0.5)', // Emerald Green
                        borderColor: '#10B981',
                        borderWidth: 1
                    },
                    {
                        label: 'Traité',
                        data: <?= json_encode($statistiques_par_statut['Traité']) ?>,
                        backgroundColor: 'rgba(59, 130, 246, 0.5)', // Bright Blue
                        borderColor: '#3B82F6',
                        borderWidth: 1
                    },
                    {
                        label: 'Résolu',
                        data: <?= json_encode($statistiques_par_statut['Résolu']) ?>,
                        backgroundColor: 'rgba(107, 114, 128, 0.5)', // Gray
                        borderColor: '#6B7280',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.raw + ' réclamation(s)';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Nombre de réclamations'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Mois/Année'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>