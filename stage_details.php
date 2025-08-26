<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: logout.php");
    exit();
}

include("config.php");

// Get user data from session
$username = $_SESSION['username'];
$email = $_SESSION['email'];
$telephone = $_SESSION['telephone'];
$nom = $_SESSION['nom'];
$prenom = $_SESSION['prenom'];
$image = $_SESSION['avatar'];

// Get stage ID from URL
$stage_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_editing = isset($_GET['edit']) && $_GET['edit'] == 'true';
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stage'])) {
    // Validate and sanitize input data
    $update_data = [
        'sujet' => mysqli_real_escape_string($conDb, trim($_POST['sujet'])),
        'date_debut' => mysqli_real_escape_string($conDb, trim($_POST['date_debut'])),
        'date_fin' => mysqli_real_escape_string($conDb, trim($_POST['date_fin'])),
        'objectifs' => mysqli_real_escape_string($conDb, trim($_POST['objectifs'])),
        'annee_universitaire' => mysqli_real_escape_string($conDb, trim($_POST['annee_universitaire'])),
        'type_id' => intval($_POST['type_id'])
    ];
    
    // Only proceed with update if no errors occurred
    if (empty($message)) {
        // Build the update query
        $update_fields = [];
        foreach ($update_data as $field => $value) {
            $update_fields[] = "$field = '" . $value . "'";
        }
        
        $update_query = "UPDATE stage SET " . implode(', ', $update_fields) . " WHERE id = $stage_id";
        
        if (mysqli_query($conDb, $update_query)) {

            $sujetevent=$update_data['sujet'];
            $event="INSERT INTO activitie (
                            type_name,action,name
                        ) VALUES (
                            'stage','modifié','$sujetevent'
                        )";
            mysqli_query($conDb,$event);

            $_SESSION['success_message'] = 'Modifications enregistrées avec succès!';
            // Refresh the page to show updated data
            header("Location: stage_details.php?id=$stage_id");
            exit();
        } else {
            $message = 'danger:Erreur lors de la mise à jour: ' . mysqli_error($conDb);
        }
    }
}

