<?php
include '../../config/database.php';

// Requête pour récupérer les tendances par type de réclamation
$sql_tendances_type = "SELECT sujet, COUNT(*) AS nombre_reclamations
                       FROM reclamations
                       GROUP BY sujet
                       ORDER BY nombre_reclamations DESC";

try {
    $stmt_tendances_type = $conn->prepare($sql_tendances_type);
    $stmt_tendances_type->execute();
    $tendances_type = $stmt_tendances_type->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de la requête pour les tendances par type : " . $e->getMessage());
}

// Prepare data for Chart.js
$labels = [];
$data = [];
$backgroundColors = [];
$borderColors = [];

$chartColors = [
    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
    '#FF9F40', '#C9CBCF', '#2ecc71', '#e74c3c', '#34495e',
    '#f1c40f', '#1abc9c', '#9b59b6', '#d35400', '#2c3e50'
];

$i = 0;
foreach ($tendances_type as $row) {
    $labels[] = $row['sujet'];
    $data[] = $row['nombre_reclamations'];
    $backgroundColors[] = $chartColors[$i % count($chartColors)];
    $borderColors[] = '#fff'; // White border for better visibility
    $i++;
}

// Function to wrap text
function wrapText($text, $maxLength = 20) {
    if (strlen($text) > $maxLength) {
        $text = wordwrap($text, $maxLength, "<br>");
    }
    return $text;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoTravel - Reclamations Tendances</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap CSS (for table and form) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom Styles -->
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
            margin: auto;
            height: 300px; /* Reduced height */
            width: 80%;
            max-width: 500px; /* Added max-width */
        }

        .legend-container {
            margin-top: 20px;
            text-align: center;
            overflow-x: auto; /* Added horizontal scrolling for legend */
            white-space: nowrap; /* Prevent legend items from wrapping */
        }

        .legend-item {
            display: inline-block;
            margin: 0 5px; /* Reduced margin */
            font-size: 12px; /* Reduced font size */
            line-height: 1.4; /* Adjust line height for wrapped text */
        }

        .legend-color {
            display: inline-block;
            width: 10px; /* Reduced size */
            height: 10px; /* Reduced size */
            margin-right: 3px; /* Reduced margin */
            border-radius: 50%;
            vertical-align: middle; /* Align color with text */
        }
    </style>
</head>
<body>
    <div class="container mx-auto h-screen flex flex-col justify-center items-center">
        <div class="content-container">
            <h1 class="text-2xl font-semibold mb-4 text-center">
                <i class="fas fa-chart-pie mr-2"></i> Tendances par Sujet de réclamation
            </h1>
            <div class="chart-container">
                <canvas id="tendancesChart"></canvas>
            </div>

            <!-- Legend -->
            <div class="legend-container">
                <?php foreach ($labels as $index => $label): ?>
                    <div class="legend-item">
                        <span class="legend-color" style="background-color: <?= $backgroundColors[$index] ?>"></span>
                        <?= wrapText(htmlspecialchars($label)) ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-4">
                <a href="consulter_reclamations.php" class="btn btn-blue text-white px-4 py-2 rounded hover:bg-blue-600">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Reclamations
                </a>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('tendancesChart').getContext('2d');
        const tendancesChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    data: <?= json_encode($data) ?>,
                    backgroundColor: <?= json_encode($backgroundColors) ?>,
                    borderColor: <?= json_encode($borderColors) ?>,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Allow chart to scale freely
                plugins: {
                    legend: {
                        display: false // Hide default legend
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.formattedValue + ' (' + context.percent.toFixed(2) + '%)';
                                return label;
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>