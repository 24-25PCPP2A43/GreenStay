<?php
// View/exportReservationsPDF.php

require_once __DIR__ . '/../Controller/ReservationController.php';
require_once __DIR__ . '/../vendor/autoload.php';  // Composer autoload

use Dompdf\Dompdf;
use Dompdf\Options;

// Fetch data
$ctrl = new ReservationController();
$reservations = $ctrl->listReservations();

// Build HTML
$html = '
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    h1 { text-align: center; color: #00c4cc; margin-top: 0; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #444; padding: 8px; text-align: center; }
    th { background-color: #00c4cc; color: #fff; }
  </style>
</head>
<body>
  <h1>Liste des Réservations</h1>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Logement</th>
        <th>Client</th>
        <th>Email</th>
        <th>Début</th>
        <th>Fin</th>
        <th>Statut</th>
      </tr>
    </thead>
    <tbody>';
foreach ($reservations as $r) {
    $statut = htmlspecialchars($r['statut']);
    $html .= '
      <tr>
        <td>' . htmlspecialchars($r['id_reservation'])   . '</td>
        <td>' . htmlspecialchars($r['titre_logement'])   . '</td>
        <td>' . htmlspecialchars($r['nom_client'])       . '</td>
        <td>' . htmlspecialchars($r['email_client'])     . '</td>
        <td>' . htmlspecialchars($r['date_debut'])       . '</td>
        <td>' . htmlspecialchars($r['date_fin'])         . '</td>
        <td>' . $statut                                . '</td>
      </tr>';
}
$html .= '
    </tbody>
  </table>
</body>
</html>';

// Dompdf setup
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);

// A4 landscape
$dompdf->setPaper('A4', 'landscape');

// Render and stream
$dompdf->render();
$dompdf->stream(
    'Liste_Reservations_' . date('Y-m-d') . '.pdf',
    ['Attachment' => true]
);
exit;
?>
