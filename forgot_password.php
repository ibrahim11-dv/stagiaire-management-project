<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation - Plateforme Stagiaires Wilaya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .reset-section {
            background: url('assets/wilayaOujda.jpg') no-repeat center center/cover;
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .reset-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
        }
        .reset-container {
            position: relative;
            z-index: 1;
            width: 100%;
        }
        .reset-card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        .reset-header {
            background-color: #dc3545;
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
        .reset-logo {
            width: 80px;
            height: 80px;
            margin-bottom: 1rem;
        }
        .form-control:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
        }
        .btn-reset {
            background-color: #dc3545;
            border: none;
            padding: 0.5rem 2rem;
            font-weight: 500;
        }
        .btn-reset:hover {
            background-color: #bb2d3b;
        }
        .reset-footer {
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
                        <a class="nav-link" href="login.php"><i class="bi bi-box-arrow-in-right me-1"></i> Connexion</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Password Reset Section -->
    <section class="reset-section">
        <div class="reset-overlay"></div>
        <div class="reset-container container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="reset-card">
                        <div class="reset-header">
                            <img src="assets/logo-removebg.png" alt="Logo Wilaya" class="reset-logo">
                            <h2>Réinitialisation du mot de passe</h2>
                        </div>
                        <div class="card-body p-5">
                            <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
                                <div class="mb-4">
                                    <label for="email" class="form-label fw-bold">Adresse email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                                        <input type="email" class="form-control" id="email" placeholder="Entrez votre adresse email" required name="email">
                                    </div>
                                    <small class="text-muted">Un lien de réinitialisation vous sera envoyé par email.</small>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-reset btn-lg text-white">
                                        <i class="bi bi-send-fill me-2"></i> Envoyer le lien
                                    </button>
                                </div>
                            </form>
                            
                            <div class="reset-footer">
                                <p class="mb-0">Retour à la <a href="login.php" class="text-danger">page de connexion</a></p>
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
<?php
include("config.php");
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$smtp_host = 'smtp.gmail.com';
$smtp_port = 587; 
$sender_email = 'brahimchahlafi273@gmail.com';
$sender_password = 'ggnl wiwn sudl pczk';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
$user_email=$_POST['email'];

$sql="select email,mot_de_passe from utilisateurs where email='$user_email'";

$result=mysqli_query($conDb,$sql);
$row = mysqli_fetch_assoc($result);

if(mysqli_num_rows($result) == 0){
    echo"user does not exist ";
}else{
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = $smtp_host;
    $mail->SMTPAuth   = true;
    $mail->Username   = $sender_email;
    $mail->Password   = $sender_password;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $smtp_port;
    $mail->setFrom($sender_email);
    $mail->addAddress($user_email);
    $mail->isHTML(true);
    $mail->Subject = 'Your Account Password';
$mail->Body = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { color: #2c3e50; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .content { padding: 20px 0; }
        .password { 
            background-color: #f8f9fa; 
            padding: 10px; 
            border-left: 4px solid #3498db;
            margin: 15px 0;
            font-family: monospace;
        }
        .footer { font-size: 12px; color: #7f8c8d; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Récupération de votre mot de passe</h2>
        </div>
        
        <div class="content">
            <p>Bonjour,</p>
            
            <p>Vous avez demandé la récupération de votre mot de passe pour le système de Gestion des Stagiaires.</p>
            
            <p><strong>Vos identifiants de connexion :</strong></p>
            <div class="password">
                <strong>Email:</strong> ' . $user_email . '<br>
                <strong>Mot de passe:</strong> ' . $row['mot_de_passe'] . '
            </div>
            
            <p>Pour votre sécurité, nous vous recommandons fortement de :</p>
            <ul>
                <li>Vous connecter immédiatement</li>
                <li>Modifier ce mot de passe dans votre profil</li>
                <li>Ne jamais partager vos identifiants</li>
            </ul>
            
            <p>Si vous n\'êtes pas à l\'origine de cette demande, veuillez contacter immédiatement l\'administrateur du système.</p>
        </div>
        
        <div class="footer">
            <p>Cet email a été généré automatiquement - merci de ne pas y répondre.</p>
            <p>&copy; ' . date('Y') . ' Gestion des Stagiaires</p>
        </div>
    </div>
</body>
</html>
';
    $mail->send();
    header("location:motP_succ.php");
}}
?>