<?php
// add_edit_stagiaire.php (simplified for adding only)

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: logout.php");
    exit();
}

include("config.php");

// Process form submission for new stagiaire
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data directly
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $cin = $_POST['cin'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $password = $_POST['password'];
    $diplome = $_POST['diplome'];
    $etablissement = $_POST['etablissement'];
    $sexe = $_POST['sexe'];
    $stage_id = $_POST['stage_id'];
    $encadrant_id = $_POST['encadrant_id'];
    $type_id = $_POST['type_id'];
    
    // Handle image upload (simplified)
    $image_url = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "images/";
        $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_filename = $_FILES['image']['name'].'.' . $file_ext;
        $target_file = $target_dir . $new_filename;
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
        $image_url = $target_file;
    }

    // Insert into database
    $sql = "INSERT INTO stagiaire (
            nom, prenom, cin, email, telephone, mot_de_passe,
            diplome, etablissement, sexe, stage_id, 
            encadrant_id, type_id, image_url
        ) VALUES (
            '$nom', '$prenom', '$cin', '$email', '$telephone', '$password',
            '$diplome', '$etablissement', '$sexe', $stage_id,
            $encadrant_id, $type_id, '$image_url'
        )";
    $full_name= $nom.' '.$prenom;
    $event="INSERT INTO activitie (
            type_name,action,name
        ) VALUES (
            'stagiaire','ajouté','$full_name'
        )";
    mysqli_query($conDb,$event);
    if ($conDb->query($sql)) {
        $id = $conDb->insert_id;
        $sql_ty = 'SELECT id FROM type_user WHERE nom="stagiaire"';
        $resultat_type = mysqli_query($conDb, $sql_ty);
        $type_row = mysqli_fetch_assoc($resultat_type); // mysqli_fetch_assoc, pas ->fetch_assoc()
        $type_id = $type_row['id'];
        header("Location: stagiaires_details.php?id=" . $id);
        exit();
    } else {
        $message = "Erreur: " . $conDb->error;
        $alert_class = "alert-danger";
    }
}

