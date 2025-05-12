<?php
// View/exportLogementsPDF.php
//fpdf(conflit)
require_once __DIR__ . '/../../Controller/LogementController.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Fetch data
$controller = new LogementController();
$logements  = $controller->listLogements();

// Build a simple HTML table
$html = '
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #444; padding: 8px; text-align: center; }
    th { background-color: #00c4cc; color: #fff; }
    h1 { text-align: center; color: #00c4cc; }
  </style>
</head>
<body>
  <h1>Liste des Logements</h1>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Titre</th>
        <th>Ville</th>
        <th>Type</th>
        <th>Prix / Nuit</th>
        <th>Capacité</th>
        <th>Statut</th>
      </tr>
    </thead>
    <tbody>';
foreach ($logements as $l) {
    $statut = $l['disponibilite'] ? 'Disponible' : 'Indisponible';
    $html .= '
      <tr>
        <td>' . htmlspecialchars($l['id_logement']) . '</td>
        <td>' . htmlspecialchars($l['titre']) . '</td>
        <td>' . htmlspecialchars($l['ville']) . '</td>
        <td>' . htmlspecialchars($l['type']) . '</td>
        <td>' . number_format($l['prix_par_nuit'], 2, ',', ' ') . ' €</td>
        <td>' . htmlspecialchars($l['capacite']) . '</td>
        <td>' . $statut . '</td>
      </tr>';
}
$html .= '
    </tbody>
  </table>
</body>
</html>';

// Set up Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);

// (Optional) Paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render and send to browser
$dompdf->render();
$dompdf->stream(
    'Liste_Logements_' . date('Y-m-d') . '.pdf',
    ['Attachment' => true]
);
