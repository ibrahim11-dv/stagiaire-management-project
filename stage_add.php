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


// Process form submission for new stage
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $sujet = mysqli_real_escape_string($conDb, trim($_POST['sujet']));
    $date_debut = mysqli_real_escape_string($conDb, trim($_POST['date_debut']));
    $date_fin = mysqli_real_escape_string($conDb, trim($_POST['date_fin']));
    $objectifs = mysqli_real_escape_string($conDb, trim($_POST['objectifs']));
    $annee_universitaire = mysqli_real_escape_string($conDb, trim($_POST['annee_universitaire']));
    $type_id = intval($_POST['type_id']);
    
    // Insert into database
    $sql = "INSERT INTO stage (sujet, date_debut, date_fin, objectifs, annee_universitaire, type_id) 
            VALUES ('$sujet', '$date_debut', '$date_fin', '$objectifs', '$annee_universitaire', $type_id)";
    
    $event="INSERT INTO activitie (
            type_name,action,name
        ) VALUES (
            'encadrant','ajouté','$sujet'
        )";
    mysqli_query($conDb,$event);
    
    if (mysqli_query($conDb, $sql)) {
        $stage_id = mysqli_insert_id($conDb);
        $_SESSION['success_message'] = 'Stage ajouté avec succès!';
        header("Location: stage_details.php?id=$stage_id");
        exit();
    } else {
        $message = "Erreur: " . mysqli_error($conDb);
        $alert_class = "alert-danger";
    }
}

// Get all stage types
$types_query = "SELECT id, intitule FROM type_stage";
$types_result = mysqli_query($conDb, $types_query);
$types = [];
while($row = $types_result->fetch_assoc()) {
    $types[] = $row;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Stage - Plateforme Stagiaires</title>
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
        .card {
            border: none;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }
        
        .stage-icon-section {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            margin-bottom: 2rem;
        }
        
        .stage-icon {
            font-size: 3rem;
            color: #dc3545;
            margin-bottom: 1rem;
        }
        
        .form-section {
            background: white;
            border-radius: 0.35rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }
        
        .form-section h5 {
            color: #dc3545;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #e3e6f0;
        }
        
        .form-control, .form-select {
            border-radius: 0.35rem;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d3e2;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        
        .btn-primary {
            background: #dc3545;
            border: none;
            border-radius: 0.35rem;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background: #bb2d3b;
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }
        
        .btn-outline-primary {
            color: #dc3545;
            border-color: #dc3545;
        }
        
        .btn-outline-primary:hover {
            background-color: #dc3545;
            color: white;
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
            <div class="col-md-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Ajouter un Nouveau Stage</h2>
                    <div class="d-flex align-items-center">
                        <span class="me-3"></span>
                        <img src="" class="rounded-circle">
                    </div>
                </div>

                <!-- Back Button -->
                <div class="mb-4">
                    <a href="stages.php" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-2"></i> Retour à la liste
                    </a>
                </div>

                <?php if (isset($message)): ?>
                    <div class="alert <?= $alert_class ?> alert-dismissible fade show" role="alert">
                        <?= $message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <!-- Stage Icon Section -->
                    <div class="stage-icon-section">
                        <div class="stage-icon">
                            <i class="bi bi-briefcase"></i>
                        </div>
                        <h4>Nouveau Stage</h4>
                        <p class="text-muted">Remplissez les détails du stage</p>
                    </div>
                    
                    <!-- Stage Information Section -->
                    <div class="form-section">
                        <h5><i class="bi bi-info-circle me-2"></i>Informations du Stage</h5>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Sujet du stage</label>
                                <input type="text" name="sujet" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date de début</label>
                                <input type="date" name="date_debut" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date de fin</label>
                                <input type="date" name="date_fin" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Année universitaire</label>
                                <input type="text" name="annee_universitaire" class="form-control" placeholder="Ex: 2023-2024" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Type de stage</label>
                                <select name="type_id" class="form-select" required>
                                    <?php foreach ($types as $type): ?>
                                        <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['intitule']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Objectifs</label>
                                <textarea name="objectifs" class="form-control" rows="3" required></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Form Buttons -->
                    <div class="d-flex justify-content-end mt-4">
                        <a href="stages.php" class="btn btn-outline-primary me-2">
                            <i class="bi bi-x-circle me-2"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>