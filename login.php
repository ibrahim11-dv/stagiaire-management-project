<?php
session_start();
include("config.php");
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['user_name'];
    $email = $_POST['user_name'];
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM utilisateurs WHERE (identifiant = '$username') or (email='$email')";
    $result = mysqli_query($conDb, $sql);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        if ($password == $user['mot_de_passe']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['identifiant'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['telephone'] = $user['telephone'];
            $_SESSION['type_user'] = $user['type'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['password'] = $user['mot_de_passe'];
            $_SESSION['avatar']=$user['photo'];
            $id=$user['postule_id'];
            header("Location: dashboard.php");
            if($user['type']==1)header("Location: dashboard.php");
            elseif($user['type']==3) header("Location: dashboard_demande.php?id=$id");
            elseif($user['type']==2) header("Location: dashboard_stagiaire.php?id=$id");
        }elseif($password != $user['mot_de_passe']){
            header("Location: login.php?error=Mot de passe incorrect");
            exit();
        }
        } else {
            header("Location: login.php?error=Nom d'utilisateur ou email incorrect");
            exit();
        }
    }
mysqli_close($conDb);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Plateforme Stagiaires Wilaya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/login.css">
    <style>
        .login-section {
            background: url('assets/wilayaOujda.jpg') no-repeat center center/cover;
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
        }
        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
        }
        .login-card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        .login-header {
            background-color: #dc3545;
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
        .login-logo {
            width: 80px;
            height: 80px;
            margin-bottom: 1rem;
        }
        .form-control:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
        }
        .btn-login {
            background-color: #dc3545;
            border: none;
            padding: 0.5rem 2rem;
            font-weight: 500;
        }
        .btn-login:hover {
            background-color: #bb2d3b;
        }
        .login-footer {
            text-align: center;
            padding-top: 1rem;
            border-top: 1px solid #eee;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
                <img src="assets/logo-removebg.png" width="50" height="50" class="me-2">
                Wilaya Stages
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
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

    <!-- Login Section -->
    <section class="login-section">
        <div class="login-overlay"></div>
        <div class="login-container container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="login-card">
                        <div class="login-header">
                            <img src="assets/logo-removebg.png" alt="Logo Wilaya" class="login-logo">
                            <h2>Connexion à votre espace</h2>
                        </div>
                        <div class="card-body p-5">
                                <?php if (isset($_GET['error'])): ?>
                                    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        <?php echo htmlspecialchars($_GET['error']); ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>
                            <form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
                                <div class="mb-4">
                                    <label for="username" class="form-label fw-bold">Nom d'utilisateur</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                                        <input type="text" class="form-control" id="username" placeholder="Entrez votre nom d'utilisateur ou email" required name="user_name">
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label for="password" class="form-label fw-bold">Mot de passe</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                        <input type="password" class="form-control" id="password" placeholder="Entrez votre mot de passe" required name="password">
                                    </div>
                                </div>
                                <div class="mb-4 form-check">
                                    <input type="checkbox" class="form-check-input" id="remember">
                                    <label class="form-check-label" for="remember">Se souvenir de moi</label>
                                    <a href="forgot_password.php" class="float-end text-danger">Mot de passe oublié ?</a>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-login btn-lg text-white">
                                        <i class="bi bi-box-arrow-in-right me-2"></i> Se connecter
                                    </button>
                                </div>
                            </form>
                            <div class="login-footer">
                                <p class="mb-0">Vous n'avez pas de compte ? <a href="#" class="text-danger">Contactez l'administrateur</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="d-flex align-items-center">
                        <img src="assets/logo-removebg.png" width="50" height="50" class="me-2">
                        <span class="fw-bold">Wilaya Stages</span>
                    </div>
                    <p class="small mt-2 mb-0 text-muted">
                        Plateforme officielle de gestion des stagiaires de la Wilaya d'Oujda
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 small">
                        © 2025 Wilaya d'Oujda - Tous droits réservés<br>
                        <a href="#" class="text-white-50 text-decoration-none">Mentions légales</a> | 
                        <a href="#" class="text-white-50 text-decoration-none">Confidentialité</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>