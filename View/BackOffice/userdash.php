<?php
session_start();
require_once __DIR__ . '/../../Config/database.php';
require_once __DIR__ . '/../../Controller/UserController.php';
$loggedUser = $_SESSION['user'] ?? null;

// Connexion à la base de données
$conn = Database::connect();

// Gestion des messages de notification
$alert = isset($_SESSION['alert']) ? $_SESSION['alert'] : null;
unset($_SESSION['alert']);

// Pagination
$limit = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;
// Gestion du tri
$sortColumn = isset($_GET['sort_column']) ? $_GET['sort_column'] : 'nom';
$sortDirection = (isset($_GET['sort_direction']) && in_array($_GET['sort_direction'], ['ASC', 'DESC'])) ? $_GET['sort_direction'] : 'ASC';
$nextSortDirection = $sortDirection === 'ASC' ? 'DESC' : 'ASC';
// Tri pour la section des rôles (gestion des comptes)
$sortColumnRoles = isset($_GET['sort_column_roles']) ? $_GET['sort_column_roles'] : 'id';
$sortDirectionRoles = (isset($_GET['sort_direction_roles']) && in_array($_GET['sort_direction_roles'], ['ASC', 'DESC'])) ? $_GET['sort_direction_roles'] : 'ASC';
$nextSortDirectionRoles = $sortDirectionRoles === 'ASC' ? 'DESC' : 'ASC';

$allowedColumnsRoles = ['id', 'nom', 'prenom', 'email', 'role'];
if (!in_array($sortColumnRoles, $allowedColumnsRoles)) {
    $sortColumnRoles = 'id';
}


// Sécuriser les colonnes autorisées pour éviter les injections
$allowedColumns = ['id', 'nom', 'prenom', 'email', 'telephone', 'role'];
if (!in_array($sortColumn, $allowedColumns)) {
    $sortColumn = 'nom';
}


// Récupération des utilisateurs avec pagination

try {
    $selectedRole = isset($_GET['role']) && $_GET['role'] !== '' ? $_GET['role'] : null;
    $sql = "SELECT * FROM utilisateurs WHERE (:role IS NULL OR role = :role) ORDER BY $sortColumn $sortDirection LIMIT :limit OFFSET :offset";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':role', $selectedRole);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    

    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Pagination indépendante pour la gestion des rôles
$limit_roles = 5;
$page_roles = isset($_GET['page_roles']) ? max(1, intval($_GET['page_roles'])) : 1;
$offset_roles = ($page_roles - 1) * $limit_roles;

$stmt_roles = $conn->prepare("SELECT * FROM utilisateurs ORDER BY $sortColumnRoles $sortDirectionRoles LIMIT :limit OFFSET :offset");

$stmt_roles->bindParam(':limit', $limit_roles, PDO::PARAM_INT);
$stmt_roles->bindParam(':offset', $offset_roles, PDO::PARAM_INT);
$stmt_roles->execute();
$rows_roles = $stmt_roles->fetchAll(PDO::FETCH_ASSOC);

// Nombre total de lignes pour les rôles
$count_roles = $conn->query("SELECT COUNT(*) FROM utilisateurs");
$totalRoles = $count_roles->fetchColumn();
$totalPagesRoles = ceil($totalRoles / $limit_roles);


    // Pour afficher les liens de pagination
    $countStmt = $conn->query("SELECT COUNT(*) FROM utilisateurs");
    $totalUsers = $countStmt->fetchColumn();
    $totalPages = ceil($totalUsers / $limit);

    // Calcul des statistiques
    $adminStmt = $conn->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'Admin'");
    $totalAdmins = $adminStmt->fetchColumn();
    $clientStmt = $conn->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'Client'");
    $totalClients = $clientStmt->fetchColumn();
    
    // Statistiques des bannis
    $bannedStmt = $conn->query("SELECT COUNT(*) FROM utilisateurs WHERE is_banned = 1");
    $totalBanned = $bannedStmt->fetchColumn();
    $activeStmt = $conn->query("SELECT COUNT(*) FROM utilisateurs WHERE is_banned = 0");
    $totalActive = $activeStmt->fetchColumn();
} catch (PDOException $e) {
    die("Erreur de requête : " . $e->getMessage());
}

// Vérifier si un email existe déjà dans la base (sauf pour l'utilisateur en cours de modification)
function emailExists($conn, $email) {
    $sql = "SELECT COUNT(*) FROM utilisateurs WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    return $stmt->fetchColumn() > 0;
}


