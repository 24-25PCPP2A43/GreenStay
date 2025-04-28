<?php
include('../../config/database.php');

// Liste des statuts possibles pour le filtre
$statuts = [
    'Nouveau' => 'Nouveau',
    'En cours de traitement' => 'En cours de traitement',
    'Traité' => 'Traité'
];

// Filtre par statut (si sélectionné)
$filter_statut = isset($_GET['statut']) ? $_GET['statut'] : '';

// Requête pour récupérer les réclamations avec la réponse (si disponible)
$sql = "SELECT r.id, r.sujet, r.message AS reclamation_message, r.statut, c.nom, c.prenom, re.date_reservation, 
                rep.message AS reponse_message, rep.date_reponse
        FROM reclamations r
        JOIN clients c ON r.client_id = c.id
        JOIN reservations re ON r.reservation_id = re.id
        LEFT JOIN reponse rep ON r.id = rep.id_reclamation";

// Ajouter la condition de filtre sur le statut si nécessaire
if ($filter_statut !== '') {
    $sql .= " WHERE r.statut = :statut";
}

try {
    $stmt = $conn->prepare($sql);

    // Si un statut est sélectionné, on le passe dans la requête
    if ($filter_statut !== '') {
        $stmt->bindParam(':statut', $filter_statut, PDO::PARAM_STR);
    }

    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de la requête : " . $e->getMessage());
}

// Requête pour récupérer les statistiques des réclamations par mois, année et statut
$sql_statistiques = "SELECT DATE_FORMAT(r.date_creation, '%Y-%m') AS mois_annee, r.statut, COUNT(r.id) AS nombre_reclamations
                     FROM reclamations r
                     GROUP BY DATE_FORMAT(r.date_creation, '%Y-%m'), r.statut ORDER BY DATE_FORMAT(r.date_creation, '%Y-%m') DESC";

try {
    $stmt_stats = $conn->prepare($sql_statistiques);
    $stmt_stats->execute();
    $stats = $stmt_stats->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de la requête pour les statistiques : " . $e->getMessage());
}

// Préparation des données pour le graphique
$labels = [];
$statistiques_par_statut = [
    'Nouveau' => [],
    'En cours de traitement' => [],
    'Traité' => []
];

// Préparation des couleurs pour chaque statut
$color_palette = [
    'Nouveau' => '#FF6347',  // Tomate pour Nouveau
    'En cours de traitement' => '#32CD32',  // Lime vert pour En cours de traitement
    'Traité' => '#1E90FF',  // Bleu Dodger pour Traité
];

// Remplissage des statistiques par statut
foreach ($stats as $stat) {
    // Format des labels comme Mois/Année
    if (!in_array(date("M Y", strtotime($stat['mois_annee'])), $labels)) {
        $labels[] = date("M Y", strtotime($stat['mois_annee']));
    }

    $statistiques_par_statut[$stat['statut']][] = $stat['nombre_reclamations'];
}

// Ajuster les statistiques pour les mois manquants dans chaque statut
foreach ($statistiques_par_statut as $statut => $data) {
    foreach ($labels as $index => $label) {
        if (!isset($data[$index])) {
            $statistiques_par_statut[$statut][$index] = 0;  // Si aucune donnée pour un mois donné, on met 0
        }
    }
}

?>

<?php include('includes/header.php'); ?>

<!-- Intégration du fichier CSS dashboard.css -->
<link rel="stylesheet" href="../../assets/css/dashboard.css">

