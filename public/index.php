<?php
// Inclure les contrôleurs nécessaires
require_once __DIR__ . '/../controllers/ServiceController.php';
require_once __DIR__ . '/../controllers/DemandeServiceController.php';


$action = $_GET['action'] ?? 'home';

$serviceController = new ServiceController();
$demandeController = new DemandeServiceController();

// Gérer les actions selon l'URL
switch ($action) {
    // BACK OFFICE : Affichage des services et des demandes
    case 'back_office':
        $serviceController->index();        
        //$demandeController->index();    
        break;

    // CRUD des services
    case 'create_service':
        $serviceController->create();
        break;

    case 'store_service':
        $serviceController->store($_POST);
        break;

    case 'edit_service':
        $serviceController->edit($_GET['id']);
        break;

    case 'update_service':
        $serviceController->update($_GET['id'], $_POST);
        break;

    case 'delete_service':
        $serviceController->delete($_GET['id']);
        break;
    case 'front_office':
        //$demandeController->front(); 
        $serviceController->front(); 
        break;
    case 'edit_demande':
        $demandeController->edit($_GET['id']);
        break;
    case 'store_demande':
            $demandeController->store($_POST);
            break;
    

    case 'update_demande':
    
        $demandecontroller = new DemandeServiceController();
        $demandecontroller->update();
        break;
    
    case 'delete_demande':
        $demandeController->delete($_GET['id']);
        break;
    case 'generate_pdf':
            $demandeController->generateDemandePDF();  // Appeler la méthode pour générer le PDF
            break; 
    case 'afficher_journal':
                $controller = new ServiceController();
                $controller->afficherJournal();
                break;
            
            
    // Page d'accueil par défaut
    case 'home':
        echo "<h2>Bienvenue sur GREEN STAY</h2>";
        echo "<a href='?action=back_office'>🎛 Back-office</a> | ";
        echo "<a href='?action=front_office'>🧑‍💻 Front-office</a>";
        break;
}
?>