// Traitement formulaire d'ajout
// Traitement formulaire d'ajout et de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        // Récupération et nettoyage des données
        $nom = trim(htmlspecialchars($_POST['nom'] ?? ''));
        $prenom = trim(htmlspecialchars($_POST['prenom'] ?? ''));
        $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
        $telephone = trim(htmlspecialchars($_POST['telephone'] ?? ''));
        $role = htmlspecialchars($_POST['role'] ?? '');
        $id = isset($_POST['id']) ? intval($_POST['id']) : null;

        // Validation des champs
        $errors = [];
        
        if (empty($nom)) $errors['nom'] = "Le nom est obligatoire";
        elseif (!preg_match("/^[a-zA-ZÀ-ÿ\s\-]{2,50}$/", $nom)) {
            $errors['nom'] = "Le nom doit contenir entre 2 et 50 caractères alphabétiques";
        }

        if (empty($prenom)) $errors['prenom'] = "Le prénom est obligatoire";
        elseif (!preg_match("/^[a-zA-ZÀ-ÿ\s\-]{2,50}$/", $prenom)) {
            $errors['prenom'] = "Le prénom doit contenir entre 2 et 50 caractères alphabétiques";
        }

        if (empty($email)) $errors['email'] = "L'email est obligatoire";
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Format d'email invalide";
        }

        if (empty($telephone)) $errors['telephone'] = "Le téléphone est obligatoire";
        elseif (!preg_match("/^[0-9]{8}$/", $telephone)) {
            $errors['telephone'] = "Le téléphone doit contenir exactement 8 chiffres";
        }

        if (empty($role)) $errors['role'] = "Le rôle est obligatoire";

        // Vérifier l'email uniquement si c'est une nouvelle entrée ou si l'email a changé
        if ($_POST['action'] === 'add') {
            if (emailExists($conn, $email)) {
                $errors['email'] = "Cet email est déjà utilisé";
            }
        } elseif ($_POST['action'] === 'update') {
            // Récupérer l'email actuel de l'utilisateur
            $currentEmailStmt = $conn->prepare("SELECT email FROM utilisateurs WHERE id = ?");
            $currentEmailStmt->execute([$id]);
            $currentEmail = $currentEmailStmt->fetchColumn();
            
            // Vérifier l'email seulement s'il a changé
            if ($email !== $currentEmail && emailExists($conn, $email)) {
                $errors['email'] = "Cet email est déjà utilisé";
            }
        }

        // Si erreurs, on les affiche
        if (!empty($errors)) {
            throw new Exception(implode("\n", $errors));
        }

        // Suite du traitement...
        if ($_POST['action'] === 'add') {
            // Ajout d'un nouvel utilisateur
            $sql = "INSERT INTO utilisateurs (nom, prenom, email, telephone, role) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$nom, $prenom, $email, $telephone, $role]);

            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Utilisateur ajouté avec succès!'
            ];
        }
        elseif ($_POST['action'] === 'update') {
            // Mise à jour d'un utilisateur existant
            $sql = "UPDATE utilisateurs SET nom=?, prenom=?, email=?, telephone=?, role=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$nom, $prenom, $email, $telephone, $role, $id]);

            $_SESSION['alert'] = [
                'type' => 'success',
                'message' => 'Utilisateur mis à jour avec succès!'
            ];
        }

        header("Location: " . $_SERVER['PHP_SELF'] . "?page=" . $page);
        exit;
    } catch (Exception $e) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => $e->getMessage()
        ];
        header("Location: " . $_SERVER['PHP_SELF'] . "?page=" . $page);
        exit;
    }
}

// Gestion du bannissement/débannissement
if (isset($_GET['ban'])) {
    try {
        $id = intval($_GET['ban']);
        $reason = isset($_GET['reason']) ? htmlspecialchars($_GET['reason']) : 'Violation des règles';
        $banUntil = isset($_GET['ban_until']) ? htmlspecialchars($_GET['ban_until']) : null;

        $sql = "UPDATE utilisateurs SET is_banned = 1, ban_reason = ?, ban_until = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$reason, $banUntil, $id]);

        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'Utilisateur banni avec succès!'
        ];
        header("Location: " . $_SERVER['PHP_SELF'] . "?page=" . $page);
        exit;
    } catch (Exception $e) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Erreur lors du bannissement: ' . $e->getMessage()
        ];
        header("Location: " . $_SERVER['PHP_SELF'] . "?page=" . $page);
        exit;
    }
}

// Gestion du débannissement
if (isset($_GET['unban'])) {
    try {
        $id = intval($_GET['unban']);

        $sql = "UPDATE utilisateurs SET is_banned = 0, ban_reason = NULL, ban_until = NULL WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);

        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'Utilisateur débanni avec succès!'
        ];
        header("Location: " . $_SERVER['PHP_SELF'] . "?page=" . $page);
        exit;
    } catch (Exception $e) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Erreur lors du débannissement: ' . $e->getMessage()
        ];
        header("Location: " . $_SERVER['PHP_SELF'] . "?page=" . $page);
        exit;
    }
}

