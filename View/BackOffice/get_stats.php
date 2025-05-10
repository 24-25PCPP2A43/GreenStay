<?php
require_once "connexion.php";

$countAdminsStmt = $conn->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'Admin'");
$totalAdmins = $countAdminsStmt->fetchColumn();

$countClientsStmt = $conn->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'Client'");
$totalClients = $countClientsStmt->fetchColumn();

$totalUsers = $totalAdmins + $totalClients;

header('Content-Type: application/json');
echo json_encode([
    'totalUsers' => $totalUsers,
    'totalAdmins' => $totalAdmins,
    'totalClients' => $totalClients
]);
?>
