
  <!-- Inclus le reste de votre page après -->
<!-- header.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
<link rel="stylesheet" href="../../assets/css/dashboard.css">

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EcoTravel - Back Office</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="../../assets/css/style.css"> <!-- Personnalisez votre fichier CSS ici -->
  <style>
    /* Header personnalisé */
    header {
      background-color: #f8f9fa; /* Gris clair */
      color: #343a40; /* Gris foncé pour le texte */
      padding: 20px 0;
    }

    /* Titre du header */
    .header-title {
      font-family: 'Poppins', sans-serif;
      font-size: 32px;
      font-weight: bold;
      text-align: center;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3); /* Ombre douce */
    }

    /* Navigation du header */
    .navbar {
      background-color: #f8f9fa; /* Gris clair pour la navbar */
      border-radius: 0;
    }

    .navbar-nav .nav-link {
      color: #343a40 !important; /* Gris foncé pour les liens */
      font-size: 16px;
    }

    .navbar-nav .nav-link:hover {
      color: #007bff !important; /* Changement de couleur au survol */
    }

    .navbar-brand {
      font-size: 24px;
      font-weight: bold;
      color: #343a40 !important; /* Gris foncé pour le logo */
    }

    /* Pour enlever les marges et la largeur grise de la section */
    .container-fluid {
        padding: 0;
        width: 100%;
    }
  </style>
</head>
<body>
  <header>
    <div class="container">
      <div class="header-title">
        <h1>EcoTravel - Back Office</h1>
      </div>
    </div>
    <nav class="navbar navbar-expand-lg navbar-light">
      <div class="container">
        <a class="navbar-brand" href="../../index.php">EcoTravel</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item">
              <a class="nav-link" href="consulter_reclamation.php">Réclamations</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="gestion_clients.php">Clients</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="gestion_reservation.php">Réservations</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="logout.php">Déconnexion</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <!-- Inclus le reste de votre page après -->
