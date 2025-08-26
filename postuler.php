<?php
session_start();
include("config.php");

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data directly
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $cin = $_POST['cin'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $diplome = $_POST['diplome'];
    $etablissement = $_POST['etablissement'];
    $mot_de_passe = $_POST['mot_de_passe'];
    $sexe = $_POST['sexe'];
    $type_stage_text = $_POST['type_stage'];
    $debut_temp = $_POST['debut_temp'];
    $fin_temp = $_POST['fin_temp'];

    // Map text stage types to numeric IDs
    $type_stage_mapping = [
        "Stage d'initiation" => 2,
        "Stage de perfectionnement" => 3,
        "Stage PFE" => 1,
        "Stage d'été" => 4,
        "Stage professionnel" => 5
    ];
    
    $type_id = $type_stage_mapping[$type_stage_text] ?? 1;

    // Handle file uploads
    $cv_path = '';
    $lettre_path = '';
    $carte_nationale_path = '';
    $image_path = '';

    // Create upload directories if they don't exist
    $upload_dirs = ['uploads/cv/', 'uploads/lettres/', 'uploads/cartes/', 'uploads/images/'];
    foreach ($upload_dirs as $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    // Upload CV
    if (isset($_FILES['cv'])) {
        $target_dir = "uploads/cv/";
        $file_ext = pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION);
        $new_filename = "cv_" . $cin . "_" . time() . "." . $file_ext;
        $target_file = $target_dir . $new_filename;
        move_uploaded_file($_FILES['cv']['tmp_name'], $target_file);
        $cv_path = $target_file;
    }

    // Upload Lettre de motivation
    if (isset($_FILES['lettre_motivation'])) {
        $target_dir = "uploads/lettres/";
        $file_ext = pathinfo($_FILES['lettre_motivation']['name'], PATHINFO_EXTENSION);
        $new_filename = "lettre_" . $cin . "_" . time() . "." . $file_ext;
        $target_file = $target_dir . $new_filename;
        move_uploaded_file($_FILES['lettre_motivation']['tmp_name'], $target_file);
        $lettre_path = $target_file;
    }

    // Upload Carte Nationale
    if (isset($_FILES['carte_nationale'])) {
        $target_dir = "uploads/cartes/";
        $file_ext = pathinfo($_FILES['carte_nationale']['name'], PATHINFO_EXTENSION);
        $new_filename = "carte_" . $cin . "_" . time() . "." . $file_ext;
        $target_file = $target_dir . $new_filename;
        move_uploaded_file($_FILES['carte_nationale']['tmp_name'], $target_file);
        $carte_nationale_path = $target_file;
    }

    // Upload Image
    if (isset($_FILES['image'])) {
        $target_dir = "uploads/images/";
        $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_filename = "img_" . $cin . "_" . time() . "." . $file_ext;
        $target_file = $target_dir . $new_filename;
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
        $image_path = $target_file;
    }

    // Insert into database
    $sql = "INSERT INTO postule (
            nom, prenom, cin, email, telephone, diplome, etablissement, 
            mot_de_pass, sexe, type_id, date_debut, date_fin,
            cv_url, lettre_url, cart_url, img_url, created_at
        ) VALUES (
            '$nom', '$prenom', '$cin', '$email', '$telephone', '$diplome', '$etablissement',
            '$mot_de_passe', '$sexe', '$type_id', '$debut_temp', '$fin_temp',
            '$cv_path', '$lettre_path', '$carte_nationale_path', '$image_path', NOW()
        )";

    if (mysqli_query($conDb, $sql)) {
        $postule_id = mysqli_insert_id($conDb);
        $full_name = $nom.' '.$prenom;
        $event = "INSERT INTO activitie (
                type_name, action, name
            ) VALUES (
                'demande_stage', 'ajouté', '$full_name'
            )";
        mysqli_query($conDb, $event);
        $sql_ty='SELECT id FROM type_user WHERE nom="candidat" ';
        $resultat_type=mysqli_query($conDb, $sql_ty);
        $resultat_type=$resultat_type->fetch_assoc();
    
        $_SESSION['success'] = "Votre demande a été soumise avec succès! Nous vous contacterons bientôt.";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        $error = "Erreur lors de la soumission: " . mysqli_error($conDb);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postuler pour un Stage - Plateforme Stagiaires</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #dc3545;
            --primary-hover: #bb2d3b;
            --secondary-color: #f8f9fa;
            --dark-color: #343a40;
            --light-color: #ffffff;
            --shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }
        
        body {
            background-color: var(--secondary-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .profile-section {
            text-align: center;
            padding: 2rem;
            background: var(--light-color);
            border-radius: 0.5rem;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
            border: 1px solid rgba(220, 53, 69, 0.2);
        }
        
        .profile-img {
            width: 180px;
            height: 180px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid var(--primary-color);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }
        
        .img-upload-btn {
            position: relative;
            display: inline-block;
            margin-top: 1rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
        }
        
        .form-section {
            background: var(--light-color);
            border-radius: 0.5rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
            border: 1px solid rgba(220, 53, 69, 0.2);
        }
        
        .form-section h5 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #e3e6f0;
            font-weight: 600;
        }
        
        .btn-danger {
            background: var(--primary-color);
            border: none;
            border-radius: 0.35rem;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
        }
        
        .password-toggle {
            font-size: 1.2em;
            position: absolute;
            right: 15px;
            top: 42px;
            cursor: pointer;
            color: var(--dark-color);
        }
        
        .password-input-container {
            position: relative;
        }
        
        .file-upload-container {
            margin-bottom: 15px;
        }
        
        .file-upload-btn {
            position: relative;
            display: inline-block;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 15px;
            font-weight: 500;
        }
        
        .file-name {
            margin-left: 10px;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .required-field::after {
            content: " *";
            color: var(--primary-color);
        }
        
        .application-container {
            max-width: 1000px;
            margin: 30px auto;
            background: var(--light-color);
            border-radius: 10px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(220, 53, 69, 0.1);
        }
        
        .application-header {
            background: linear-gradient(135deg, var(--primary-color), #c82333);
            color: white;
            padding: 25px;
            text-align: center;
        }
        
        .application-body {
            padding: 30px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
                <img src="assets/logo-removebg.png" width="50" height="50" class="me-2">
                Wilaya Stages
            </a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="bi bi-house-door me-1"></i> Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="login.php"><i class="bi bi-box-arrow-in-right me-1"></i> Connexion</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="application-container">
        <div class="application-header">
            <h2><i class="bi bi-briefcase me-2"></i>Demande de Stage</h2>
            <p class="mb-0">Remplissez ce formulaire pour postuler à un stage</p>
        </div>
        
        <div class="application-body">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i><?= $_SESSION['success'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <!-- Section Photo de Profil -->
                <div class="profile-section">
                    <img src="images/default_profile.jpg" class="profile-img mb-3" id="profileImage">
                    <button type="button" class="btn img-upload-btn">
                        <i class="bi bi-camera-fill me-2"></i>Changer photo
                        <input type="file" name="image" id="imageUpload" accept="image/*">
                    </button>
                    <div class="mt-2 text-muted small">Formats acceptés: JPG, PNG (max 2MB)</div>
                </div>
                
                <!-- Informations Personnelles -->
                <div class="form-section">
                    <h5><i class="bi bi-person-lines-fill me-2"></i>Informations Personnelles</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nom" class="form-label required-field">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="prenom" class="form-label required-field">Prénom</label>
                            <input type="text" class="form-control" id="prenom" name="prenom" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cin" class="form-label required-field">CIN</label>
                            <input type="text" class="form-control" id="cin" name="cin" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="sexe" class="form-label required-field">Sexe</label>
                            <select class="form-select" id="sexe" name="sexe" required>
                                <option value="">Sélectionner</option>
                                <option value="Homme">Homme</option>
                                <option value="Femme">Femme</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label required-field">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="telephone" class="form-label required-field">Téléphone</label>
                            <input type="tel" class="form-control" id="telephone" name="telephone" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3 password-input-container">
                            <label for="mot_de_passe" class="form-label required-field">Mot de passe</label>
                            <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                            <i class="bi bi-eye-slash password-toggle" id="togglePassword"></i>
                        </div>
                    </div>
                </div>
                
                <!-- Informations Scolaires -->
                <div class="form-section">
                    <h5><i class="bi bi-book me-2"></i>Informations Scolaires</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="etablissement" class="form-label required-field">Établissement</label>
                            <input type="text" class="form-control" id="etablissement" name="etablissement" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="diplome" class="form-label required-field">Diplôme</label>
                            <input type="text" class="form-control" id="diplome" name="diplome" required>
                        </div>
                    </div>
                </div>
                
                <!-- Informations sur le Stage -->
                <div class="form-section">
                    <h5><i class="bi bi-briefcase me-2"></i>Informations sur le Stage</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type_stage" class="form-label required-field">Type de Stage</label>
                            <select class="form-select" id="type_stage" name="type_stage" required>
                                <option value="">Sélectionner</option>
                                <option value="Stage d'initiation">Stage d'initiation</option>
                                <option value="Stage de perfectionnement">Stage de perfectionnement</option>
                                <option value="Stage PFE">Stage PFE</option>
                                <option value="Stage d'été">Stage d'été</option>
                                <option value="Stage professionnel">Stage professionnel</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="debut_temp" class="form-label required-field">Date de début</label>
                            <input type="date" class="form-control" id="debut_temp" name="debut_temp" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fin_temp" class="form-label required-field">Date de fin</label>
                            <input type="date" class="form-control" id="fin_temp" name="fin_temp" required>
                        </div>
                    </div>
                </div>
                
                <!-- Documents -->
                <div class="form-section">
                    <h5><i class="bi bi-files me-2"></i>Documents à Fournir</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cv" class="form-label required-field">Curriculum Vitae (CV)</label>
                            <div class="file-upload-container">
                                <button type="button" class="btn file-upload-btn">
                                    <i class="bi bi-upload me-2"></i>Choisir fichier
                                    <input type="file" name="cv" id="cv" accept=".pdf" required>
                                </button>
                                <span class="file-name" id="cvFileName">Aucun fichier sélectionné</span>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lettre_motivation" class="form-label required-field">Lettre de Motivation</label>
                            <div class="file-upload-container">
                                <button type="button" class="btn file-upload-btn">
                                    <i class="bi bi-upload me-2"></i>Choisir fichier
                                    <input type="file" name="lettre_motivation" id="lettre_motivation" accept=".pdf" required>
                                </button>
                                <span class="file-name" id="lettreFileName">Aucun fichier sélectionné</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="carte_nationale" class="form-label required-field">Carte Nationale</label>
                        <div class="file-upload-container">
                            <button type="button" class="btn file-upload-btn">
                                <i class="bi bi-upload me-2"></i>Choisir fichier
                                <input type="file" name="carte_nationale" id="carte_nationale" accept=".pdf,.jpg,.jpeg,.png" required>
                            </button>
                            <span class="file-name" id="carteFileName">Aucun fichier sélectionné</span>
                        </div>
                    </div>
                </div>
                
                <div class="form-section bg-light">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="consentement" required>
                        <label class="form-check-label" for="consentement">
                            Je déclare que les informations fournies dans ce formulaire sont exactes et complètes.
                        </label>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-4">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-send me-2"></i>Soumettre la Demande
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5>Wilaya Stages</h5>
                    <p>Plateforme de gestion des stages pour les établissements publics de la wilaya.</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Contact</h5>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-envelope me-2"></i> contact@wilayastages.dz</li>
                        <li><i class="bi bi-telephone me-2"></i> +213 XX XX XX XX</li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p class="mb-0">&copy; <?= date('Y') ?> Wilaya Stages. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image upload preview
        document.getElementById('imageUpload').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('profileImage').src = event.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

        // Show selected file names
        document.getElementById('cv').addEventListener('change', function(e) {
            document.getElementById('cvFileName').textContent = e.target.files[0]?.name || 'Aucun fichier sélectionné';
        });
        
        document.getElementById('lettre_motivation').addEventListener('change', function(e) {
            document.getElementById('lettreFileName').textContent = e.target.files[0]?.name || 'Aucun fichier sélectionné';
        });
        
        document.getElementById('carte_nationale').addEventListener('change', function(e) {
            document.getElementById('carteFileName').textContent = e.target.files[0]?.name || 'Aucun fichier sélectionné';
        });

        // Password toggle
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#mot_de_passe');

        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });

        // Set default dates for stage period
        document.addEventListener('DOMContentLoaded', function() {
            const now = new Date();
            const inTwoMonths = new Date();
            inTwoMonths.setMonth(now.getMonth() + 2);
            
            // Format dates as YYYY-MM-DD
            const formatDate = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };
            
            document.getElementById('debut_temp').value = formatDate(now);
            document.getElementById('fin_temp').value = formatDate(inTwoMonths);
        });
    </script>
</body>
</html>