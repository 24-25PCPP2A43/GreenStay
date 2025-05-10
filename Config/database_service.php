<?php
class Database {
    private static $dbInstance = null;
    private $connection;

    private function __construct() {
        $host = 'localhost';
        $dbname = 'ecotech';
        $username = 'root';
        $password = '';

        try {
            $this->connection = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die('Erreur de connexion : ' . $e->getMessage());
        }
    }

    public static function getConnection() {
        if (self::$dbInstance == null) {
            self::$dbInstance = new Database();
        }
        return self::$dbInstance->connection;
    }
}
