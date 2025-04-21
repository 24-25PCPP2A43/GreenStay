<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoTravel - Accueil</title>
    <!-- Lien vers un fichier CSS pour styliser la page -->
    <link rel="stylesheet" href="assets/template/css/style.css">
    <style>
        /* 🌄 Ajout du fond personnalisé */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('/EcoTravel/assets/images/best-04.jpg'); /* Image du template */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }

        .container {
            text-align: center;
            background: rgba(255, 255, 255, 0.8); /* Fond semi-translucide pour contraster avec l'image */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 600px;
        }

        h1 {
            font-size: 2.5rem;
            color: #4CAF50;
        }

        p {
            font-size: 1.2rem;
            margin: 20px 0;
        }

        a {
            display: inline-block;
            text-decoration: none;
            color: white;
            background-color: #4CAF50;
            padding: 15px 30px;
            margin: 10px;
            border-radius: 5px;
            font-size: 1.1rem;
            transition: background-color 0.3s;
        }

        a:hover {
            background-color: #45a049;
        }

        .icons {
            font-size: 1.5rem;
            margin-right: 10px;
        }

        /* Styles pour mobile */
        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }
            p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Bienvenue sur EcoTravel</h1>
        <p>Choisissez votre espace :</p>
        <a href="View/front/listReclamations.php">
            <span class="icons">👤</span>Espace Client
        </a><br>
        <a href="View/back/consulter_reclamations.php">
            <span class="icons">🛠️</span>Espace Administrateur
        </a>
    </div>

</body>
</html>