// Suppression d'un utilisateur
if (isset($_GET['delete'])) {
    try {
        $id = intval($_GET['delete']);
        $sql = "DELETE FROM utilisateurs WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);

        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'Utilisateur supprimé avec succès!'
        ];
        header("Location: " . $_SERVER['PHP_SELF'] . "?page=" . $page);
        exit;
    } catch (Exception $e) {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
        ];
        header("Location: " . $_SERVER['PHP_SELF'] . "?page=" . $page);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Users Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />
    <link href="../../assets/css/dashboard.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

    <style>
        .table-dark a {
    color: white !important;
    text-decoration: none;
}
.table-dark a:hover {
    color: #ccc !important;
}

        .centered { text-align: center; }
        .badge-admin { background-color: #f39c12; color: white; padding: 3px 8px; border-radius: 4px; }
        .badge-client { background-color: #3498db; color: white; padding: 3px 8px; border-radius: 4px; }
        .sidebar { transition: all 0.3s; }
        .sidebar.active { margin-left: -250px; }
        .toggle-btn { position: fixed; left: 10px; top: 10px; z-index: 1000; }
        #statsSection { display: none; }
        #accountManagementSection { display: none; }
        #pieChartContainer { height: 200px; margin-top: 20px; }
        .editable { cursor: pointer; }
        .edit-form { display: none; }
        .edit-form.active { display: block; }
        .view-mode { display: block; }
        .view-mode.hidden { display: none; }
        .alert-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        }
        .is-invalid { border-color: #dc3545; }
        .invalid-feedback { color: #dc3545; font-size: 0.875em; }
        .ban-details {
            font-size: 0.8em;
            color: #6c757d;
        }
        .ban-reason {
            color: #dc3545;
            font-weight: bold;
        }
        .ban-until {
            color: #fd7e14;
        }
    </style>
</head>
<body>

<!-- Bouton menu -->
<button class="btn btn-light toggle-btn" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <a href="../FrontOffice/home.php"><i class="fa-solid fa-house-user"></i> Home Page</a>
    <a href="#" onclick="toggleAddForm(); return false;"><i class="fas fa-user-plus"></i> Ajouter un utilisateur</a>
    <a href="#" onclick="toggleUserTable(); return false;"><i class="fas fa-users"></i> Afficher les utilisateurs</a>
    <a href="#" onclick="toggleAccountManagement(); return false;"><i class="fas fa-user-shield"></i> Gestion des comptes</a>
    <a href="#" onclick="toggleStats(); return false;"><i class="fas fa-chart-line"></i> Statistiques</a>
    <a href="../../Controller/logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
</div>

<!-- Contenu principal -->
<div class="content">
    <!-- Alertes Bootstrap -->
    <?php if ($alert): ?>
    <div class="alert-container">
        <div class="alert alert-<?= $alert['type'] ?> alert-dismissible fade show" role="alert">
            <?= $alert['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg shadow">
    <div class="container-fluid d-flex justify-content-between align-items-center">
    <h1 class="navbar-brand d-flex align-items-center">
        <img src="../../assets/svg/dash-cryptocurrency-coin-svgrepo-com.svg" class="action-icon me-2" alt="Dashboard" />
        Dashboard
    </h1>

    <form class="d-flex me-auto ms-3">
        <input class="form-control me-2" type="search" id="searchInput" placeholder="Recherche..." onkeyup="filterTable()">
    </form>

    <?php if ($loggedUser): ?>
        <div class="d-flex align-items-center text-end">
            <i class="fas fa-user-circle fa-lg me-2 text-primary"></i>
            <span class="fw-bold"><?= htmlspecialchars($loggedUser['prenom']) ?> <?= htmlspecialchars($loggedUser['nom']) ?></span>
        </div>
    <?php endif; ?>
</div>

    </nav>

    <!-- Formulaire ajout utilisateur -->
    <div class="container mt-5" id="addUserForm" style="display: none;">
        <h2 class="centered">Ajouter un Utilisateur</h2>
        <form id="addUserFormElement" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" novalidate>
            <input type="hidden" name="action" value="add">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" class="form-control" id="nom" name="nom" required 
                       pattern="[a-zA-ZÀ-ÿ\s\-]{2,50}" 
                       title="2-50 caractères alphabétiques">
                <div class="invalid-feedback">Veuillez entrer un nom valide (2-50 caractères alphabétiques)</div>
            </div>
            <div class="mb-3">
                <label for="prenom" class="form-label">Prénom</label>
                <input type="text" class="form-control" id="prenom" name="prenom" required 
                       pattern="[a-zA-ZÀ-ÿ\s\-]{2,50}" 
                       title="2-50 caractères alphabétiques">
                <div class="invalid-feedback">Veuillez entrer un prénom valide (2-50 caractères alphabétiques)</div>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
                <div class="invalid-feedback">Veuillez entrer un email valide</div>
            </div>
            <div class="mb-3">
                <label for="telephone" class="form-label">Téléphone</label>
                <input type="tel" class="form-control" id="telephone" name="telephone" required 
                       pattern="[0-9]{8}" 
                       title="8 chiffres exactement (ex: 12345678)"
                       maxlength="8">
                <div class="invalid-feedback">Veuillez entrer exactement 8 chiffres</div>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Rôle</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="">Sélectionnez un rôle</option>
                    <option value="Admin">Admin</option>
                    <option value="Client">Client</option>
                </select>
                <div class="invalid-feedback">Veuillez sélectionner un rôle</div>
            </div>
            <button type="submit" class="btn btn-success">Ajouter</button>
        </form>
    </div>

    <!-- Bloc Statistiques -->
    <div class="container mt-5" id="statsSection">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Utilisateurs</h5>
                        <p class="card-text"><?= $totalUsers ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <h5 class="card-title">Nombre d'Admins</h5>
                        <p class="card-text"><?= $totalAdmins ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Nombre de Clients</h5>
                        <p class="card-text"><?= $totalClients ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5 class="card-title">Utilisateurs Bannis</h5>
                        <p class="card-text"><?= $totalBanned ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Camembert -->
        <div class="row">
            <div class="col-md-6">
                <div id="pieChartContainer">
                    <canvas id="userPieChart"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div id="banPieChartContainer">
                    <canvas id="banPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des utilisateurs -->
    <div class="container mt-5" id="userTableContainer">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Utilisateurs</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="userTable">
                        <thead class="table-dark">
                            
                            <tr>
        <th><a href="?sort_column=id&sort_direction=<?= $sortColumn == 'id' ? $nextSortDirection : 'ASC' ?>&role=<?= urlencode($selectedRole) ?>">
            ID <i class="fas fa-sort<?= $sortColumn == 'id' ? ($sortDirection == 'ASC' ? '-up' : '-down') : '' ?>"></i></a></th>

        <th><a href="?sort_column=nom&sort_direction=<?= $sortColumn == 'nom' ? $nextSortDirection : 'ASC' ?>&role=<?= urlencode($selectedRole) ?>">
            Nom <i class="fas fa-sort<?= $sortColumn == 'nom' ? ($sortDirection == 'ASC' ? '-up' : '-down') : '' ?>"></i></a></th>

        <th><a href="?sort_column=prenom&sort_direction=<?= $sortColumn == 'prenom' ? $nextSortDirection : 'ASC' ?>&role=<?= urlencode($selectedRole) ?>">
            Prénom <i class="fas fa-sort<?= $sortColumn == 'prenom' ? ($sortDirection == 'ASC' ? '-up' : '-down') : '' ?>"></i></a></th>

        <th><a href="?sort_column=email&sort_direction=<?= $sortColumn == 'email' ? $nextSortDirection : 'ASC' ?>&role=<?= urlencode($selectedRole) ?>">
            Email <i class="fas fa-sort<?= $sortColumn == 'email' ? ($sortDirection == 'ASC' ? '-up' : '-down') : '' ?>"></i></a></th>

        <th><a href="?sort_column=telephone&sort_direction=<?= $sortColumn == 'telephone' ? $nextSortDirection : 'ASC' ?>&role=<?= urlencode($selectedRole) ?>">
            Téléphone <i class="fas fa-sort<?= $sortColumn == 'telephone' ? ($sortDirection == 'ASC' ? '-up' : '-down') : '' ?>"></i></a></th>

        <th><a href="?sort_column=role&sort_direction=<?= $sortColumn == 'role' ? $nextSortDirection : 'ASC' ?>&role=<?= urlencode($selectedRole) ?>">
            Rôle <i class="fas fa-sort<?= $sortColumn == 'role' ? ($sortDirection == 'ASC' ? '-up' : '-down') : '' ?>"></i></a></th>

        <th>Statut</th>
        <th>Action</th>
    </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($rows as $row): ?>

                            <tr data-id="<?= $row['id'] ?>">
                                <td><?= htmlspecialchars($row["id"]) ?></td>
                                
                                <!-- Nom -->
                                <td>
                                    <div class="view-mode"><?= htmlspecialchars($row["nom"]) ?></div>
                                    <div class="edit-form">
                                        <input type="text" class="form-control form-control-sm" name="nom" 
                                               value="<?= htmlspecialchars($row["nom"]) ?>" required
                                               pattern="[a-zA-ZÀ-ÿ\s\-]{2,50}">
                                        <div class="invalid-feedback">Nom invalide</div>
                                    </div>
                                </td>
                                
                                <!-- Prénom -->
                                <td>
                                    <div class="view-mode"><?= htmlspecialchars($row["prenom"]) ?></div>
                                    <div class="edit-form">
                                        <input type="text" class="form-control form-control-sm" name="prenom" 
                                               value="<?= htmlspecialchars($row["prenom"]) ?>" required
                                               pattern="[a-zA-ZÀ-ÿ\s\-]{2,50}">
                                        <div class="invalid-feedback">Prénom invalide</div>
                                    </div>
                                </td>
                                
                                <!-- Email -->
                                <td>
                                    <div class="view-mode"><?= htmlspecialchars($row["email"]) ?></div>
                                    <div class="edit-form">
                                        <input type="email" class="form-control form-control-sm" name="email" 
                                               value="<?= htmlspecialchars($row["email"]) ?>" required>
                                        <div class="invalid-feedback">Email invalide</div>
                                    </div>
                                </td>
                                
                                <!-- Téléphone -->
                                <td>
                                    <div class="view-mode"><?= htmlspecialchars($row["telephone"]) ?></div>
                                    <div class="edit-form">
                                        <input type="tel" class="form-control form-control-sm" name="telephone" 
                                               value="<?= htmlspecialchars($row["telephone"]) ?>" required
                                               pattern="[0-9]{8}" maxlength="8">
                                        <div class="invalid-feedback">8 chiffres exactement</div>
                                    </div>
                                </td>
                                
                                <!-- Rôle -->
                                <td>
                                    <div class="view-mode">
                                        <span class="<?= $row['role'] === 'Admin' ? 'badge-admin' : 'badge-client'; ?>">
                                            <?= htmlspecialchars($row['role']) ?>
                                        </span>
                                    </div>
                                    <div class="edit-form">
                                        <select class="form-select form-select-sm" name="role" required>
                                            <option value="Admin" <?= $row['role'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
                                            <option value="Client" <?= $row['role'] === 'Client' ? 'selected' : '' ?>>Client</option>
                                        </select>
                                        <div class="invalid-feedback">Rôle invalide</div>
                                    </div>
                                </td>
                                
                                <!-- Statut -->
                                <td>
                                    <?php if ($row['is_banned']): ?>
                                        <span class="badge bg-danger">Banni</span>
                                        <div class="ban-details">
                                            <div class="ban-reason">Raison: <?= htmlspecialchars($row['ban_reason'] ?? 'Non spécifiée') ?></div>
                                            <?php if (!empty($row['ban_until'])): ?>
                                                <div class="ban-until">Jusqu'au: <?= date('d/m/Y', strtotime($row['ban_until'])) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-success">Actif</span>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- Actions -->
                                <td>
                                    <div class="view-mode">
                                        <button class="btn btn-outline-primary btn-sm me-2 edit-btn">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm" onclick="confirmDelete(<?= $row['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <div class="edit-form">
                                        <button class="btn btn-outline-success btn-sm me-2 save-btn" data-id="<?= $row['id'] ?>">
                                            <i class="fas fa-save"></i>
                                        </button>
                                        <button class="btn btn-outline-secondary btn-sm cancel-btn">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table><form method="GET" class="mb-3 d-flex justify-content-end">
    <label class="me-2">Filtrer par rôle :</label>
    <select name="role" class="form-select w-auto me-2" onchange="this.form.submit()">
        <option value="">Tous</option>
        <option value="Admin" <?= isset($_GET['role']) && $_GET['role'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
        <option value="Client" <?= isset($_GET['role']) && $_GET['role'] === 'Client' ? 'selected' : '' ?>>Client</option>
    </select>
</form>


                    <a href="export_users.php" class="btn btn-outline-success mt-3">
                        <i class="fas fa-file-csv"></i> Exporter en CSV
                    </a>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <nav>
            <ul class="pagination justify-content-center mt-3">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&role=<?= urlencode($selectedRole) ?>"><?= $i ?></a>

                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <!-- Section Gestion des Comptes -->
    <div class="container mt-5" id="accountManagementSection">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Gestion des Comptes</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover">

                <thead class="table-dark">
                    <tr>
                    <th><a href="?page_roles=<?= $page_roles ?>&sort_column_roles=id&sort_direction_roles=<?= $sortColumnRoles == 'id' ? $nextSortDirectionRoles : 'ASC' ?>#accountManagementSection">
    ID <i class="fas fa-sort<?= $sortColumnRoles == 'id' ? ($sortDirectionRoles == 'ASC' ? '-up' : '-down') : '' ?>"></i>
</a></th>

<th><a href="?page_roles=<?= $page_roles ?>&sort_column_roles=nom&sort_direction_roles=<?= $sortColumnRoles == 'nom' ? $nextSortDirectionRoles : 'ASC' ?>#accountManagementSection">
    Nom <i class="fas fa-sort<?= $sortColumnRoles == 'nom' ? ($sortDirectionRoles == 'ASC' ? '-up' : '-down') : '' ?>"></i>
</a></th>

<th><a href="?page_roles=<?= $page_roles ?>&sort_column_roles=prenom&sort_direction_roles=<?= $sortColumnRoles == 'prenom' ? $nextSortDirectionRoles : 'ASC' ?>#accountManagementSection">
    Prénom <i class="fas fa-sort<?= $sortColumnRoles == 'prenom' ? ($sortDirectionRoles == 'ASC' ? '-up' : '-down') : '' ?>"></i>
</a></th>

<th><a href="?page_roles=<?= $page_roles ?>&sort_column_roles=email&sort_direction_roles=<?= $sortColumnRoles == 'email' ? $nextSortDirectionRoles : 'ASC' ?>#accountManagementSection">
    Email <i class="fas fa-sort<?= $sortColumnRoles == 'email' ? ($sortDirectionRoles == 'ASC' ? '-up' : '-down') : '' ?>"></i>
</a></th>

<th><a href="?page_roles=<?= $page_roles ?>&sort_column_roles=role&sort_direction_roles=<?= $sortColumnRoles == 'role' ? $nextSortDirectionRoles : 'ASC' ?>#accountManagementSection">
    Rôle <i class="fas fa-sort<?= $sortColumnRoles == 'role' ? ($sortDirectionRoles == 'ASC' ? '-up' : '-down') : '' ?>"></i>
</a></th>

                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                <?php foreach ($rows_roles as $row): ?>

                    <tr>
                        <td><?= htmlspecialchars($row["id"]) ?></td>
                        <td><?= htmlspecialchars($row["nom"]) ?></td>
                        <td><?= htmlspecialchars($row["prenom"]) ?></td>
                        <td><?= htmlspecialchars($row["email"]) ?></td>
                        <td><?= htmlspecialchars($row["role"]) ?></td>
                        <td>
                            <?php if ($row['is_banned']): ?>
                                <span class="badge bg-danger">Banni</span>
                                <div class="ban-details">
                                    <div class="ban-reason">Raison: <?= htmlspecialchars($row['ban_reason'] ?? 'Non spécifiée') ?></div>
                                    <?php if (!empty($row['ban_until'])): ?>
                                        <div class="ban-until">Jusqu'au: <?= date('d/m/Y', strtotime($row['ban_until'])) ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <span class="badge bg-success">Actif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['is_banned']): ?>
                                <button class="btn btn-outline-success btn-sm me-2" onclick="toggleBan(<?= $row['id'] ?>, false)">
                                    <i class="fas fa-user-check"></i> Débannir
                                </button>
                            <?php else: ?>
                                <button class="btn btn-outline-danger btn-sm me-2" onclick="showBanModal(<?= $row['id'] ?>)">
                                    <i class="fas fa-user-slash"></i> Bannir
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <nav>
    <ul class="pagination justify-content-center mt-3">
        <?php for ($i = 1; $i <= $totalPagesRoles; $i++): ?>
            <li class="page-item <?= $i === $page_roles ? 'active' : '' ?>">
                <a class="page-link" href="?page_roles=<?= $i ?>#accountManagementSection"><?= $i ?></a>
            </li>
        <?php endfor; ?>
    </ul>
    <a href="export_roles.php" class="btn btn-outline-success mb-3">
    <i class="fas fa-file-csv"></i> Exporter les rôles en CSV
</a>

</nav>

        </div>
    </div>
</div>

<!-- Modal de bannissement stylisé -->
<div class="modal fade" id="banModal" tabindex="-1" aria-labelledby="banModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="banModalLabel">
                    <i class="fas fa-user-slash me-2"></i> Bannir un utilisateur
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <form id="banForm">
                    <input type="hidden" id="banUserId">

                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <div>
                            Cette action empêchera l'utilisateur de se connecter jusqu'à la date spécifiée.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="banReason" class="form-label">Raison du bannissement</label>
                        <select class="form-select" id="banReason" required onchange="toggleOtherReason(this.value)">
                            <option value="">-- Sélectionnez une raison --</option>
                            <option value="Violation des règles">Violation des règles</option>
                            <option value="Comportement inapproprié">Comportement inapproprié</option>
                            <option value="Spam">Spam</option>
                            <option value="Autre">Autre</option>
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="otherReasonContainer">
                        <label for="otherReason" class="form-label">Veuillez préciser</label>
                        <input type="text" class="form-control" id="otherReason" placeholder="Détail de la raison">
                    </div>

                    <div class="mb-3">
                        <label for="banUntil" class="form-label">Bannir jusqu'au (optionnel)</label>
                        <input type="date" class="form-control" id="banUntil">
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times-circle me-1"></i> Annuler
                </button>
                <button type="button" class="btn btn-danger" onclick="confirmBan()">
                    <i class="fas fa-ban me-1"></i> Confirmer le bannissement
                </button>
            </div>
        </div>
    </div>
</div>
<script>
function toggleOtherReason(value) {
    const otherContainer = document.getElementById('otherReasonContainer');
    if (value === 'Autre') {
        otherContainer.classList.remove('d-none');
        document.getElementById('otherReason').required = true;
    } else {
        otherContainer.classList.add('d-none');
        document.getElementById('otherReason').required = false;
    }
}
</script>


<!-- JS Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Variables pour stocker les graphiques
    let userPieChart = null;
    let banPieChart = null;
    let banModal = null;

    document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const pageRolesParam = urlParams.get('page_roles');

    // Si on vient pour les rôles, afficher directement la gestion des comptes
    if (pageRolesParam !== null) {
        toggleAccountManagement();
    } else {
        toggleUserTable(); // sinon afficher le tableau des utilisateurs
    }

        
        // Gestion de l'édition en ligne
        initInlineEditing();
        
        // Initialiser le modal
        banModal = new bootstrap.Modal(document.getElementById('banModal'));
        
        // Fermeture automatique des alertes après 5 secondes
        const alert = document.querySelector('.alert');
        if (alert) {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        }

        // Validation du formulaire d'ajout
        const addForm = document.getElementById('addUserFormElement');
        if (addForm) {
            addForm.addEventListener('submit', function(event) {
                if (!this.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                this.classList.add('was-validated');
            }, false);
        }

        // Restreindre le champ téléphone à 8 chiffres uniquement
        const phoneInput = document.getElementById('telephone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function() {
                // N'autoriser que les chiffres
                this.value = this.value.replace(/[^0-9]/g, '');
                // Limiter à 8 caractères
                if (this.value.length > 8) {
                    this.value = this.value.slice(0, 8);
                }
            });
        }
    });

    function initInlineEditing() {
        // Bouton Éditer
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                toggleEditMode(row, true);
            });
        });

        // Bouton Annuler
        document.querySelectorAll('.cancel-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                toggleEditMode(row, false);
            });
        });

        // Bouton Sauvegarder
        document.querySelectorAll('.save-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const id = this.getAttribute('data-id');
                
                // Vérifier la validité des champs avant soumission
                let isValid = true;
                row.querySelectorAll('.edit-form input, .edit-form select').forEach(input => {
                    if (!input.checkValidity()) {
                        input.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });

                if (isValid) {
                    saveUserChanges(row, id);
                } else {
                    // Afficher un message d'erreur
                    Swal.fire({
                        title: 'Erreur',
                        text: 'Veuillez corriger les champs invalides',
                        icon: 'error'
                    });
                }
            });
        });

        // Validation en temps réel pour les champs d'édition
        document.querySelectorAll('.edit-form input').forEach(input => {
            input.addEventListener('input', function() {
                if (this.name === 'telephone') {
                    // N'autoriser que les chiffres
                    this.value = this.value.replace(/[^0-9]/g, '');
                    // Limiter à 8 caractères
                    if (this.value.length > 8) {
                        this.value = this.value.slice(0, 8);
                    }
                }
                
                if (this.checkValidity()) {
                    this.classList.remove('is-invalid');
                } else {
                    this.classList.add('is-invalid');
                }
            });
        });
    }

    function toggleEditMode(row, isEdit) {
        // Basculer entre les modes vue et édition
        row.querySelectorAll('.view-mode').forEach(el => {
            el.classList.toggle('hidden', isEdit);
        });
        row.querySelectorAll('.edit-form').forEach(el => {
            el.classList.toggle('active', isEdit);
        });

        // Réinitialiser les états de validation
        if (!isEdit) {
            row.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
        }
    }

    function saveUserChanges(row, id) {
        // Récupérer les valeurs modifiées
        const nom = row.querySelector('input[name="nom"]').value;
        const prenom = row.querySelector('input[name="prenom"]').value;
        const email = row.querySelector('input[name="email"]').value;
        const telephone = row.querySelector('input[name="telephone"]').value;
        const role = row.querySelector('select[name="role"]').value;

        // Créer un formulaire dynamique pour la soumission
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>';
        
        // Ajouter les champs cachés
        const addField = (name, value) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            form.appendChild(input);
        };

        addField('action', 'update');
        addField('id', id);
        addField('nom', nom);
        addField('prenom', prenom);
        addField('email', email);
        addField('telephone', telephone);
        addField('role', role);

        // Soumettre le formulaire
        document.body.appendChild(form);
        form.submit();
    }

   
    function confirmDelete(userId) {
        Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: "Cette action est irréversible !",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer !',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>?delete=' + userId + '&page=<?= $page ?>';
            }
        });
    }

    function showBanModal(userId) {
        document.getElementById('banUserId').value = userId;
        document.getElementById('banReason').value = '';
        document.getElementById('banUntil').value = '';
        banModal.show();
    }

    function confirmBan() {
        const userId = document.getElementById('banUserId').value;
        const reason = document.getElementById('banReason').value;
        const banUntil = document.getElementById('banUntil').value;

        if (!reason) {
            Swal.fire({
                title: 'Erreur',
                text: 'Veuillez sélectionner une raison de bannissement',
                icon: 'error'
            });
            return;
        }

        banModal.hide();
        
        let url = `<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>?ban=${userId}&reason=${encodeURIComponent(reason)}`;
        if (banUntil) {
            url += `&ban_until=${banUntil}`;
        }
        url += `&page=<?= $page ?>`;
        
        window.location.href = url;
    }

    function toggleBan(userId, ban) {
        if (ban) {
            showBanModal(userId);
        } else {
            Swal.fire({
                title: 'Débannir cet utilisateur ?',
                text: "L'utilisateur pourra à nouveau se connecter.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Oui, débannir',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>?unban=${userId}&page=<?= $page ?>`;
                }
            });
        }
    }

    function toggleSidebar() {
        document.getElementById("sidebar").classList.toggle("active");
    }

    function toggleAddForm() {
        const addForm = document.getElementById("addUserForm");
        const statsSection = document.getElementById("statsSection");
        const userTable = document.getElementById("userTableContainer");
        const accountManagement = document.getElementById("accountManagementSection");

        addForm.style.display = "block";
        statsSection.style.display = "none";
        userTable.style.display = "none";
        accountManagement.style.display = "none";
    }

    function toggleStats() {
        const addForm = document.getElementById("addUserForm");
        const statsSection = document.getElementById("statsSection");
        const userTable = document.getElementById("userTableContainer");
        const accountManagement = document.getElementById("accountManagementSection");

        addForm.style.display = "none";
        statsSection.style.display = "block";
        userTable.style.display = "none";
        accountManagement.style.display = "none";

        // Créer les graphiques seulement si c'est la première fois
        if (!userPieChart) {
            createPieChart();
        }
        if (!banPieChart) {
            createBanPieChart();
        }
    }

    function toggleUserTable() {
        const addForm = document.getElementById("addUserForm");
        const statsSection = document.getElementById("statsSection");
        const userTable = document.getElementById("userTableContainer");
        const accountManagement = document.getElementById("accountManagementSection");

        addForm.style.display = "none";
        statsSection.style.display = "none";
        userTable.style.display = "block";
        accountManagement.style.display = "none";
    }

    function toggleAccountManagement() {
        const addForm = document.getElementById("addUserForm");
        const statsSection = document.getElementById("statsSection");
        const userTable = document.getElementById("userTableContainer");
        const accountManagement = document.getElementById("accountManagementSection");

        addForm.style.display = "none";
        statsSection.style.display = "none";
        userTable.style.display = "none";
        accountManagement.style.display = "block";
    }

    function filterTable() {
        const filter = document.getElementById("searchInput").value.toLowerCase();
        const rows = document.querySelectorAll("#userTable tbody tr");

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? "" : "none";
        });
    }

    function createPieChart() {
        const ctx = document.getElementById('userPieChart').getContext('2d');
        userPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Admins', 'Clients'],
                datasets: [{
                    data: [<?= $totalAdmins ?>, <?= $totalClients ?>],
                    backgroundColor: ['#f39c12', '#3498db'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Répartition des rôles'
                    }
                }
            }
        });
    }

    function createBanPieChart() {
        const ctx = document.getElementById('banPieChart').getContext('2d');
        banPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Actifs', 'Bannis'],
                datasets: [{
                    data: [<?= $totalActive ?>, <?= $totalBanned ?>],
                    backgroundColor: ['#28a745', '#dc3545'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Statut des comptes'
                    }
                }
            }
        });
    }
</script>
</body>
</html>