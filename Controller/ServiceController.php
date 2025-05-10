<?php
require_once __DIR__ . '/../Model/Service.php';
require_once __DIR__ . '/../Model/DemandeService.php';
class ServiceController {

    public function index() {
        $service = new Service();
        $connService = $service->getConnection();
        $stmtService = $connService->query("SELECT * FROM " . $service->getTable());
        $services = $stmtService->fetchAll(PDO::FETCH_ASSOC);
    
        // Récupération des demandes
        $demande = new DemandeService();
        $connDemande = $demande->getConnection();
        $stmtDemande = $connDemande->query("SELECT * FROM " . $demande->getTable());
        $demandes = $stmtDemande->fetchAll(PDO::FETCH_ASSOC);
    
       include __DIR__ . '/../View/BackOffice/service.php';
    }
    
    public function front() {
        $service = new Service();
        $conn = $service->getConnection();

        $stmt = $conn->query("SELECT * FROM " . $service->getTable() . " WHERE disponible = 1");
        $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include __DIR__ . '/../View/FrontOffice/demande_service.php';
    }

    public function create() {
        include __DIR__ . '/../View/BackOffice/service.php';
    }

    // Crée un nouveau service sans image
    public function store($data) {
        $service = new Service();
        $conn = $service->getConnection();
        $stmt = $conn->prepare("INSERT INTO " . $service->getTable() . " (nom_service, description, categorie, prix_estime, disponible) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['nom_service'],
            $data['description'],
            $data['categorie'],
            $data['prix_estime'],
            $data['disponible']
        ]);

        // Redirection après l'ajout
        $this->ecrireLog("Le service {$data['nom_service']} a été ajouté.");
        header("Location: index.php?action=back_office");
        exit();
    }

    public function edit($id) {
        $service = new Service();
        $conn = $service->getConnection();

        $stmt = $conn->prepare("SELECT * FROM " . $service->getTable() . " WHERE id_service = ?");
        $stmt->execute([$id]);
        $serviceToEdit = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($serviceToEdit) {
            include __DIR__ . '/../View/BackOffice/service.php';
        } else {
            echo "Service non trouvé.";
        }
    }

    public function update($id, $data) {
        $service = new Service();
        $conn = $service->getConnection();
        $stmt = $conn->prepare("UPDATE " . $service->getTable() . " SET nom_service=?, description=?, categorie=?, prix_estime=?, disponible=? WHERE id_service=?");
        $stmt->execute([
            $data['nom_service'],
            $data['description'],
            $data['categorie'],
            $data['prix_estime'],
            $data['disponible'],
            $id
        ]);
        $this->ecrireLog("Le service {$data['nom_service']} a été modifié.");

        header("Location: index.php?action=back_office");
        exit();
    }

    public function delete($id) {
        $service = new Service();
        $conn = $service->getConnection();
        $stmt = $conn->prepare("DELETE FROM " . $service->getTable() . " WHERE id_service = ?");
        $stmt->execute([$id]);
        $this->ecrireLog("Le service avec l’ID $id a été supprimé.");

        header("Location: index.php?action=back_office");
        exit();
    }
    private function ecrireLog($message) {
        $logDir = __DIR__ . '/../logs';
        $logFile = $logDir . '/activites.log';

        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $timestamp = date('[d/m/Y H:i:s]');
        $logMessage = $timestamp . ' ' . $message . PHP_EOL;

        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    public function afficherJournal() {
        $logFile = __DIR__ . '/../logs/activites.log';
        $logs = [];
    
        if (file_exists($logFile)) {
            $logs = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        }
    
        include __DIR__ . '/../View/BackOffice/service.php';
    }
    
    
    


}
?>
