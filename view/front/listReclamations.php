<?php 
include '../../config/database.php'; // Connexion à la base de données

// Récupérer les réclamations
$sql = "SELECT r.*, c.nom, c.prenom FROM reclamations r
        JOIN clients c ON r.client_id = c.id";
$stmt = $conn->prepare($sql);
$stmt->execute();
$reclamations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<!-- 🌄 Ajout du background image personnalisé -->
<style>
  .section {
    background-image: url('/EcoTravel/assets/images/best-03.jpg'); /* L'image de fond ici */
    background-size: cover; /* Couvre toute la taille de l'écran */
    background-position: center; /* Centre l'image */
    background-attachment: fixed; /* Fixe l'image de fond lors du défilement */
    padding-top: 100px;
    padding-bottom: 100px;
  }

  .card {
    background-color: rgba(255, 255, 255, 0.95); /* Fond translucide */
    border-radius: 20px;
  }

  .btn-primary {
    background-color: #2980b9; /* Bleu correspondant aux en-têtes du tableau */
    border-color: #2980b9;
    transition: 0.3s;
  }

  .btn-primary:hover {
    background-color: #1d5984; /* Bleu plus foncé au survol */
    border-color: #1d5984;
  }

  table {
    width: 100%;
    margin: 20px 0;
    border-collapse: collapse;
    background: white;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
  }

  table th, table td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: center;
  }

  table th {
    background-color: #2980b9; /* Bleu correspondant aux boutons */
    color: white;
  }

  table td {
    color: black; /* Changer la couleur du texte des données du tableau en noir */
  }

  table tr:hover {
    background-color: #ecf0f1; /* Ajouter un fond clair au survol des lignes */
  }

  .btn {
    display: inline-block;
    padding: 10px 20px;
    margin: 20px 5px 0 0;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    transition: background-color 0.3s;
  }

  /* Boutons Modifier et Supprimer avec la même couleur bleu que les en-têtes */
  .btn-modifier, .btn-supprimer {
    background-color: #2980b9; /* Bleu correspondant aux en-têtes */
    border-color: #2980b9;
  }

  .btn-modifier:hover, .btn-supprimer:hover {
    background-color: #1d5984; /* Bleu plus foncé au survol */
    border-color: #1d5984;
  }

  .actions a {
    margin: 0 5px;
    color: #3498db;
    text-decoration: none;
  }

  .actions a:hover {
    text-decoration: underline;
  }
</style>

<!-- 📝 Liste des réclamations avec ajout et actions -->
<section class="section">
  <div class="container">
    <div class="row justify-content-center wow fadeInUp" data-wow-delay="0.2s">
      <div class="col-lg-10">
        <div class="card shadow-lg border-0">
          <div class="card-body p-5">
            <h2 class="mb-4 text-center">📋 Liste des Réclamations</h2>

            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nom Client</th>
                  <th>Sujet</th>
                  <th>Message</th>
                  <th>Statut</th>
                  <th>Réponse de l'Admin</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php if (count($reclamations) > 0): ?>
                  <?php foreach ($reclamations as $row): ?>
                    <tr>
                      <td><?= $row['id'] ?></td>
                      <td><?= htmlspecialchars($row['prenom'] . ' ' . $row['nom']) ?></td>
                      <td><?= htmlspecialchars($row['sujet']) ?></td>
                      <td><?= htmlspecialchars($row['message']) ?></td>
                      <td><?= htmlspecialchars($row['statut']) ?></td>
                      <td><?= !empty($row['reponse']) ? htmlspecialchars($row['reponse']) : '<em>Pas encore répondu</em>' ?></td>
                      <td class="actions">
                        <a href="modifierReclamation.php?id=<?= $row['id'] ?>" class="btn btn-modifier">✏️ Modifier</a> |
                        <a href="supprimerReclamation.php?id=<?= $row['id'] ?>" class="btn btn-supprimer" onclick="return confirm('Voulez-vous vraiment supprimer cette réclamation ?')">🗑️ Supprimer</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr><td colspan="7">Aucune réclamation trouvée.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>

            <div class="text-center">
              <a href="addReclamation.php" class="btn btn-primary btn-lg">➕ Ajouter une réclamation</a>
              <a href="/EcoTravel/index.php" class="btn btn-primary btn-lg">🏠 Retour à l'accueil</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
