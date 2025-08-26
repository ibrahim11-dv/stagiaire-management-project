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

// Get trainee ID from URL
$trainee_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_editing = isset($_GET['edit']) && $_GET['edit'] == 'true';
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stagiaire'])) {
    // Validate and sanitize input data
    $update_data = [
        'prenom' => mysqli_real_escape_string($conDb, trim($_POST['prenom'])),
        'nom' => mysqli_real_escape_string($conDb, trim($_POST['nom'])),
        'cin' => mysqli_real_escape_string($conDb, trim($_POST['cin'])),
        'email' => mysqli_real_escape_string($conDb, trim($_POST['email'])),
        'telephone' => mysqli_real_escape_string($conDb, trim($_POST['telephone'])),
        'sexe' => mysqli_real_escape_string($conDb, $_POST['sexe']),
        'diplome' => mysqli_real_escape_string($conDb, trim($_POST['diplome'])),
        'etablissement' => mysqli_real_escape_string($conDb, trim($_POST['etablissement'])),
        'type_id' => intval($_POST['type_id']),
        'status' => mysqli_real_escape_string($conDb, $_POST['status']),
        'encadrant_id' => intval($_POST['encadrant_id']),
        'stage_id' => intval($_POST['stage_id'])
    ];
    
    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "images/";
        $target_file = $target_dir . basename($_FILES['image']['name']);
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if($check === false) {
            $message = 'danger:Le fichier sélectionné n\'est pas une image valide.';
        } 
        // Check file size (max 2MB)
        elseif ($_FILES['image']['size'] > 20000000) {
            $message = 'danger:Désolé, votre fichier est trop volumineux (max 20MB).';
        }
        // Allow certain file formats
        elseif(!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $message = 'danger:Seuls les fichiers JPG, JPEG, PNG & GIF sont autorisés.';
        }
        // Try to upload file
        elseif (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $update_data['image_url'] = $target_file;
        } else {
            $message = 'danger:Une erreur s\'est produite lors du téléchargement de l\'image.';
        }
    } elseif (!empty($_POST['current_image'])) {
        $update_data['image_url'] = $_POST['current_image'];
    }
    
    // Only proceed with update if no errors occurred
    if (empty($message)) {
        // Build the update query
        $update_fields = [];
        foreach ($update_data as $field => $value) {
            $update_fields[] = "$field = '" . $value . "'";
        }
        
        $update_query = "UPDATE stagiaire SET " . implode(', ', $update_fields) . " WHERE id = $trainee_id";
        
        if (mysqli_query($conDb, $update_query)) {
            $full_name=$update_data['nom'].' '.$update_data['prenom'];
            $event="INSERT INTO activitie (
                            type_name,action,name
                        ) VALUES (
                            'stagiaire','modifié','$full_name'
                        )";
            mysqli_query($conDb,$event);
            $_SESSION['success_message'] = 'Modifications enregistrées avec succès!';
            // Refresh the page to show updated data
            header("Location: dashboard_stagiaire.php?id=$trainee_id");
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

// Get trainee information
$trainee_query = "SELECT * FROM stagiaire WHERE id = $trainee_id AND est_supp = false";
$trainee_result = mysqli_query($conDb, $trainee_query);
$trainee = $trainee_result->fetch_assoc();


if (!$trainee) {
    header("Location: logout.php");
    echo '<script>alert("something when wrong - stagiagire not found");</script>';
    exit();
}

// Stage types
$stage_types = [
    1 => 'PFE',
    2 => 'Initiation',
    3 => 'Perfectionnement',
    4 => 'Ouvrier',
    5 => 'Technicien'
];

// Set default profile image
if (empty($trainee['image_url'])) {
    $trainee['image_url'] = ($trainee['sexe'] == 'homme') ? "images/homme_default.jpg" : "images/femme_default.jpg";
}

// Get supervisor information
$supervisor_id = $trainee['encadrant_id'];
$supervisor_query = "SELECT * FROM encadrant WHERE id = $supervisor_id";
$supervisor_result = mysqli_query($conDb, $supervisor_query);
$supervisor = $supervisor_result->fetch_assoc();

// Get all supervisors
$all_supervisors_query = "SELECT id, CONCAT(prenom, ' ', nom) as full_name, poste FROM encadrant";
$all_supervisors_result = mysqli_query($conDb, $all_supervisors_query);
$all_supervisors = [];
while($row = $all_supervisors_result->fetch_assoc()) {
    $all_supervisors[] = $row;
}

// Get stage information
$stage_id = $trainee['stage_id'];
$stage_query = "SELECT * FROM stage WHERE id = $stage_id";
$stage_result = mysqli_query($conDb, $stage_query);
$stage = $stage_result->fetch_assoc();

// Get all stages
$all_stages_query = "SELECT id, sujet, date_debut, date_fin, annee_universitaire FROM stage";
$all_stages_result = mysqli_query($conDb, $all_stages_query);
$all_stages = [];
while($row = $all_stages_result->fetch_assoc()) {
    $all_stages[] = $row;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_editing ? 'Modifier' : 'Détails' ?> Stagiaire - Plateforme Stagiaires</title>
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
        .profile-img {
            border-radius: 50%;
            width: 180px;
            height: 180px;
            object-fit: cover;
            border: 3px solid #dc3545;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }
        .profile-img:hover {
            transform: scale(1.05);
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
                    <h2 class="mb-0"><?= $is_editing ? 'Modifier Stagiaire' : 'Détails du Stagiaire' ?></h2>
                    <div class="d-flex align-items-center">
                        <span class="me-3"></span>
                        <img src="" class="rounded-circle">
                    </div>
                </div>

                <!-- Back Button -->
                <div class="mb-4">
                    <a href="stagiaires.php" class="btn btn-outline-primary">
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

                <form method="POST" action="dashboard_stagiaire.php?id=<?= $trainee_id ?>" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Left Column - Profile Image and Basic Info -->
                        <div class="col-lg-4">
                            <div class="profile-section text-center mb-4">
                                <div class="position-relative d-inline-block">
                                    <img src="<?= htmlspecialchars($trainee['image_url']) ?>" class="profile-img mb-3 " alt="Photo de profil">
                                    <?php if ($is_editing): ?>
                                        <input type="file" name="image" class="d-none" id="imageUpload" accept="image/*">
                                        <label for="imageUpload" class="btn btn-sm btn-light position-absolute bottom-0 end-0 rounded-circle">
                                            <i class="bi bi-camera"></i>
                                        </label>
                                        <input type="hidden" name="current_image" value="<?= htmlspecialchars($trainee['image_url']) ?>">
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($is_editing): ?>
                                    <input type="text" name="prenom" class="form-control form-control-lg text-center fw-bold mb-2" value="<?= htmlspecialchars($trainee['prenom']) ?>" required>
                                    <input type="text" name="nom" class="form-control form-control-lg text-center fw-bold mb-3" value="<?= htmlspecialchars($trainee['nom']) ?>" required>
                                    <select name="status" class="form-select w-75 mx-auto">
                                        <option value="En cours" <?= $trainee['status'] == 'En cours' ? 'selected' : '' ?>>En cours</option>
                                        <option value="terminé" <?= $trainee['status'] == 'terminé' ? 'selected' : '' ?>>terminé</option>
                                    </select>
                                <?php else: ?>
                                    <h2 class="mb-1"><?= htmlspecialchars($trainee['prenom']) ?></h2>
                                    <h2 class="mb-3"><?= htmlspecialchars($trainee['nom']) ?></h2>
                                    <span class="badge status-badge">
                                        <?= htmlspecialchars($trainee['status']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Right Column - Details -->
                        <div class="col-lg-8">
                            <!-- Personal Information -->
                            <div class="card mb-4">
                                <div class="card-header d-flex align-items-center">
                                    <i class="bi bi-person-vcard me-2"></i>
                                    <span>Informations personnelles</span>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">CIN</label>
                                            <?php if ($is_editing): ?>
                                                <input type="text" name="cin" class="form-control" value="<?= htmlspecialchars($trainee['cin']) ?>" required>
                                            <?php else: ?>
                                                <p class="form-control-plaintext"><?= htmlspecialchars($trainee['cin']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email</label>
                                            <?php if ($is_editing): ?>
                                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($trainee['email']) ?>" required>
                                            <?php else: ?>
                                                <p class="form-control-plaintext"><?= htmlspecialchars($trainee['email']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Téléphone</label>
                                            <?php if ($is_editing): ?>
                                                <input type="tel" name="telephone" class="form-control" value="<?= htmlspecialchars($trainee['telephone']) ?>">
                                            <?php else: ?>
                                                <p class="form-control-plaintext"><?= htmlspecialchars($trainee['telephone']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Sexe</label>
                                            <?php if ($is_editing): ?>
                                                <select name="sexe" class="form-select">
                                                    <option value="homme" <?= $trainee['sexe'] == 'homme' ? 'selected' : '' ?>>Homme</option>
                                                    <option value="femme" <?= $trainee['sexe'] == 'femme' ? 'selected' : '' ?>>Femme</option>
                                                </select>
                                            <?php else: ?>
                                                <p class="form-control-plaintext"><?= htmlspecialchars($trainee['sexe']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Academic Information -->
                            <div class="card mb-4">
                                <div class="card-header d-flex align-items-center">
                                    <i class="bi bi-book me-2"></i>
                                    <span>Informations académiques</span>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Diplôme</label>
                                            <?php if ($is_editing): ?>
                                                <input type="text" name="diplome" class="form-control" value="<?= htmlspecialchars($trainee['diplome']) ?>">
                                            <?php else: ?>
                                                <p class="form-control-plaintext"><?= htmlspecialchars($trainee['diplome']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Établissement</label>
                                            <?php if ($is_editing): ?>
                                                <input type="text" name="etablissement" class="form-control" value="<?= htmlspecialchars($trainee['etablissement']) ?>">
                                            <?php else: ?>
                                                <p class="form-control-plaintext"><?= htmlspecialchars($trainee['etablissement']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Supervisor and Internship -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-header d-flex align-items-center">
                                            <i class="bi bi-person-badge me-2"></i>
                                            <span>Encadrant</span>
                                        </div>
                                        <div class="card-body">
                                            <?php if ($is_editing): ?>
                                                <select name="encadrant_id" class="form-select mb-3">
                                                    <?php foreach ($all_supervisors as $e): ?>
                                                        <option value="<?= $e['id'] ?>" <?= $trainee['encadrant_id'] == $e['id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($e['full_name']) ?> (<?= htmlspecialchars($e['poste']) ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php else: ?>
                                                <h5 class="card-title"><?= htmlspecialchars($supervisor['prenom']) ?> <?= htmlspecialchars($supervisor['nom']) ?></h5>
                                                <p class="card-text text-muted mb-1"><?= htmlspecialchars($supervisor['poste']) ?></p>
                                                <p class="card-text mb-1"><i class="bi bi-envelope me-2"></i> <?= htmlspecialchars($supervisor['email']) ?></p>
                                                <p class="card-text"><i class="bi bi-telephone me-2"></i> <?= htmlspecialchars($supervisor['telephone']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-header d-flex align-items-center">
                                            <i class="bi bi-briefcase me-2"></i>
                                            <span>Stage</span>
                                        </div>
                                        <div class="card-body">
                                            <?php if ($is_editing): ?>
                                                <select name="stage_id" class="form-select mb-3">
                                                    <?php foreach ($all_stages as $s): ?>
                                                        <option value="<?= $s['id'] ?>" <?= $trainee['stage_id'] == $s['id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($s['sujet']) ?> (<?= date('d/m/Y', strtotime($s['date_debut'])) ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php else: ?>
                                                <h5 class="card-title"><?= htmlspecialchars($stage['sujet']) ?></h5>
                                                <p class="card-text text-muted mb-1">
                                                    <?= date('d/m/Y', strtotime($stage['date_debut'])) ?> - <?= date('d/m/Y', strtotime($stage['date_fin'])) ?>
                                                </p>
                                            <?php endif; ?><br>
                                            <div class="col-md-6 mb-3">
                                            <label class="form-label">Type de Stage</label>
                                            <?php if ($is_editing): ?>
                                                <select name="type_id" class="form-select">
                                                    <?php foreach ($stage_types as $key => $type): ?>
                                                        <option value="<?= $key ?>" <?= $trainee['type_id'] == $key ? 'selected' : '' ?>>
                                                            <?= $type ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php else: ?>
                                                <p class="form-control-plaintext"><?= $stage_types[$trainee['type_id']] ?? 'Inconnu' ?></p>
                                            <?php endif; ?>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Buttons -->
                    <div class="d-flex justify-content-end mt-4">
                        <?php if ($is_editing): ?>
                            <a href="dashboard_stagiaire.php?id=<?= $trainee_id ?>" class="btn btn-outline-secondary me-2">
                                <i class="bi bi-x-circle me-2"></i>Annuler
                            </a>
                            <button type="submit" name="update_stagiaire" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Enregistrer
                            </button>
                        <?php else: ?>
                            <a href="dashboard_stagiaire.php?id=<?= $trainee_id ?>&edit=true" class="btn btn-primary me-2">
                                <i class="bi bi-pencil me-2"></i>Modifier
                            </a>
                            <?php if($trainee['status']=='terminé'): ?>
                            <button class="btn btn-success" type="button" onclick="window.location.href='print.php?id=<?=$trainee['id']?>'">
                                <i class="bi bi-printer me-2"></i>Attestation
                            </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="print_attestation/printf.js"></script>
    <script>
        // Hide toast after 5 seconds
        setTimeout(() => {
            const toast = document.querySelector('.toast');
            if (toast) {
                toast.classList.add('hide');
                setTimeout(() => toast.remove(), 500);
            }
        }, 5000);
        
        // Image upload preview
        document.getElementById('imageUpload')?.addEventListener('change', function(e) {
            const [file] = e.target.files;
            if (file) {
                const img = document.querySelector('.profile-img');
                img.src = URL.createObjectURL(file);
            }
        });
    </script>
</body>
</html>