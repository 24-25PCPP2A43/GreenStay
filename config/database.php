<?php
$host = 'localhost';
$dbname = 'ecotravel';
$username = 'root';
$password = '';

try {
    // Crée la connexion à la base de données
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Gestion des erreurs
} catch (PDOException $e) {
    // En cas d'erreur de connexion, afficher le message d'erreur
    die("Connection failed: " . $e->getMessage());
}
?>
