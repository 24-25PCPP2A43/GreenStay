<?php
require_once __DIR__ . '/../../Config/database.php';
$conn = Database::connect();

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="utilisateurs.csv"');

$output = fopen("php://output", "w");
fputcsv($output, ["ID", "Nom", "Prénom", "Email", "Téléphone", "Rôle"]);

$sql = "SELECT * FROM utilisateurs";
$stmt = $conn->prepare($sql);
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, $row);
}
fclose($output);
exit;