// Fetch lists for dropdowns
$encadrants = $conDb->query("SELECT id, nom, prenom FROM encadrant ORDER BY nom")->fetch_all(MYSQLI_ASSOC);
$stages = $conDb->query("SELECT id, sujet FROM stage ORDER BY id")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Stagiaire - Plateforme Stagiaires</title>
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
        
        .profile-section {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
            margin-bottom: 2rem;
        }
        
        .profile-img {
            width: 180px;
            height: 180px;
            object-fit: cover;
            border-radius: 50%;
            border: 1px solid #dc3545;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }
        
        .profile-img:hover {
            transform: scale(1.05);
        }
        
        .img-upload-btn {
            position: relative;
            display: inline-block;
            margin-top: 1rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .img-upload-btn:hover {
            border : 1px red solid ;
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }
        
        .img-upload-btn input[type=file] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            cursor: pointer;
        }
        
        .form-section {
            background: white;
            border-radius: 0.35rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }
        
        .form-section h5 {
            color: var(--primary);
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
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        .btn-primary {
            background: var(--primary);
            border: none;
            border-radius: 0.35rem;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }
        
        .password-toggle {
            font-size: 1.7em;
            position: absolute;
            right: 20px;
            top: 56px;
            transform: translateY(-50%);
            cursor: pointer;
            color:rgb(0, 0, 0);
        }
        
        .password-toggle:hover {
            color: gray;
        }
        
        .password-input-container {
            position: relative;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar - Identique à l'original -->
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
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="stagiaires.php">
                            <i class="bi bi-people me-2"></i> Stagiaires
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="encadrants.php">
                            <i class="bi bi-person-badge me-2"></i> Encadrants
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="stages.php">
                            <i class="bi bi-briefcase me-2"></i> Stages
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="demandes.php">
                            <i class="bi bi-person-add me-2"></i> Demandes
                        </a>
                    </li>
                    <li class="nav-item mt-3">
                        <a class="nav-link" href="logout.php">
                            <i class="bi bi-box-arrow-left me-2"></i> Déconnexion
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Gestion des Stagiaires</h2>
                    <div class="d-flex align-items-center">
                        <span class="me-3"></span>
                        <img src="" class="rounded-circle">
                    </div>
                </div>
            <div class="mb-4">
                    <a href="stagiaires.php" class="btn btn-outline-danger">
                        <i class="bi bi-arrow-left me-2"></i>Retour à la liste
                    </a>
            </div>

            <!-- Main Content - Structure modifiée mais fonctionnalités identiques -->
            <div class="col-md-10 main-content">

                <?php if (isset($message)): ?>
                    <div class="alert <?= $alert_class ?> alert-dismissible fade show" role="alert">
                        <?= $message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <!-- Section Photo de Profil Centrée -->
                    <div class="profile-section">
                        <img src="images/default_profile.jpg" class="profile-img mb-3" id="profileImage">
                        <button type="button" class="btn img-upload-btn">
                            <i class="bi bi-camera-fill me-2"></i>Changer photo
                            <input type="file" name="image" id="imageUpload" accept="image/*">
                        </button>
                    </div>
                    
                    <!-- Section Informations Personnelles -->
                    <div class="form-section">
                        <h5><i class="bi bi-person-lines-fill me-2"></i>Informations Personnelles</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label">Nom</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="prenom" class="form-label">Prénom</label>
                                <input type="text" class="form-control" id="prenom" name="prenom" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cin" class="form-label">CIN</label>
                                <input type="text" class="form-control" id="cin" name="cin" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="telephone" name="telephone" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="sexe" class="form-label">Sexe</label>
                                <select class="form-select" id="sexe" name="sexe" required>
                                    <option value="homme">Homme</option>
                                    <option value="femme">Femme</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section Formation -->
                    <div class="form-section">
                        <h5><i class="bi bi-book me-2"></i>Formation</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="diplome" class="form-label">Diplôme</label>
                                <input type="text" class="form-control" id="diplome" name="diplome" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="etablissement" class="form-label">Établissement</label>
                                <input type="text" class="form-control" id="etablissement" name="etablissement" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section Stage -->
                    <div class="form-section">
                        <h5><i class="bi bi-briefcase me-2"></i>Stage</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="encadrant_id" class="form-label">Encadrant</label>
                                <select class="form-select" id="encadrant_id" name="encadrant_id" required>
                                    <option value="">Sélectionner un encadrant</option>
                                    <?php foreach ($encadrants as $enc): ?>
                                        <option value="<?= $enc['id'] ?>">
                                            <?= $enc['nom'] . ' ' . $enc['prenom'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="stage_id" class="form-label">Stage</label>
                                <select class="form-select" id="stage_id" name="stage_id" required>
                                    <option value="">Sélectionner un stage</option>
                                    <?php foreach ($stages as $stage): ?>
                                        <option value="<?= $stage['id'] ?>">
                                            <?= $stage['sujet'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="type_id" class="form-label">Type de stage</label>
                                <select class="form-select" id="type_id" name="type_id" required>
                                    <option value="1">PFE</option>
                                    <option value="2">Initiation</option>
                                    <option value="3">Perfectionnement</option>
                                    <option value="4">Ouvrier</option>
                                    <option value="5">Technicien</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Section Sécurité (Mot de passe en dernier) -->
                    <div class="form-section">
                        <h5><i class="bi bi-shield-lock me-2"></i>Sécurité</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3 password-input-container">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <i class="bi bi-eye-slash password-toggle" id="togglePassword"></i>
                            </div>
                            <div class="col-md-6 mb-3 password-input-container">
                                <label for="confirm_password" class="form-label">Confirmer mot de passe</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                <i class="bi bi-eye-slash password-toggle" id="toggleConfirmPassword"></i>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <a href="stagiaires.php" class="btn btn-secondary me-2 p-2">Annuler</a>
                        <button type="submit" class="btn btn-danger p-2">Ajouter Stagiaire</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image upload preview (identique à l'original)
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

        // Password confirmation validation (identique à l'original)
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirm_password = document.getElementById('confirm_password').value;
            
            if (password !== confirm_password) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas!');
            }
        });

        // Nouvelle fonctionnalité: toggle password visibility
        const togglePassword = document.querySelector('#togglePassword');
        const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
        const password = document.querySelector('#password');
        const confirmPassword = document.querySelector('#confirm_password');

        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });

        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPassword.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
    </script>
</body>
</html>