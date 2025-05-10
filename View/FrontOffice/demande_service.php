<?php
// Inclure les modèles nécessaires
require_once __DIR__ . '/../../Model/Service.php';
require_once __DIR__ . '/../../Model/DemandeService.php';
require_once __DIR__ . '/../../Controller/MessageController.php';
$serviceModel = new Service();
$demandeModel = new DemandeService();
$controller = new MessageController();
$services = $serviceModel->getAll();
$demandes = $demandeModel->getAll();


// Si une demande est en cours de modification
$demandeToEdit = null;
if (isset($_GET['edit'])) {
    $demandeToEdit = $demandeModel->getOne($_GET['edit']);
}


session_start();
if (!isset($_SESSION['user'])) {
    // pas de session = pas connecté
    header('Location: inscription.php');
    exit();
}

// Tu relis exactement ce que tu as stocké
$userData  = $_SESSION['user'];
$clientId  = $userData['id'];
$clientNom = $userData['nom'];
// Exemple : 'admin' ou 'client' selon ton système d'authentification
$expediteur = $_SESSION['role'] ?? 'client';

$controller = new MessageController();

// Traitement de l'envoi de message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contenu'])) {
    $controller->envoyerMessage($expediteur, $_POST['contenu']);
}

$messages = $controller->afficherMessages();
?>

<!-- BOUTON FLOTTANT EN BAS À DROITE -->
<button onclick="toggleDiscussion()" id="chatToggleBtn">
    💬
</button>

<!-- BOÎTE DE DISCUSSION CACHÉE PAR DÉFAUT -->
<div id="discussionBox">
    <h3 style="margin-top: 0;">💬 Discussion</h3>
    <form method="post" style="display: flex; flex-direction: column; gap: 8px;">
        <textarea name="contenu" rows="3" placeholder="Écrire un message..." required style="padding: 8px; border-radius: 5px; border: 1px solid #ccc; font-size: 13px;"></textarea>
        <button type="submit" style="padding: 8px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">
            Envoyer
        </button>
    </form>

    <div class="messages" style="margin-top: 12px; max-height: 200px; overflow-y: auto;">
        <?php foreach ($messages as $msg): ?>
            <div style="margin-bottom: 10px; padding: 10px; border-radius: 6px; background-color: <?= $msg['expediteur'] === 'admin' ? '#e3f2fd' : '#d4edda' ?>;">
                <strong style="color: #007bff;"><?= htmlspecialchars($msg['expediteur']) ?>:</strong>
                <p style="font-size: 13px; margin: 5px 0;"><?= htmlspecialchars($msg['contenu']) ?></p>
                <div style="font-size: 0.75em; color: gray;"><?= $msg['date_envoi'] ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- STYLES -->
<style>
#chatToggleBtn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 45px;
    height: 45px;
    border: none;
    border-radius: 50%;
    background-color: #007bff;
    color: white;
    font-size: 20px;
    cursor: pointer;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    z-index: 999;
}

#discussionBox {
    display: none;
    position: fixed;
    bottom: 80px;
    right: 20px;
    width: 320px;
    background-color: white;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0px 5px 15px rgba(0,0,0,0.2);
    z-index: 998;
}
</style>

<!-- SCRIPT -->
<script>
function toggleDiscussion() {
    const box = document.getElementById('discussionBox');
    box.style.display = box.style.display === 'none' ? 'block' : 'none';
}
</script>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Green House - Services</title>

    <!-- CSS Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS principal du template WoOx Travel -->
    <link rel="stylesheet" href="http://localhost/eco_tech/assets/css/templatemo-woox-travel.css">

    <!-- Global style CSS personnalisé -->
    <link rel="stylesheet" href="http://localhost/eco_tech/assets/css/global-style.css">

    <!-- Style personnalisé pour la navbar -->
    <style>
        /* Style personnalisé pour la navbar */
        .header-area {
            background-color: #5bc0de; /* Bleu clair */
            padding: 15px 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            position: relative; /* Evite les problèmes de masquage */
            z-index: 1000;
        }
        #search-container {
  margin-top: 80px;  /* Ajustez cette valeur si nécessaire */
  z-index: 500; /* Garantit que la barre de recherche n'est pas masquée par la navbar */
}