<!-- Section avec l'image de fond -->
<div class="container-fluid mt-5 mb-5 reclamation-section" style="background-image: url('../../assets/images/best-03.jpg'); background-size: cover; background-position: center; padding: 0;">
    <div class="container p-4 shadow rounded" style="background-color: rgba(255, 255, 255, 0.8);">
        <h2 class="text-center custom-header">Liste des Réclamations</h2>

        <!-- Formulaire de filtre par statut -->
        <form action="consulter_reclamations.php" method="GET" class="mb-4">
            <div class="d-flex justify-content-center">
                <select name="statut" class="form-select" aria-label="Filtrer par statut">
                    <option value="">Tous les statuts</option>
                    <?php foreach ($statuts as $key => $value): ?>
                        <option value="<?= $key ?>" <?= ($filter_statut == $key) ? 'selected' : '' ?>><?= $value ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-outline-info ms-2">Filtrer</button>
            </div>
        </form>

        <table class="table table-hover table-bordered text-center align-middle">
            <thead class="table-info">
                <tr>
                    <th>ID</th>
                    <th>Sujet</th>
                    <th>Message</th>
                    <th>Statut</th>
                    <th>Client</th>
                    <th>Date Réservation</th>
                    <th>Réponse</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (count($result) > 0): ?>
                <?php foreach ($result as $row): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['sujet']) ?></td>
                        <td><?= htmlspecialchars($row['reclamation_message']) ?></td>
                        <td>
                            <?= htmlspecialchars($row['statut']) ?>
                        </td>
                        <td><?= $row['nom'] . ' ' . $row['prenom'] ?></td>
                        <td><?= date("d/m/Y", strtotime($row['date_reservation'])) ?></td>
                        <td>
                            <?php if ($row['reponse_message']): ?>
                                <?= htmlspecialchars($row['reponse_message']) ?> 
                                <br><small>Le <?= date("d/m/Y H:i", strtotime($row['date_reponse'])) ?></small>
                            <?php else: ?>
                                Pas encore répondu
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="ajouterReponse.php?id=<?= $row['id'] ?>" class="btn btn-outline-dark btn-sm">Répondre</a>
                            <a href="changerStatut.php?id=<?= $row['id'] ?>" class="btn btn-gradient-blue btn-sm">Changer statut</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8" class="text-center text-muted">Aucune réclamation trouvée.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>

        <!-- Section Statistiques -->
        <div class="text-center mt-4">
            <button class="btn btn-outline-info btn-lg" data-bs-toggle="collapse" data-bs-target="#statistiques" aria-expanded="false" aria-controls="statistiques">
                Statistiques des réclamations
            </button>
        </div>

        <div class="collapse mt-3" id="statistiques">
            <h3>Statistiques des réclamations par mois/année et statut</h3>
            
            <!-- Ajout du graphique dynamique -->
            <canvas id="statistiquesChart" width="400" height="200"></canvas>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const ctx = document.getElementById('statistiquesChart').getContext('2d');
                const statistiquesChart = new Chart(ctx, {
                    type: 'bar',  // Type de graphique: barres
                    data: {
                        labels: <?= json_encode($labels) ?>,  // Mois/Année
                        datasets: [
                            {
                                label: 'Nouveau',
                                data: <?= json_encode($statistiques_par_statut['Nouveau']) ?>,
                                backgroundColor: 'rgba(255, 99, 71, 0.5)',  // Tomate pastel transparent
                                borderColor: 'rgba(0, 0, 0, 0.1)',  // Couleur de la bordure
                                borderWidth: 1
                            },
                            {
                                label: 'En cours de traitement',
                                data: <?= json_encode($statistiques_par_statut['En cours de traitement']) ?>,
                                backgroundColor: 'rgba(50, 205, 50, 0.5)',  // Lime pastel transparent
                                borderColor: 'rgba(0, 0, 0, 0.1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Traité',
                                data: <?= json_encode($statistiques_par_statut['Traité']) ?>,
                                backgroundColor: 'rgba(30, 144, 255, 0.5)',  // Dodger Blue pastel transparent
                                borderColor: 'rgba(0, 0, 0, 0.1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        return tooltipItem.raw + ' réclamation(s)';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Mois/Année'
                                }
                            }
                        }
                    }
                });
            </script>
        </div>

        <div class="text-center mt-4">
            <a href="consulter_reponses.php" class="btn btn-outline-info btn-lg">Voir les réponses</a>
        </div>

        <div class="text-center mt-2">
            <a href="../../index.php" class="btn btn-marine">Retour à l'accueil</a>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<!-- Ajout de Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
