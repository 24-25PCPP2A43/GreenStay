<?php
require_once __DIR__ . '/../../Config/database.php';

try {
    $conn = Database::connect();
    $stmt = $conn->query("SELECT id, nom, prenom, email, role, is_banned, ban_reason, ban_until FROM utilisateurs");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=gestion_comptes.csv');
    $output = fopen('php://output', 'w');

    fputcsv($output, ['ID', 'Nom', 'Prénom', 'Email', 'Rôle', 'Statut', 'Raison du ban', 'Ban jusqu\'à']);
    foreach ($users as $user) {
        fputcsv($output, [
            $user['id'],
            $user['nom'],
            $user['prenom'],
            $user['email'],
            $user['role'],
            $user['is_banned'] ? 'Banni' : 'Actif',
            $user['ban_reason'] ?? '',
            $user['ban_until'] ?? ''
        ]);
    }

    fclose($output);
    exit();
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
