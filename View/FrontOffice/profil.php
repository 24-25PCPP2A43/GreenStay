<?php
session_start();
include 'connexion.php'; // fichier de connexion

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();
?>
