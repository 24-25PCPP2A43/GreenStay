<?php
require_once __DIR__ . '/../models/DemandeService.php'; // Inclut le modèle DemandeService
require_once __DIR__ . '/../models/Service.php'; 
require_once __DIR__ . '/../libs/fpdf.php';
class DemandeServiceController {

    public function index() {
        $demande = new DemandeService();
        $demandes = $demande->getAll(); 
        include __DIR__ . '/../views/back_office.php';}

    public function front() {
            include_once 'models/DemandeService.php';
            include_once 'models/Service.php';
        
            $demandeServiceModel = new DemandeService();
            $serviceModel = new Service();
        
            $services = $serviceModel->getAll();
            $demandes = $demandeServiceModel->getAll();
        
            // Tri des demandes (en PHP, dans le contrôleur)
            $sort_by = $_GET['sort_by'] ?? 'id';
            $allowed_sorts = ['id', 'service_name', 'date'];
        
            if (!in_array($sort_by, $allowed_sorts)) {
                $sort_by = 'id';
            }
        
            usort($demandes, function ($a, $b) use ($sort_by) {
                if ($sort_by === 'date') {
                    return strtotime($a['date']) <=> strtotime($b['date']);
                } else {
                    return strcmp($a[$sort_by], $b[$sort_by]);
                }
            });
        
            include 'views/front_office.php';
        }
    
        
    
    /*public function create() {
        include __DIR__ . '/../views/front_office.php'; 
    }*/
   
    public function store($data) {
        $demande = new DemandeService();
        $conn = $demande->getConnection();
    
        $stmt = $conn->prepare("
            INSERT INTO " . $demande->getTable() . " (service_id, client_id, description, date_demande, etat)
            VALUES (?, ?, ?, NOW(), 'en attente')
        ");
    
        $stmt->execute([
            $data['service_id'],
            $data['client_id'],
            $data['description']
        ]);
    
        header("Location: index.php?action=front_office");
        exit();
    }
    

    public function edit() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $demande = new DemandeService();
            $demandeToEdit = $demande->getOne($id); // Récupère la demande à modifier
            
            if ($demandeToEdit) {
                include __DIR__ . '/../views/front_office.php'; 
                echo "Demande non trouvée.";
            }
        } else {
            echo "Aucun ID de demande spécifié.";
        }
    }
   

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $service_id = $_POST['service_id'];
            $client_id = $_POST['client_id'];
            $description = $_POST['description'];
            $etat = $_POST['etat'];
    
            $model = new DemandeService();
            $conn = $model->getConnection();
            $stmt = $conn->prepare("
                UPDATE " . $model->getTable() . " 
                SET service_id = ?, client_id = ?, description = ?, etat = ?
                WHERE id = ?
            ");
            $stmt->execute([$service_id, $client_id, $description, $etat, $id]);
    
            header("Location: index.php?action=front_office");
            exit();
        }
    }
    
    
    public function delete($id) {
        $demande = new DemandeService();
        $conn = $demande->getConnection(); 

        $stmt = $conn->prepare("DELETE FROM " . $demande->getTable() . " WHERE id = ?");
        $stmt->execute([$id]);

        header("Location: index.php?action=front_office");
        exit();
    }
    public function generateDemandePDF() {
        // Créer une instance de FPDF
        $pdf = new FPDF();
        $pdf->AddPage();
    
        // Ajouter le logo (en haut à gauche)
        $logoPath = "http://localhost/eco_tech/assets/images/logo1.png";
        $pdf->Image($logoPath, 10, 10, 40);  // 10 = position X, 10 = position Y, 40 = largeur du logo
    
        // Définir la couleur du texte en vert
        $pdf->SetTextColor(0, 128, 0); // Vert
    
        // Définir la police pour le titre
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Ln(30);  // Ajouter un espace après l'image pour ne pas chevaucher le texte
    
        // Titre du PDF
        $pdf->Cell(200, 10, 'Liste des demandes de service', 0, 1, 'C');
        $pdf->Ln(10);  // Saut de ligne
    
        // Récupérer les demandes existantes
        $demandeModel = new DemandeService();  // Assure-toi que c'est ton modèle
        $demandes = $demandeModel->getAll();  // Méthode pour récupérer toutes les demandes
    
        // Vérifier si des demandes existent
        if ($demandes) {
            // Ajouter les entêtes des colonnes dans le PDF avec un fond vert et texte blanc
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->SetFillColor(0, 128, 0); // Fond vert
            $pdf->SetTextColor(255, 255, 255); // Texte en blanc
            $pdf->Cell(30, 10, 'ID', 1, 0, 'C', true); // Le 'true' applique la couleur de fond
            $pdf->Cell(50, 10, 'ID du Service', 1, 0, 'C', true);
            $pdf->Cell(50, 10, 'Client ID', 1, 0, 'C', true);
            $pdf->Cell(40, 10, 'Date de la demande', 1, 0, 'C', true); // Colonne de la date plus large
            $pdf->Cell(30, 10, 'Status', 1, 1, 'C', true);
    
            // Revenir au texte vert pour les lignes de données
            $pdf->SetFont('Arial', '', 12);
            $pdf->SetTextColor(0, 128, 0); // Texte en vert
            $pdf->SetFillColor(220, 255, 220); // Fond vert pâle pour les lignes de données
    
            // Ajouter chaque demande dans le PDF
            foreach ($demandes as $demande) {
                $pdf->Cell(30, 10, $demande['id'], 1, 0, 'C', true);
                $pdf->Cell(50, 10, $demande['service_id'], 1, 0, 'C', true);
                $pdf->Cell(50, 10, $demande['client_id'], 1, 0, 'C', true);
                $pdf->Cell(40, 10, $demande['date_demande'], 1, 0, 'C', true); // Colonne de la date plus large
                $pdf->Cell(30, 10, $demande['etat'], 1, 1, 'C', true);
            }
    
            // Ajouter une ligne de séparation pour la fin du tableau
            $pdf->Ln(10);  // Saut de ligne
            $pdf->SetFont('Arial', 'I', 10);
            $pdf->Cell(200, 10, 'Fin de la liste des demandes', 0, 1, 'C');
            
            // Générer et afficher le PDF
            $pdf->Output('D', 'demandes.pdf'); // Télécharger sous le nom 'demandes.pdf'
        } else {
            // Si aucune demande n'est trouvée
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(200, 10, 'Aucune demande trouvée.', 0, 1, 'C');
            $pdf->Output();
        }
        exit; // Arrêter l'exécution après l'affichage du PDF
    }
    

    
    
    



}
?>