/* Assurez-vous que la barre de recherche est bien visible */
#search {
  width: 100%;
  max-width: 400px;
  margin: 0 auto;
  padding: 10px;
  box-sizing: border-box;
  position: relative;
  z-index: 1; /* S'assurer que la barre de recherche est visible */
}

        .main-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .main-nav .logo img {
            height: 50px;
        }

        .main-nav .nav {
            list-style: none;
            display: flex;
            gap: 20px;
            margin: 0;
            padding: 0;
        }

        .main-nav .nav li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s;
        }

        .main-nav .nav li a:hover,
        .main-nav .nav li a.active {
            color: #ffffff;
            text-decoration: underline;
        }

        .menu-trigger {
            display: none;
        }

        /* Style des boutons */
        .service-content .btn {
            background-color: #5bc0de; /* Même couleur que la navbar */
            border-color: #5bc0de; /* Même couleur que la navbar */
            color: white; /* Texte en blanc */
            transition: background-color 0.3s ease;
        }

        .service-content .btn:hover {
            background-color: #3aa8b4; /* Couleur légèrement plus foncée pour l'effet hover */
            border-color: #3aa8b4;
        }

        /* Autres styles spécifiques à la page */
        body {
            background-image: url("http://localhost/eco_tech/assets/images/banner-01.jpg");
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .form-container {
            display: none;
        }

        .service-card {
            position: relative;
            overflow: hidden;
            border-radius: 15px;
            background-color: #fff;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        .service-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .service-card:hover img {
            transform: scale(1.1);
        }

        .service-content {
            padding: 20px;
            text-align: center;
        }

        .service-content h4 {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 15px;
        }

        .service-content p {
            color: #666;
            font-size: 1rem;
            margin-bottom: 15px;
        }

        .service-content .btn {
            background-color: #5bc0de; /* Bleu clair comme navbar */
            border-color: #5bc0de;
            color: white; /* Texte blanc */
            transition: background-color 0.3s ease;
        }

        .service-content .btn:hover {
            background-color: #3aa8b4; /* Couleur légèrement plus foncée pour l'effet hover */
            border-color: #3aa8b4;
        }

        .service-content .price {
            font-size: 1.2rem;
            font-weight: bold;
            color:rgb(255, 30, 30);
        }

        .section-heading h2 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 30px;
        }

        .section-heading p {
            font-size: 1.2rem;
            color: #666;
        }
    </style>
</head>



<body>
  <!-- Navbar -->
  <div class="header-area">
    <div class="container">
      <nav class="main-nav">
        <!-- Logo -->
        <a href="index.php" class="logo">
          <img src="http://localhost/eco_tech/assets/images/logo.png" alt="Green House">
        </a>

        <!-- Liens de navigation -->
        <ul class="nav">
          
        <li><a href="/Projet/View/FrontOffice/home.php" class="active">Accueil</a></li>
          
          <li><a href="/Projet/View/FrontOffice/listReclamations.php">Réclamations</a></li>
          
        </ul>
      </nav>
    </div>
  </div>

  <!-- Barre de recherche sous la navbar -->
  <div class="container mt-4" id="search-container">
    <input type="text" id="search" class="form-control" placeholder="Rechercher un service...">
  </div>

<!-- Section Services -->
<div class="row mt-4" id="service-list">
    <?php foreach ($services as $service): ?>
        <?php if ($service['disponible'] != '1') continue; ?>

        <div class="col-lg-4 col-md-6 mb-4 service-card" data-name="<?= strtolower($service['nom_service']) ?>">
            <div class="card">
                <img src="http://localhost/eco_tech/assets/images/country-01.jpg" alt="Service Image">

                <div class="service-content">
                    <h4><?= htmlspecialchars($service['nom_service']) ?></h4>
                    <p><strong>ID du Service:</strong> <?= $service['id_service'] ?></p>
                    <p><?= htmlspecialchars($service['description']) ?></p>
                    <p class="price"><?= $service['prix_estime'] ?> dt</p>
                    <button class="btn btn-outline-light mt-3" onclick="showForm(<?= $service['id_service'] ?>)">Faire une demande</button>
                </div>

                <!-- Formulaire masqué -->
                <div class="form-container mt-4" id="form-<?= $service['id_service'] ?>">
                    <form method="POST" action="index.php?action=store_demande" class="bg-white p-3 border rounded">
                        <input type="hidden" name="service_id" value="<?= $service['id_service'] ?>">
                        <div class="form-group">
                            <label>ID du Client</label>
                            <input type="text" name="client_id" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Envoyer la demande</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>  adapter 

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Affichage du formulaire
    window.showForm = function(id) {
        const form = document.getElementById('form-' + id);
        if (form) {
            form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    };

    // Recherche dynamique
    const searchInput = document.getElementById('search');
    const serviceCards = document.querySelectorAll('#service-list .service-card');

    searchInput.addEventListener('input', function (e) {
        const searchTerm = e.target.value.toLowerCase();
        serviceCards.forEach(function(card) {
            const serviceName = card.getAttribute('data-name')?.toLowerCase() || '';
            card.style.display = serviceName.includes(searchTerm) ? 'block' : 'none';
        });
    });
});
</script>

