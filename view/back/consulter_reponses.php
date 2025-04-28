<?php
include '../../config/database.php';

try {
    // Récupérer toutes les réponses avec info sur la réclamation
    $sql = "SELECT r.id_reponse, r.message AS texte_reponse, r.date_reponse, 
                   rec.id AS id_reclamation, rec.sujet
            FROM reponse r
            INNER JOIN reclamations rec ON r.id_reclamation = rec.id";
    $stmt = $conn->query($sql);
    $reponses = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<section class="section" style="margin-top: 100px;">
  <div class="container">
    <div class="row justify-content-center wow fadeInUp" data-wow-delay="0.2s">
      <div class="col-lg-12">
        <div class="card shadow-lg border-0">
          <div class="card-body p-5">

            <!-- Bouton Accueil en gris -->
            <div class="mb-4 text-start">
              <a href="../../index.php" class="btn btn-secondary">
                🏠 Accueil
              </a>
            </div>

            <h2 class="mb-4 text-center">📋 Liste des Réponses</h2>

            <?php if (isset($_GET['success'])) : ?>
              <div class="alert alert-success text-center">✅ Opération réussie !</div>
            <?php endif; ?>

            <?php if (empty($reponses)) : ?>
              <p class="text-center">Aucune réponse trouvée.</p>
            <?php else : ?>
              <div class="table-responsive">
                <table class="table table-bordered table-striped text-center">
                  <thead class="thead-dark">
                    <tr>
                      <th>ID Réponse</th>
                      <th>ID Réclamation</th>
                      <th>Sujet Réclamation</th>
                      <th>Message Réponse</th>
                      <th>Date Réponse</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($reponses as $reponse) : ?>
                      <tr>
                        <td><?= htmlspecialchars($reponse['id_reponse']) ?></td>
                        <td><?= htmlspecialchars($reponse['id_reclamation']) ?></td>
                        <td><?= htmlspecialchars($reponse['sujet']) ?></td>
                        <td><?= nl2br(htmlspecialchars($reponse['texte_reponse'])) ?></td>
                        <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($reponse['date_reponse']))) ?></td>
                        <td>
                          <a href="modifierReponse.php?id_reponse=<?= $reponse['id_reponse'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                          <a href="supprimerReponse.php?id_reponse=<?= $reponse['id_reponse'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Confirmer la suppression ?')">Supprimer</a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>
