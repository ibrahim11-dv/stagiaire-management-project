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

// Get supervisor ID from URL
$supervisor_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_editing = isset($_GET['edit']) && $_GET['edit'] == 'true';
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_encadrant'])) {
    // Validate and sanitize input data
    $update_data = [
        'prenom' => mysqli_real_escape_string($conDb, trim($_POST['prenom'])),
        'nom' => mysqli_real_escape_string($conDb, trim($_POST['nom'])),
        'cin' => mysqli_real_escape_string($conDb, trim($_POST['cin'])),
        'email' => mysqli_real_escape_string($conDb, trim($_POST['email'])),
        'telephone' => mysqli_real_escape_string($conDb, trim($_POST['telephone'])),
        'poste' => mysqli_real_escape_string($conDb, trim($_POST['poste'])),
        'status' => mysqli_real_escape_string($conDb, trim($_POST['status'])),
        'service_id' => intval($_POST['service_id'])
    ];
    
    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "images/";
        $target_file = $target_dir . $_FILES['image']['name'];
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if($check === false) {
            $message = 'danger:Le fichier sélectionné n\'est pas une image valide.';
        } 
        // Check file size (max 2MB)
        elseif ($_FILES['image']['size'] > 2000000) {
            $message = 'danger:Désolé, votre fichier est trop volumineux (max 2MB).';
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
        
        $update_query = "UPDATE encadrant SET " . implode(', ', $update_fields) . " WHERE id = $supervisor_id";
        
        if (mysqli_query($conDb, $update_query)) {

        $full_name=$update_data['nom'].' '.$update_date['prenom'];
                $event="INSERT INTO activitie (
                                type_name,action,name
                            ) VALUES (
                                'encadrant','modifié','$full_name'
                            )";
                mysqli_query($conDb,$event);

            $_SESSION['success_message'] = 'Modifications enregistrées avec succès!';
            // Refresh the page to show updated data
            header("Location: encadrants_details.php?id=$supervisor_id");
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

// Get supervisor information
$supervisor_query = "SELECT * FROM encadrant WHERE id = $supervisor_id AND est_supp = false";
$supervisor_result = mysqli_query($conDb, $supervisor_query);
$supervisor = $supervisor_result->fetch_assoc();


if (!$supervisor) {
    header("Location: encadrants.php");
    exit();
}

// Set default profile image
if (empty($supervisor['image_url'])) {
    $supervisor['image_url'] = ($supervisor['sexe'] == 'homme') ? "images/homme_default.jpg" : "images/femme_default.jpg";
}

// Get all services
$services_query = "SELECT id, nom FROM service";
$services_result = mysqli_query($conDb, $services_query);
$services = [];
while($row = $services_result->fetch_assoc()) {
    $services[] = $row;
}

// Get trainees supervised by this supervisor
$trainees_query = "SELECT s.id, s.nom, s.prenom, s.cin, s.email, s.telephone, s.diplome, s.status 
                   FROM stagiaire s 
                   WHERE s.encadrant_id = $supervisor_id AND s.est_supp = false
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
    <title><?= $is_editing ? 'Modifier' : 'Détails' ?> Encadrant - Plateforme Stagiaires</title>
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
                        <a class="nav-link active" href="encadrants.php">
                            <i class="bi bi-person-badge"></i> Encadrants
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="stages.php">
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
                    <h2 class="mb-0"><?= $is_editing ? 'Modifier Stagiaire' : 'Détails du Stagiaire' ?></h2>
                    <div class="d-flex align-items-center">
                        <span class="me-3"></span>
                        <img src="" class="rounded-circle" >
                    </div>
                </div>

                <!-- Back Button -->
                <div class="mb-4">
                    <a href="encadrants.php" class="btn btn-outline-primary">
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

                <form method="POST" action="encadrants_details.php?id=<?= $supervisor_id ?>" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Left Column - Profile Image and Basic Info -->
                        <div class="col-lg-4">
                            <div class="profile-section text-center mb-4">
                                <div class="position-relative d-inline-block">
                                    <img src="<?= htmlspecialchars($supervisor['image_url']) ?>" class="profile-img mb-3" alt="Photo de profil">
                                    <?php if ($is_editing): ?>
                                        <input type="file" name="image" class="d-none" id="imageUpload" accept="image/*">
                                        <label for="imageUpload" class="btn btn-sm btn-light position-absolute bottom-0 end-0 rounded-circle">
                                            <i class="bi bi-camera"></i>
                                        </label>
                                        <input type="hidden" name="current_image" value="<?= htmlspecialchars($supervisor['image_url']) ?>">
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($is_editing): ?>
                                    <input type="text" name="prenom" class="form-control form-control-lg text-center fw-bold mb-2" value="<?= htmlspecialchars($supervisor['prenom']) ?>" required>
                                    <input type="text" name="nom" class="form-control form-control-lg text-center fw-bold mb-3" value="<?= htmlspecialchars($supervisor['nom']) ?>" required>
                                    <select name="status" class="form-select w-75 mx-auto">
                                        <option value="En cours" <?= $supervisor['status'] == 'En cours' ? 'selected' : '' ?>>En cours</option>
                                        <option value="terminé" <?= $supervisor['status'] == 'terminé' ? 'selected' : '' ?>>terminé</option>
                                    </select>
                                <?php else: ?>
                                    <h2 class="mb-1"><?= htmlspecialchars($supervisor['prenom']) ?></h2>
                                    <h2 class="mb-3"><?= htmlspecialchars($supervisor['nom']) ?></h2>
                                    <span class="badge status-badge">
                                        <?= htmlspecialchars($supervisor['status']) ?>
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
                                                <input type="text" name="cin" class="form-control" value="<?= htmlspecialchars($supervisor['cin']) ?>" required>
                                            <?php else: ?>
                                                <p class="form-control-plaintext"><?= htmlspecialchars($supervisor['cin']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email</label>
                                            <?php if ($is_editing): ?>
                                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($supervisor['email']) ?>" required>
                                            <?php else: ?>
                                                <p class="form-control-plaintext"><?= htmlspecialchars($supervisor['email']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Téléphone</label>
                                            <?php if ($is_editing): ?>
                                                <input type="tel" name="telephone" class="form-control" value="<?= htmlspecialchars($supervisor['telephone']) ?>">
                                            <?php else: ?>
                                                <p class="form-control-plaintext"><?= htmlspecialchars($supervisor['telephone']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Poste</label>
                                            <?php if ($is_editing): ?>
                                                <input type="text" name="poste" class="form-control" value="<?= htmlspecialchars($supervisor['poste']) ?>" required>
                                            <?php else: ?>
                                                <p class="form-control-plaintext"><?= htmlspecialchars($supervisor['poste']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Service</label>
                                            <?php if ($is_editing): ?>
                                                <select name="service_id" class="form-select">
                                                    <?php foreach ($services as $service): ?>
                                                        <option value="<?= $service['id'] ?>" <?= $supervisor['service_id'] == $service['id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($service['nom']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            <?php else: ?>
                                                <?php 
                                                    $service_name = 'Non spécifié';
                                                    foreach ($services as $service) {
                                                        if ($service['id'] == $supervisor['service_id']) {
                                                            $service_name = $service['nom'];
                                                            break;
                                                        }
                                                    }
                                                ?>
                                                <p class="form-control-plaintext"><?= htmlspecialchars($service_name) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Stagiaires encadrés -->
                            <div class="card">
                                <div class="card-header d-flex align-items-center">
                                    <i class="bi bi-people me-2"></i>
                                    <span>Stagiaires encadrés</span>
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
                                                        <th>Diplôme</th>
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
                                                            <td><?= htmlspecialchars($trainee['diplome']) ?></td>
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
                                            Cet encadrant n'a actuellement aucun stagiaire sous sa supervision.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Buttons -->
                    <div class="d-flex justify-content-end mt-4">
                        <?php if ($is_editing): ?>
                            <a href="encadrants_details.php?id=<?= $supervisor_id ?>" class="btn btn-outline-secondary me-2">
                                <i class="bi bi-x-circle me-2"></i>Annuler
                            </a>
                            <button type="submit" name="update_encadrant" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Enregistrer
                            </button>
                        <?php else: ?>
                            <button type="button" onclick="exportToExcel()" class="btn btn-success me-2">
                                <i class="bi bi-table me-2"></i>Exporter Excel
                            </button>
                            <a href="encadrants_details.php?id=<?= $supervisor_id ?>&edit=true" class="btn btn-primary">
                                <i class="bi bi-pencil me-2"></i>Modifier
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
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
        
        // Image upload preview
        document.getElementById('imageUpload')?.addEventListener('change', function(e) {
            const [file] = e.target.files;
            if (file) {
                const img = document.querySelector('.profile-img');
                img.src = URL.createObjectURL(file);
            }
        });
function exportToExcel() {
    // Create a new workbook
    const wb = XLSX.utils.book_new();
    
    // Prepare supervisor data (escaped and safe)
    const supervisorData = {
        nom: <?= json_encode($supervisor['nom']) ?>,
        prenom: <?= json_encode($supervisor['prenom']) ?>,
        cin: <?= json_encode($supervisor['cin']) ?>,
        email: <?= json_encode($supervisor['email']) ?>,
        telephone: <?= json_encode($supervisor['telephone']) ?>,
        poste: <?= json_encode($supervisor['poste']) ?>,
        status: <?= json_encode($supervisor['status']) ?>
    };
    
    const serviceName = <?= json_encode($service_name) ?>;
    const traineesData = <?= json_encode($trainees) ?>;
    
    // Prepare the data array with beautiful formatting
    const data = [
        // MAIN TITLE - Row 0
        ["🏛️ WILAYA D'OUJDA-ANGAD - DIRECTION GÉNÉRALE", "", "", "", "", "", ""],
        ["FICHE DÉTAILLÉE DE L'ENCADRANT", "", "", "", "", "", ""],
        ["", "", "", "", "", "", ""],
        
        // ENCADRANT SECTION - Row 3
        ["👤 INFORMATIONS PERSONNELLES", "", "", "", "", "", ""],
        ["═══════════════════════════════════════", "", "", "", "", "", ""],
        ["", "", "", "", "", "", ""],
        
        // Personal Details - Row 6
        ["📋 Nom complet :", `${supervisorData.nom} ${supervisorData.prenom}`, "", "🏷️ Statut :", supervisorData.status, "", ""],
        ["🆔 CIN :", supervisorData.cin, "", "💼 Poste :", supervisorData.poste, "", ""],
        ["📧 Email :", supervisorData.email, "", "🏢 Service :", serviceName, "", ""],
        ["📞 Téléphone :", supervisorData.telephone, "", "📅 Date export :", new Date().toLocaleDateString('fr-FR'), "", ""],
        ["", "", "", "", "", "", ""],
        ["", "", "", "", "", "", ""],
        
        // STATISTICS SECTION - Row 12
        ["📊 STATISTIQUES", "", "", "", "", "", ""],
        ["═══════════════════════════════════════", "", "", "", "", "", ""],
        ["", "", "", "", "", "", ""],
        [`👥 Nombre total de stagiaires encadrés : ${traineesData.length}`, "", "", "", "", "", ""],
        [`✅ Stagiaires actifs : ${traineesData.filter(t => t.status === 'En cours').length}`, "", "", "", "", "", ""],
        [`✅ Stagiaires terminés : ${traineesData.filter(t => t.status === 'terminé').length}`, "", "", "", "", "", ""],
        ["", "", "", "", "", "", ""],
        ["", "", "", "", "", "", ""],
        
        // TRAINEES SECTION - Row 20
        ["👨‍🎓 LISTE DES STAGIAIRES ENCADRÉS", "", "", "", "", "", ""],
        ["═══════════════════════════════════════", "", "", "", "", "", ""],
        ["", "", "", "", "", "", ""],
        
        // Table Headers - Row 23
        ["👤 Nom complet", "🆔 CIN", "📧 Email", "📞 Téléphone", "🎓 Diplôme", "📊 Statut", "📝 Remarques"]
    ];
    
    // Add trainees data with better formatting
    if (traineesData.length > 0) {
        traineesData.forEach((trainee, index) => {
            const statusEmoji = trainee.status === 'en cours' ? '🟢' : '🔴';
            data.push([
                `${index + 1}. ${trainee.nom} ${trainee.prenom}`,
                trainee.cin,
                trainee.email,
                trainee.telephone,
                trainee.diplome,
                `${statusEmoji} ${trainee.status}`,
                trainee.status === 'en cours' ? 'Stage en cours' : 'Stage terminé'
            ]);
        });
    } else {
        data.push([
            "❌ Aucun stagiaire encadré actuellement", "", "", "", "", "", "Aucun stage assigné"
        ]);
    }
    
    // Add beautiful footer
    data.push(
        ["", "", "", "", "", "", ""],
        ["", "", "", "", "", "", ""],
        ["📄 INFORMATIONS D'EXPORT", "", "", "", "", "", ""],
        ["═══════════════════════════════════════", "", "", "", "", "", ""],
        ["🕒 Exporté le :", new Date().toLocaleDateString('fr-FR') + " à " + new Date().toLocaleTimeString('fr-FR'), "", "👤 Par :", <?= json_encode($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?>, "", ""],
        ["🏛️ Organisme :", "Wilaya d'Oujda-Angad", "", "📋 Version :", "1.0", "", ""],
        ["", "", "", "", "", "", ""],
        ["═══════════════════════════════════════", "", "", "", "", "", ""],
        ["✨ Document généré automatiquement par le système de gestion des stages", "", "", "", "", "", ""]
    );

    // Create worksheet
    const ws = XLSX.utils.aoa_to_sheet(data);
    
    // Set column widths for better visibility
    ws['!cols'] = [
        {wch: 30}, // Nom complet
        {wch: 15}, // CIN
        {wch: 25}, // Email
        {wch: 15}, // Téléphone
        {wch: 20}, // Diplôme
        {wch: 15}, // Statut
        {wch: 20}  // Remarques
    ];
    
    // Set merges for better layout
    ws['!merges'] = [
        // Main title
        {s: {r:0, c:0}, e: {r:0, c:6}},
        {s: {r:1, c:0}, e: {r:1, c:6}},
        
        // Sections headers
        {s: {r:3, c:0}, e: {r:3, c:6}}, // Informations personnelles
        {s: {r:4, c:0}, e: {r:4, c:6}}, // Separator
        {s: {r:12, c:0}, e: {r:12, c:6}}, // Statistiques
        {s: {r:13, c:0}, e: {r:13, c:6}}, // Separator
        {s: {r:20, c:0}, e: {r:20, c:6}}, // Stagiaires
        {s: {r:21, c:0}, e: {r:21, c:6}}, // Separator
        
        // Footer
        {s: {r:data.length-6, c:0}, e: {r:data.length-6, c:6}}, // Footer title
        {s: {r:data.length-5, c:0}, e: {r:data.length-5, c:6}}, // Separator
        {s: {r:data.length-2, c:0}, e: {r:data.length-2, c:6}}, // Separator
        {s: {r:data.length-1, c:0}, e: {r:data.length-1, c:6}}  // Final note
    ];
    
    // Define beautiful styles
    const titleStyle = {
        font: { bold: true, sz: 16, color: { rgb: "FFFFFF" } },
        alignment: { horizontal: "center", vertical: "center" },
        fill: { fgColor: { rgb: "DC3545" } },
        border: {
            top: { style: "thick", color: { rgb: "000000" } },
            bottom: { style: "thick", color: { rgb: "000000" } },
            left: { style: "thick", color: { rgb: "000000" } },
            right: { style: "thick", color: { rgb: "000000" } }
        }
    };
    
    const sectionHeaderStyle = {
        font: { bold: true, sz: 14, color: { rgb: "FFFFFF" } },
        alignment: { horizontal: "center", vertical: "center" },
        fill: { fgColor: { rgb: "6C757D" } },
        border: {
            top: { style: "medium", color: { rgb: "000000" } },
            bottom: { style: "medium", color: { rgb: "000000" } },
            left: { style: "medium", color: { rgb: "000000" } },
            right: { style: "medium", color: { rgb: "000000" } }
        }
    };
    
    const tableHeaderStyle = {
        font: { bold: true, sz: 12, color: { rgb: "FFFFFF" } },
        alignment: { horizontal: "center", vertical: "center" },
        fill: { fgColor: { rgb: "495057" } },
        border: {
            top: { style: "medium", color: { rgb: "000000" } },
            bottom: { style: "medium", color: { rgb: "000000" } },
            left: { style: "thin", color: { rgb: "000000" } },
            right: { style: "thin", color: { rgb: "000000" } }
        }
    };
    
    const dataStyle = {
        font: { sz: 10 },
        alignment: { horizontal: "left", vertical: "center" },
        border: {
            top: { style: "thin", color: { rgb: "CCCCCC" } },
            bottom: { style: "thin", color: { rgb: "CCCCCC" } },
            left: { style: "thin", color: { rgb: "CCCCCC" } },
            right: { style: "thin", color: { rgb: "CCCCCC" } }
        }
    };
    
    // Apply styles
    // Title styles
    if (ws['A1']) ws['A1'].s = titleStyle;
    if (ws['A2']) ws['A2'].s = titleStyle;
    
    // Section headers
    if (ws['A4']) ws['A4'].s = sectionHeaderStyle;
    if (ws['A13']) ws['A13'].s = sectionHeaderStyle;
    if (ws['A21']) ws['A21'].s = sectionHeaderStyle;
    
    // Table headers
    for (let col = 0; col < 7; col++) {
        const cellAddress = XLSX.utils.encode_cell({r: 23, c: col});
        if (ws[cellAddress]) ws[cellAddress].s = tableHeaderStyle;
    }
    
    // Data rows styling
    for (let row = 24; row < data.length - 6; row++) {
        for (let col = 0; col < 7; col++) {
            const cellAddress = XLSX.utils.encode_cell({r: row, c: col});
            if (ws[cellAddress]) ws[cellAddress].s = dataStyle;
        }
    }
    
    // Add worksheet to workbook
    XLSX.utils.book_append_sheet(wb, ws, "Fiche Encadrant");
    
    // Generate beautiful file name
    const currentDate = new Date().toISOString().slice(0,10).replace(/-/g,'');
    const fileName = `📋 Fiche_Encadrant_${supervisorData.nom}_${supervisorData.prenom}_${currentDate}.xlsx`;
    
    // Export to Excel
    XLSX.writeFile(wb, fileName);
    
    // Show success message
    const toast = document.createElement('div');
    toast.className = 'toast show align-items-center text-white toast-success';
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-check-circle me-2"></i>
                Fiche Excel exportée avec succès! 📊✨
            </div>
        </div>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
    </script>
</body>
</html>