// Check for success message from session
if (isset($_SESSION['success_message'])) {
    $message = 'success:' . $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Get stage information
$stage_query = "SELECT * FROM stage WHERE id = $stage_id AND est_supp = false";
$stage_result = mysqli_query($conDb, $stage_query);
$stage = $stage_result->fetch_assoc();


if (!$stage) {
    header("Location: stages.php");
    exit();
}

// Get all stage types
$types_query = "SELECT id, intitule FROM type_stage";
$types_result = mysqli_query($conDb, $types_query);
$types = [];
while($row = $types_result->fetch_assoc()) {
    $types[] = $row;
}

// Get supervisors assigned to this stage
$supervisors_query = "SELECT e.id, e.nom, e.prenom, e.email, e.telephone, e.poste, e.service_id, e.status 
                      FROM encadrant e 
                      WHERE e.stage_id = $stage_id AND e.est_supp = false
                      ORDER BY e.nom, e.prenom";
$supervisors_result = mysqli_query($conDb, $supervisors_query);
$supervisors = [];
while($row = $supervisors_result->fetch_assoc()) {
    $supervisors[] = $row;
}

// Get trainees assigned to this stage
$trainees_query = "SELECT s.id, s.nom, s.prenom, s.cin, s.email, s.telephone, s.diplome, s.status, s.encadrant_id
                   FROM stagiaire s 
                   WHERE s.stage_id = $stage_id AND s.est_supp = false
                   ORDER BY s.nom, s.prenom";
$trainees_result = mysqli_query($conDb, $trainees_query);
$trainees = [];
while($row = $trainees_result->fetch_assoc()) {
    $trainees[] = $row;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_editing ? 'Modifier' : 'Détails' ?> Stage - Plateforme Stagiaires</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #212529;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
        }
        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link.active {
            color: white;
            background-color: #dc3545;
        }
        .main-content {
            background-color: #f8f9fa;
        }
        .profile-section {
            background: linear-gradient(135deg, #495057 0%, #6c757d 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            border: 1px solid #dc3545;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border: none;
            margin-bottom: 20px;
            transition: transform 0.3s;
            border-top: 3px solid #dc3545;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: none;
            border-radius: 15px 15px 0 0 !important;
            font-weight: 600;
            color: #495057;
        }
        .status-badge {
            font-size: 0.9rem;
            padding: 0.4em 0.8em;
            background-color: #dc3545;
        }
        .btn-edit {
            border-radius: 50px;
            padding: 8px 20px;
        }
        .form-control, .form-select {
            border-radius: 10px;
            padding: 10px 15px;
            border: 1px solid #ced4da;
        }
        .form-control:focus, .form-select:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
        }
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            border: none;
        }
        .toast-success {
            background-color: #28a745;
        }
        .toast-danger {
            background-color: #dc3545;
        }
        .nav-link {
            display: flex;
            align-items: center;
        }
        .nav-link i {
            margin-right: 10px;
        }
        .btn-primary {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .btn-primary:hover {
            background-color: #bb2d3b;
            border-color: #b02a37;
        }
        .btn-outline-primary {
            color: #dc3545;
            border-color: #dc3545;
        }
        .btn-outline-primary:hover {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .text-primary {
            color: #dc3545 !important;
        }
        .status-active {
            background-color: #d1e7dd;
            color: #0f5132;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 0.85rem;
        }
        .status-inactive {
            background-color: #f8d7da;
            color: #842029;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 0.85rem;
        }
        .progress {
            height: 10px;
            border-radius: 5px;
        }
        .progress-bar {
            background-color: #dc3545;
        }
        .stage-icon {
            font-size: 3rem;
            color: #dc3545;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="p-3 text-white">
                    <h4 class="d-flex align-items-center">
                        <img src="assets/logo-removebg.png" width="40" height="40" class="me-2">
                        Wilaya Stages
                    </h4>
                    <hr class="bg-light">
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="stagiaires.php">
                            <i class="bi bi-people"></i> Stagiaires
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="encadrants.php">
                            <i class="bi bi-person-badge"></i> Encadrants
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="stages.php">
                            <i class="bi bi-briefcase"></i> Stages
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="demandes.php">
                            <i class="bi bi-person-add me-2"></i> Demandes
                        </a>
                    </li>
                    <li class="nav-item mt-3">
                        <a class="nav-link" href="logout.php">
                            <i class="bi bi-box-arrow-left"></i> Déconnexion
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0"><?= $is_editing ? 'Modifier Stage' : 'Détails du Stage' ?></h2>
                    <div class="d-flex align-items-center">
                        <span class="me-3"></span>
                        <img src="" class="rounded-circle">
                    </div>
                </div>

                <!-- Back Button -->
                <div class="mb-4">
                    <a href="stages.php" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-2"></i>Retour à la liste
                    </a>
                </div>

                <?php if ($message): ?>
                <div class="toast show align-items-center text-white <?= explode(':', $message)[0] == 'success' ? 'toast-success' : 'toast-danger' ?>" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi <?= explode(':', $message)[0] == 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle' ?> me-2"></i>
                            <?= explode(':', $message)[1] ?>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
                <?php endif; ?>

                <form method="POST" action="stage_details.php?id=<?= $stage_id ?>">
                    <div class="row">
                        <!-- Left Column - Stage Info -->
                        <div class="col-lg-4">
                            <div class="profile-section text-center mb-4">
                                <div class="stage-icon">
                                    <i class="bi bi-briefcase"></i>
                                </div>
                                
                                <?php if ($is_editing): ?>
                                    <input type="text" name="sujet" class="form-control form-control-lg text-center fw-bold mb-3" value="<?= htmlspecialchars($stage['sujet']) ?>" required>
                                <?php else: ?>
                                    <h2 class="mb-3"><?= htmlspecialchars($stage['sujet']) ?></h2>
                                    <span class="badge status-badge">
                                        <?= htmlspecialchars($stage['annee_universitaire']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Progress -->
                            <div class="card mb-4">
                                <div class="card-header d-flex align-items-center">
                                    <i class="bi bi-calendar-range me-2"></i>
                                    <span>Progression du stage</span>
                                </div>
                                <div class="card-body">
                                    <?php
                                        $start_date = new DateTime($stage['date_debut']);
                                        $end_date = new DateTime($stage['date_fin']);
                                        $today = new DateTime();
                                        $total_days = $start_date->diff($end_date)->days;
                                        $days_passed = $start_date->diff($today)->days;
                                        $progress = min(100, max(0, ($days_passed / $total_days) * 100));
                                        
                                        $status_class = 'status-active';
                                        $status_text = 'En cours';
                                        
                                        if ($today < $start_date) {
                                            $status_class = 'status-inactive';
                                            $status_text = 'Pas encore commencé';
                                        } elseif ($today > $end_date) {
                                            $status_class = 'status-inactive';
                                            $status_text = 'Terminé';
                                        }
                                    ?>
                                    <div class="mb-3">
                                        <span class="<?= $status_class ?>"><?= $status_text ?></span>
                                    </div>
                                    <div class="progress mb-2">
                                        <div class="progress-bar" role="progressbar" style="width: <?= $progress ?>%" 
                                             aria-valuenow="<?= $progress ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="d-flex justify-content-between small text-muted">
                                        <span><?= $stage['date_debut'] ?></span>
                                        <span><?= $stage['date_fin'] ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column - Details -->
                        <div class="col-lg-8">
                            <!-- Stage Information -->
                            <div class="card mb-4">
                                <div class="card-header d-flex align-items-center">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <span>Informations du stage</span>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Date de début</label>
                                            <?php if ($is_editing): ?>
                                                <input type="date" name="date_debut" class="form-control" value="<?= htmlspecialchars($stage['date_debut']) ?>" required>
                                            <?php else: ?>
                                                <p class="form-control-plaintext"><?= htmlspecialchars($stage['date_debut']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Date de fin</label>
                                            <?php if ($is_editing): ?>
                                                <input type="date" name="date_fin" class="form-control" value="<?= htmlspecialchars($stage['date_fin']) ?>" required>
                                            <?php else: ?>
                                                <p class="form-control-plaintext"><?= htmlspecialchars($stage['date_fin']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Année universitaire</label>
                                            <?php if ($is_editing): ?>
                                                <input type="text" name="annee_universitaire" class="form-control" value="<?= htmlspecialchars($stage['annee_universitaire']) ?>" required>
                                            <?php else: ?>
                                                <p class="form-control-plaintext"><?= htmlspecialchars($stage['annee_universitaire']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Type de stage</label>
                                            <?php if ($is_editing): ?>
                                                <select name="type_id" class="form-select">
                                                    <?php foreach ($types as $type): ?>
                                                        <option value="<?= $type['id'] ?>" <?= $stage['type_id'] == $type['id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($type['intitule']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php else: ?>
                                                <?php 
                                                    $type_name = 'Non spécifié';
                                                    foreach ($types as $type) {
                                                        if ($type['id'] == $stage['type_id']) {
                                                            $type_name = $type['intitule'];
                                                            break;
                                                        }
                                                    }
                                                ?>
                                                <p class="form-control-plaintext"><?= htmlspecialchars($type_name) ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="form-label">Objectifs</label>
                                            <?php if ($is_editing): ?>
                                                <textarea name="objectifs" class="form-control" rows="3"><?= htmlspecialchars($stage['objectifs']) ?></textarea>
                                            <?php else: ?>
                                                <p class="form-control-plaintext"><?= htmlspecialchars($stage['objectifs']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Encadrants -->
                            <div class="card mb-4">
                                <div class="card-header d-flex align-items-center">
                                    <i class="bi bi-person-badge me-2"></i>
                                    <span>Encadrants</span>
                                </div>
                                <div class="card-body">
                                    <?php if (count($supervisors) > 0): ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Nom complet</th>
                                                        <th>Poste</th>
                                                        <th>Email</th>
                                                        <th>Téléphone</th>
                                                        <th>Statut</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($supervisors as $supervisor): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($supervisor['nom']) ?> <?= htmlspecialchars($supervisor['prenom']) ?></td>
                                                            <td><?= htmlspecialchars($supervisor['poste']) ?></td>
                                                            <td><?= htmlspecialchars($supervisor['email']) ?></td>
                                                            <td><?= htmlspecialchars($supervisor['telephone']) ?></td>
                                                            <td>
                                                                <span class="<?= $supervisor['status'] == 'en cour' ? 'status-active' : 'status-inactive' ?>">
                                                                    <?= htmlspecialchars($supervisor['status']) ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <a href="encadrants_details.php?id=<?= $supervisor['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                                    <i class="bi bi-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            Aucun encadrant n'est actuellement assigné à ce stage.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Stagiaires -->
                            <div class="card">
                                <div class="card-header d-flex align-items-center">
                                    <i class="bi bi-people me-2"></i>
                                    <span>Stagiaires</span>
                                </div>
                                <div class="card-body">
                                    <?php if (count($trainees) > 0): ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Nom complet</th>
                                                        <th>CIN</th>
                                                        <th>Email</th>
                                                        <th>Téléphone</th>
                                                        <th>Encadrant</th>
                                                        <th>Statut</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($trainees as $trainee): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($trainee['nom']) ?> <?= htmlspecialchars($trainee['prenom']) ?></td>
                                                            <td><?= htmlspecialchars($trainee['cin']) ?></td>
                                                            <td><?= htmlspecialchars($trainee['email']) ?></td>
                                                            <td><?= htmlspecialchars($trainee['telephone']) ?></td>
                                                            <td>
                                                                <?php
                                                                    $encadrant_name = 'Non assigné';
                                                                    foreach ($supervisors as $supervisor) {
                                                                        if ($supervisor['id'] == $trainee['encadrant_id']) {
                                                                            $encadrant_name = $supervisor['prenom'] . ' ' . $supervisor['nom'];
                                                                            break;
                                                                        }
                                                                    }
                                                                    echo htmlspecialchars($encadrant_name);
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <span class="<?= $trainee['status'] == 'en cour' ? 'status-active' : 'status-inactive' ?>">
                                                                    <?= htmlspecialchars($trainee['status']) ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <a href="stagiaires_details.php?id=<?= $trainee['id'] ?>" class="btn btn-sm btn-outline-primary">
                                                                    <i class="bi bi-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            Aucun stagiaire n'est actuellement inscrit à ce stage.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Buttons -->
                    <div class="d-flex justify-content-end mt-4">
                        <?php if ($is_editing): ?>
                            <a href="stage_details.php?id=<?= $stage_id ?>" class="btn btn-outline-secondary me-2">
                                <i class="bi bi-x-circle me-2"></i>Annuler
                            </a>
                            <button type="submit" name="update_stage" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Enregistrer
                            </button>
                        <?php else: ?>
                            <a href="stage_details.php?id=<?= $stage_id ?>&edit=true" class="btn btn-primary">
                                <i class="bi bi-pencil me-2"></i>Modifier
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Hide toast after 5 seconds
        setTimeout(() => {
            const toast = document.querySelector('.toast');
            if (toast) {
                toast.classList.add('hide');
                setTimeout(() => toast.remove(), 500);
            }
        }, 5000);
    </script>
</body>
</html>