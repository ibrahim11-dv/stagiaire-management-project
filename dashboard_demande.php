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

// Get candidate ID from URL
$candidate_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_editing = isset($_GET['edit']) && $_GET['edit'] == 'true';
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_candidate'])) {
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
    ];
    
    // Handle date fields
    if (!empty($_POST['date_debut'])) {
        $update_data['date_debut'] = mysqli_real_escape_string($conDb, $_POST['date_debut']);
    }
    if (!empty($_POST['date_fin'])) {
        $update_data['date_fin'] = mysqli_real_escape_string($conDb, $_POST['date_fin']);
    }
    
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
        elseif ($_FILES['image']['size'] > 2000000) {
            $message = 'danger:Désolé, votre fichier est trop volumineux (max 2MB).';
        }
        // Allow certain file formats
        elseif(!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $message = 'danger:Seuls les fichiers JPG, JPEG, PNG & GIF sont autorisés.';
        }
        // Try to upload file
        elseif (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $update_data['img_url'] = $target_file;
        } else {
            $message = 'danger:Une erreur s\'est produite lors du téléchargement de l\'image.';
        }
    } elseif (!empty($_POST['current_image'])) {
        $update_data['img_url'] = $_POST['current_image'];
    }
    
    // Handle document uploads (supprimé diplome_url car pas dans la base)
    $document_fields = ['cv_url', 'lettre_url', 'cart_url'];
    foreach ($document_fields as $field) {
        if (!empty($_FILES[$field]['name'])) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES[$field]['name']);
            
            if (move_uploaded_file($_FILES[$field]['tmp_name'], $target_file)) {
                $update_data[$field] = $target_file;
            }
        } elseif (!empty($_POST['current_' . $field])) {
            $update_data[$field] = $_POST['current_' . $field];
        }
    }
    
    // Only proceed with update if no errors occurred
    if (empty($message)) {
        // Build the update query
        $update_fields = [];
        foreach ($update_data as $field => $value) {
            $update_fields[] = "$field = '" . $value . "'";
        }
        
        $update_query = "UPDATE postule SET " . implode(', ', $update_fields) . " WHERE id = $candidate_id";
        
        if (mysqli_query($conDb, $update_query)) {
            $_SESSION['success_message'] = 'Modifications enregistrées avec succès!';
            // Refresh the page to show updated data
            header("Location: dashboard_demande.php?id=$candidate_id");
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

// Get candidate information
$candidate_query = "SELECT * FROM postule WHERE id = $candidate_id";
$candidate_result = mysqli_query($conDb, $candidate_query);
$candidate = $candidate_result->fetch_assoc();

if (!$candidate) {
    header("Location: postules.php");
    exit();
}

// Set default profile image
if (empty($candidate['img_url'])) {
    $candidate['img_url'] = ($candidate['sexe'] == 'femme') ? "images/femme_default.jpg" : "images/homme_default.jpg";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $is_editing ? 'Modifier Candidat' : 'Détails Candidat' ?> - Plateforme Stages</title>
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
        .document-link {
            display: block;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 5px;
            background-color: #f8f9fa;
            transition: all 0.3s;
        }
        .document-link:hover {
            background-color: #e9ecef;
            text-decoration: none;
        }
        .document-upload {
            margin-bottom: 15px;
        }
        .document-upload label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .current-file {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">

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
                    <h2 class="mb-0"><?= $is_editing ? 'Modifier Candidat' : 'Détails du Candidat' ?></h2>
                    <div class="d-flex align-items-center">
                        <span class="me-3"></span>
                        <img src="" class="rounded-circle">
                    </div>
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

                <form method="POST" action="dashboard_demande.php?id=<?= $candidate_id ?>" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Left Column - Profile Image and Basic Info -->
                        <div class="col-lg-4">
                            <div class="profile-section text-center mb-4">
                                <div class="position-relative d-inline-block">
                                    <img src="<?= htmlspecialchars($candidate['img_url']) ?>" class="profile-img mb-3" alt="Photo de profil">
                                    <?php if ($is_editing): ?>
                                        <input type="file" name="image" class="d-none" id="imageUpload" accept="image/*">
                                        <label for="imageUpload" class="btn btn-sm btn-light position-absolute bottom-0 end-0 rounded-circle">
                                            <i class="bi bi-camera"></i>
                                        </label>
                                        <input type="hidden" name="current_image" value="<?= htmlspecialchars($candidate['img_url']) ?>">
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($is_editing): ?>
                                    <input type="text" name="prenom" class="form-control form-control-lg text-center fw-bold mb-2" value="<?= htmlspecialchars($candidate['prenom']) ?>" required>
                                    <input type="text" name="nom" class="form-control form-control-lg text-center fw-bold mb-3" value="<?= htmlspecialchars($candidate['nom']) ?>" required>
                                <?php else: ?>
                                    <h2 class="mb-1"><?= htmlspecialchars($candidate['prenom']) ?></h2>
                                    <h2 class="mb-3"><?= htmlspecialchars($candidate['nom']) ?></h2>
                                <?php endif; ?>
                            </div>

                            <!-- Documents Section -->
                            <div class="card mb-4">
                                <div class="card-header d-flex align-items-center">
                                    <i class="bi bi-files me-2"></i>
                                    <span>Documents</span>
                                </div>
                                <div class="card-body">
                                    <?php if ($is_editing): ?>
                                        <!-- CV Upload -->
                                        <div class="document-upload">
                                            <label for="cvUpload">CV:</label>
                                            <input type="file" name="cv_url" class="form-control" id="cvUpload">
                                            <?php if (!empty($candidate['cv_url'])): ?>
                                                <div class="current-file">
                                                    Fichier actuel: <a href="<?= htmlspecialchars($candidate['cv_url']) ?>" target="_blank">Voir</a>
                                                </div>
                                                <input type="hidden" name="current_cv_url" value="<?= htmlspecialchars($candidate['cv_url']) ?>">
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Lettre Upload -->
                                        <div class="document-upload">
                                            <label for="lettreUpload">Lettre de motivation:</label>
                                            <input type="file" name="lettre_url" class="form-control" id="lettreUpload">
                                            <?php if (!empty($candidate['lettre_url'])): ?>
                                                <div class="current-file">
                                                    Fichier actuel: <a href="<?= htmlspecialchars($candidate['lettre_url']) ?>" target="_blank">Voir</a>
                                                </div>
                                                <input type="hidden" name="current_lettre_url" value="<?= htmlspecialchars($candidate['lettre_url']) ?>">
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Carte Etudiant Upload -->
                                        <div class="document-upload">
                                            <label for="cartUpload">Carte d'étudiant:</label>
                                            <input type="file" name="cart_url" class="form-control" id="cartUpload">
                                            <?php if (!empty($candidate['cart_url'])): ?>
                                                <div class="current-file">
                                                    Fichier actuel: <a href="<?= htmlspecialchars($candidate['cart_url']) ?>" target="_blank">Voir</a>
                                                </div>
                                                <input type="hidden" name="current_cart_url" value="<?= htmlspecialchars($candidate['cart_url']) ?>">
                                            <?php endif; ?>
                                        </div>
                                        
                                    <?php else: ?>
                                        <?php if (!empty($candidate['cv_url'])): ?>
                                            <a href="<?= htmlspecialchars($candidate['cv_url']) ?>" target="_blank" class="document-link">
                                                <i class="bi bi-file-earmark-person me-2"></i> CV
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($candidate['lettre_url'])): ?>
                                            <a href="<?= htmlspecialchars($candidate['lettre_url']) ?>" target="_blank" class="document-link">
                                                <i class="bi bi-file-earmark-text me-2"></i> Lettre de motivation
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($candidate['cart_url'])): ?>
                                            <a href="<?= htmlspecialchars($candidate['cart_url']) ?>" target="_blank" class="document-link">
                                                <i class="bi bi-file-earmark-medical me-2"></i> Carte d'étudiant
                                            </a>
                                        <?php endif; ?>
                                        
                                    <?php endif; ?>
                                </div>
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
                                                <input type="text" name="cin" class="form-control" value="<?= htmlspecialchars($candidate['cin']) ?>" required>
                                            <?php else: ?>
                                                <p class="form-control-plaintext"><?= htmlspecialchars($candidate['cin']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email</label>
                                            <?php if ($is_editing): ?>
                                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($candidate['email']) ?>" required>
                                            <?php else: ?>
                                                <p class="form-control-plaintext"><?= htmlspecialchars($candidate['email']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Téléphone</label>
                                            <?php if ($is_editing): ?>
                                                <input type="tel" name="telephone" class="form-control" value="<?= htmlspecialchars($candidate['telephone']) ?>">
                                            <?php else: ?>
                                                <p class="form-control-plaintext"><?= htmlspecialchars($candidate['telephone']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Sexe</label>
                                            <?php if ($is_editing): ?>
                                                <select name="sexe" class="form-select">
                                                    <option value="homme" <?= $candidate['sexe'] == 'homme' ? 'selected' : '' ?>>Homme</option>
                                                    <option value="femme" <?= $candidate['sexe'] == 'femme' ? 'selected' : '' ?>>Femme</option>
                                                </select>
                                            <?php else: ?>
                                                <p class="form-control-plaintext"><?= $candidate['sexe'] == 'homme' ? 'Homme' : 'Femme' ?></p>
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
                                                <input type="text" name="diplome" class="form-control" value="<?= htmlspecialchars($candidate['diplome']) ?>">
                                            <?php else: ?>
                                                <p class="form-control-plaintext"><?= htmlspecialchars($candidate['diplome']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Établissement</label>
                                            <?php if ($is_editing): ?>
                                                <input type="text" name="etablissement" class="form-control" value="<?= htmlspecialchars($candidate['etablissement']) ?>">
                                            <?php else: ?>
                                                <p class="form-control-plaintext"><?= htmlspecialchars($candidate['etablissement']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Internship Period -->
                            <div class="card mb-4">
                                <div class="card-header d-flex align-items-center">
                                    <i class="bi bi-calendar-range me-2"></i>
                                    <span>Période de stage souhaitée</span>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Date de début</label>
                                            <?php if ($is_editing): ?>
                                                <input type="date" name="date_debut" class="form-control" value="<?= htmlspecialchars($candidate['date_debut']) ?>">
                                            <?php else: ?>
                                                <p class="form-control-plaintext"><?= !empty($candidate['date_debut']) ? date('d/m/Y', strtotime($candidate['date_debut'])) : 'Non spécifiée' ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Date de fin</label>
                                            <?php if ($is_editing): ?>
                                                <input type="date" name="date_fin" class="form-control" value="<?= htmlspecialchars($candidate['date_fin']) ?>">
                                            <?php else: ?>
                                                <p class="form-control-plaintext"><?= !empty($candidate['date_fin']) ? date('d/m/Y', strtotime($candidate['date_fin'])) : 'Non spécifiée' ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Buttons -->
                    <div class="d-flex justify-content-end mt-4">
                        <?php if ($is_editing): ?>
                            <a href="dashboard_demande.php?id=<?= $candidate_id ?>" class="btn btn-outline-secondary me-2">
                                <i class="bi bi-x-circle me-2"></i>Annuler
                            </a>
                            <button type="submit" name="update_candidate" class="btn btn-primary">
                                <i class="bi bi-save me-2"></i>Enregistrer
                            </button>
                        <?php else: ?>
                            <a href="dashboard_demande.php?id=<?= $candidate_id ?>&edit=true" class="btn btn-primary me-2">
                                <i class="bi bi-pencil me-2"></i>Modifier
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal d'envoi d'email -->
    <div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="emailModalLabel">Envoyer un message</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="demandes_send_email.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="emailSubject" class="form-label">Sujet :</label>
                            <input type="text" class="form-control" id="emailSubject" name="subject" required>
                        </div>
                        <div class="mb-3">
                            <label for="emailMessage" class="form-label">Message :</label>
                            <textarea class="form-control" id="emailMessage" name="message" rows="5" required></textarea>
                        </div>
                        <input type="hidden" name="candidate_id" value="<?= $candidate_id ?>">
                        <input type="hidden" name="email" value="<?= htmlspecialchars($candidate['email']) ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Envoyer</button>
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