<?php
// statistique 
try {
    $conn = new PDO('mysql:host=localhost;dbname=ecotech', 'root', '');
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête SQL pour récupérer les services les plus demandés
    $sql = "SELECT s.nom_service, COUNT(d.id) AS nombre_demandes
            FROM demande_service d
            JOIN services s ON d.service_id = s.id_service
            GROUP BY d.service_id
            ORDER BY nombre_demandes DESC
            LIMIT 3"; // Nous limitons à 3 pour afficher les top 3 services

    // Préparer la requête
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Récupérer les résultats
    $topServices = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    $topServices = []; // En cas d'erreur, retourner un tableau vide
}
?>



<?php if (!empty($topServices)): ?>
    <h2 class="text-center mt-5">📊 Services les plus demandés</h2>
    <canvas id="servicesChart" height="100"></canvas>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = <?= json_encode(array_column($topServices, 'nom_service')) ?>;
        const data = <?= json_encode(array_column($topServices, 'nombre_demandes')) ?>;

        new Chart(document.getElementById('servicesChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nombre de Demandes',
                    data: data,
                    backgroundColor: 'rgba(34, 189, 255, 0.2)',
                    borderColor: 'rgba(10, 46, 59, 0.41)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    </script>
<?php endif; ?>


<!-- Bouton Afficher mes demandes + Sélecteur de tri -->
<div class="container text-center my-4 d-flex justify-content-center align-items-center flex-wrap gap-3">
  <button id="toggle-demandes" class="btn btn-success px-4 py-2 shadow-sm">
    <i class="bi bi-eye-fill me-2"></i>Afficher mes demandes
  </button>

  <div id="sortWrapper" class="input-group w-auto" style="display: none;">
    <label class="input-group-text bg-success text-white fw-bold" for="sortCriteria">
      <i class="bi bi-sort-alpha-down"></i> Trier par
    </label>
    <select id="sortCriteria" class="form-select">
      <option value="id">ID</option>
      <option value="service">Service</option>
      <option value="date">Date</option>
    </select>
  </div>
</div>


<!-- Section Liste des Demandes -->
<!-- Dans front_office.php -->
<!-- Dans front_office.php -->
<a href="?action=generate_pdf" class="btn">Générer PDF des demandes</a>


<section class="section bg-light py-5" id="demande-section" style="display: none;">
  <div class="container">
    <div class="section-heading text-center mb-4">
      <h2>Mes Demandes</h2>
    </div>
    <div class="table-responsive">
      <table class="table table-bordered text-center">
        <thead class="thead-dark">
          <tr>
            <th>ID</th>
            <th>Service</th>
            <th>Description</th>
            <th>Date</th>
            <th>État</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="demande-body">
          <?php foreach ($demandes as $demande): ?>
            <tr class="demande-row"
                data-id="<?= $demande['id'] ?>"
                data-service="<?= htmlspecialchars($demande['service_id']) ?>"
                data-date="<?= $demande['date_demande'] ?>">
              <td><?= $demande['id'] ?></td>
              <td><?= $demande['service_id'] ?></td>
              <td><?= htmlspecialchars($demande['description']) ?></td>
              <td><?= $demande['date_demande'] ?></td>
              <td><?= $demande['etat'] ?></td>
              <td>
                <button class="btn btn-warning btn-sm" onclick="toggleEditForm(<?= $demande['id'] ?>)">Modifier</button>
                <a href="index.php?action=delete_demande&id=<?= $demande['id'] ?>" onclick="return confirm('Confirmer la suppression ?')" class="btn btn-danger btn-sm">Supprimer</a>
              </td>
            </tr>
            <!-- Formulaire de modification masqué -->
            <tr id="edit-form-<?= $demande['id'] ?>" style="display: none;">
              <td colspan="6">
                <form method="POST" action="index.php?action=update_demande" class="bg-white p-3 border rounded">
                  <input type="hidden" name="id" value="<?= $demande['id'] ?>">
                  <div class="form-group">
                    <label>Service</label>
                    <select name="service_id" class="form-control">
                      <?php foreach ($services as $service): ?>
                        <option value="<?= $service['id_service'] ?>" <?= $service['id_service'] === $demande['service_id'] ? 'selected' : '' ?>>
                          <?= htmlspecialchars($service['nom_service']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>ID du Client</label>
                    <input type="text" name="client_id" class="form-control" value="<?= $demande['client_id'] ?>">
                  </div>
                  <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($demande['description']) ?></textarea>
                  </div>
                  <div class="form-group">
                    <label>État actuel</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($demande['etat']) ?>" readonly>
                    <input type="hidden" name="etat" value="<?= $demande['etat'] ?>">
                  </div>
                  <button type="submit" class="btn btn-primary">Enregistrer</button>
                  <button type="button" class="btn btn-secondary" onclick="toggleEditForm(<?= $demande['id'] ?>)">Annuler</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- TRI JS -->
<script>
document.getElementById('toggle-demandes').addEventListener('click', function () {
  const section = document.getElementById('demande-section');
  const triWrapper = document.getElementById('sortWrapper');
  const visible = section.style.display === 'block';

  section.style.display = visible ? 'none' : 'block';
  triWrapper.style.display = visible ? 'none' : 'flex';

  this.innerHTML = visible
    ? '<i class="bi bi-eye-fill me-2"></i>Afficher mes demandes'
    : '<i class="bi bi-eye-slash me-2"></i>Masquer mes demandes';
});



  document.getElementById('sortCriteria').addEventListener('change', function () {
    const sortBy = this.value;
    const tbody = document.getElementById('demande-body');
    const rows = Array.from(tbody.querySelectorAll('.demande-row'));

    rows.sort((a, b) => {
      let valA = a.dataset[sortBy];
      let valB = b.dataset[sortBy];

      if (sortBy === 'id') {
        return parseInt(valA) - parseInt(valB);
      } else if (sortBy === 'date') {
        return new Date(valA) - new Date(valB);
      } else {
        return valA.localeCompare(valB);
      }
    });

    rows.forEach(row => {
      const editForm = document.getElementById('edit-form-' + row.dataset.id);
      tbody.appendChild(row);
      if (editForm) tbody.appendChild(editForm);
    });
  });

  function toggleEditForm(id) {
    const form = document.getElementById('edit-form-' + id);
    form.style.display = (form.style.display === 'none' || !form.style.display) ? 'table-row' : 'none';
  }
  
</script>

<script>
// Fonction de validation de formulaire
function setupFormValidation() {
  const forms = document.querySelectorAll('form[action*="store_demande"], form[action*="update_demande"]');

  forms.forEach(form => {
    const clientId = form.querySelector('[name="client_id"]');
    const description = form.querySelector('[name="description"]');

    if (!clientId || !description) return; // Vérifie que les champs existent

    // Ajoute un message d'erreur juste après le champ client_id
    let clientIdError = document.createElement('small');
    clientIdError.style.color = 'red';
    clientIdError.classList.add('client-id-error');
    clientId.after(clientIdError);

    // Ajoute un message d'erreur juste après le champ description
    let descriptionError = document.createElement('small');
    descriptionError.style.color = 'red';
    descriptionError.classList.add('description-error');
    description.after(descriptionError);

    // Événement sur la soumission du formulaire
    form.addEventListener('submit', function (e) {
      let hasError = false;

      // Réinitialise les messages d'erreur
      clientIdError.textContent = '';
      descriptionError.textContent = '';

      const clientIdValue = clientId.value.trim();
      const descriptionValue = description.value.trim();

      // Validation ID client
      if (!clientIdValue) {
        clientIdError.textContent = 'Veuillez entrer votre ID Client.';
        hasError = true;
      } else if (!/^\d+$/.test(clientIdValue) || parseInt(clientIdValue) <= 0) {
        clientIdError.textContent = 'L\'ID Client doit être un entier positif.';
        hasError = true;
      }

      // Validation description
      if (!descriptionValue) {
        descriptionError.textContent = 'Veuillez entrer une description.';
        hasError = true;
      } else if (!/[A-Za-zÀ-ÿ]/.test(descriptionValue)) {
        descriptionError.textContent = 'La description doit contenir au moins une lettre.';
        hasError = true;
      }

      // Empêche l'envoi si une erreur est détectée
      if (hasError) {
        e.preventDefault();
      }
    });
  });
}

// Lance la validation une fois que la page est entièrement chargée
document.addEventListener('DOMContentLoaded', setupFormValidation);
</script>



</body>
</html>
