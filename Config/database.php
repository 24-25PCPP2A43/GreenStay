<?php
// /config/database.php

class Database {
    // Instance de la base de données
    private static $dbInstance = null;
    // Connexion à la base de données
    private $connection;

    // Constructeur privé pour empêcher l'instanciation depuis l'extérieur
    private function __construct() {
        $host = 'localhost';
        $dbname = 'ecotech';
        $username = 'root';
        $password = '';

        try {
            // Création de la connexion à la base de données avec PDO
            $this->connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Si une erreur survient lors de la connexion, elle est affichée
            die('Erreur de connexion : ' . $e->getMessage());
        }
    }

    // Méthode statique pour obtenir une instance de la connexion
    public static function getConnection() {
        // Si l'instance n'existe pas, on la crée
        if (self::$dbInstance == null) {
            self::$dbInstance = new Database();
        }
        // Retourne la connexion à la base de données
        return self::$dbInstance->connection;
    }
}
?>
