<?php
// View/exportStatisticsPDF.php

require_once __DIR__ . '/../../Controller/StatisticsController.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Récupérer les statistiques
$statsController = new StatisticsController();
$totalLogements = $statsController->getTotalLogements();
$totalReservations = $statsController->getTotalReservations();
$occupancyRate = $statsController->getOccupancyRate();
$averageStayDuration = $statsController->getAverageStayDuration();
$mostPopularCity = $statsController->getMostPopularCity();
$revenueByMonth = $statsController->getRevenueByMonth();
$reservationsByStatus = $statsController->getReservationsByStatus();
$topLogements = $statsController->getTopLogements(5);
$totalRevenue = $statsController->getTotalRevenue();

// Construire le HTML
$html = '
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <style>
    body { 
        font-family: DejaVu Sans, sans-serif; 
        font-size: 12px; 
        color: #333;
        line-height: 1.5;
    }
    h1 { 
        text-align: center; 
        color: #00c4cc; 
        margin-top: 0; 
        padding: 10px;
        border-bottom: 2px solid #00c4cc;
    }
    h2 {
        color: #00c4cc;
        border-bottom: 1px solid #00c4cc;
        padding-bottom: 5px;
        margin-top: 20px;
    }
    .header {
        background-color: #00c4cc;
        color: white;
        padding: 20px;
        text-align: center;
        margin-bottom: 20px;
    }
    .header h1 {
        color: white;
        margin: 0;
        border: none;
    }
    .date {
        font-style: italic;
        text-align: center;
        margin-bottom: 20px;
    }
    table { 
        width: 100%; 
        border-collapse: collapse; 
        margin-top: 15px;
        margin-bottom: 25px;
    }
    th, td { 
        border: 1px solid #ddd; 
        padding: 8px; 
        text-align: left; 
    }
    th { 
        background-color: #00c4cc; 
        color: #fff; 
    }
    tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    .stats-grid {
        display: flex;
        flex-wrap: wrap;
        margin: 0 -10px;
    }
    .stats-card {
        width: 29%;
        margin: 10px;
        padding: 15px;
        background-color: #f9f9f9;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .stats-card .title {
        color: #666;
        font-size: 14px;
        margin-bottom: 5px;
    }
    .stats-card .value {
        color: #00c4cc;
        font-size: 20px;
        font-weight: bold;
    }
    .footer {
        text-align: center;
        margin-top: 30px;
        font-size: 10px;
        color: #666;
        border-top: 1px solid #ddd;
        padding-top: 10px;
    }
  </style>
</head>
<body>
  <div class="header">
    <h1>Rapport statistique WoOx Travel</h1>
  </div>
  
  <div class="date">Généré le ' . date('d/m/Y') . '</div>
  
  <h2>Statistiques générales</h2>
  
  <div class="stats-grid">
    <div class="stats-card">
      <div class="title">Total des logements</div>
      <div class="value">' . $totalLogements . '</div>
    </div>
    
    <div class="stats-card">
      <div class="title">Total des réservations</div>
      <div class="value">' . $totalReservations . '</div>
    </div>
    
    <div class="stats-card">
      <div class="title">Taux d\'occupation</div>
      <div class="value">' . number_format($occupancyRate, 1) . '%</div>
    </div>
    
    <div class="stats-card">
      <div class="title">Durée moyenne de séjour</div>
      <div class="value">' . number_format($averageStayDuration, 1) . ' jours</div>
    </div>
    
    <div class="stats-card">
      <div class="title">Ville la plus populaire</div>
      <div class="value">' . htmlspecialchars($mostPopularCity['ville']) . '</div>
    </div>
    
    <div class="stats-card">
      <div class="title">Revenu total</div>
      <div class="value">' . number_format($totalRevenue, 2, ',', ' ') . ' €</div>
    </div>
  </div>
  
  <h2>Top 5 des logements les plus réservés</h2>
  
  <table>
    <thead>
      <tr>
        <th>Rang</th>
        <th>Logement</th>
        <th>Ville</th>
        <th>Réservations</th>
        <th>Revenus générés</th>
      </tr>
    </thead>
    <tbody>';

$rank = 1;
foreach ($topLogements as $logement) {
    $html .= '
      <tr>
        <td>' . $rank++ . '</td>
        <td>' . htmlspecialchars($logement['titre']) . '</td>
        <td>' . htmlspecialchars($logement['ville']) . '</td>
        <td>' . $logement['reservation_count'] . '</td>
        <td>' . number_format($logement['revenue'], 2, ',', ' ') . ' €</td>
      </tr>';
}

$html .= '
    </tbody>
  </table>
  
  <h2>Réservations par statut</h2>
  
  <table>
    <thead>
      <tr>
        <th>Statut</th>
        <th>Nombre</th>
        <th>Pourcentage</th>
      </tr>
    </thead>
    <tbody>';

$totalCount = array_sum(array_column($reservationsByStatus, 'count'));
foreach ($reservationsByStatus as $status) {
    $percentage = $totalCount > 0 ? ($status['count'] / $totalCount) * 100 : 0;
    $html .= '
      <tr>
        <td>' . htmlspecialchars($status['statut']) . '</td>
        <td>' . $status['count'] . '</td>
        <td>' . number_format($percentage, 1) . '%</td>
      </tr>';
}

$html .= '
    </tbody>
  </table>
  
  <h2>Revenus par mois</h2>
  
  <table>
    <thead>
      <tr>
        <th>Mois</th>
        <th>Revenus (€)</th>
      </tr>
    </thead>
    <tbody>';

foreach ($revenueByMonth as $revenue) {
    $html .= '
      <tr>
        <td>' . htmlspecialchars($revenue['month_name']) . '</td>
        <td>' . number_format($revenue['total_revenue'], 2, ',', ' ') . ' €</td>
      </tr>';
}

$html .= '
    </tbody>
  </table>
  
  <div class="footer">
    <p>Ce rapport est généré automatiquement par le système WoOx Travel.</p>
    <p>Pour plus d\'informations, consultez le tableau de bord complet.</p>
  </div>
</body>
</html>';

// Configuration de Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);

// Format A4 portrait
$dompdf->setPaper('A4', 'portrait');

// Rendu et téléchargement
$dompdf->render();
$dompdf->stream(
    'Statistiques_WoOx_' . date('Y-m-d') . '.pdf',
    ['Attachment' => true]
);
exit;
?>