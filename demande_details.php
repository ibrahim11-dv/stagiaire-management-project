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
$message = '';

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
    header("Location: postules.php"); // Rediriger vers la liste des candidats si non trouvé
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
    <title>Détails Candidat - Plateforme Stages</title>
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
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border: none;
            margin-bottom: 20px;
            border-top: 3px solid #dc3545;
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: none;
            border-radius: 15px 15px 0 0 !important;
            font-weight: 600;
            color: #495057;
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
                        <a class="nav-link" href="stages.php">
                            <i class="bi bi-briefcase"></i> Stages
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="demandes.php">
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
                    <h2 class="mb-0">Détails du Candidat</h2>
                    <div class="d-flex align-items-center">
                        <span class="me-3"></span>
                        <img src="" class="rounded-circle" >
                    </div>
                </div>

                <!-- Back Button -->
                <div class="mb-4">
                    <a href="demandes.php" class="btn btn-outline-primary">
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

                <div class="row">
                    <!-- Left Column - Profile Image and Basic Info -->
                    <div class="col-lg-4">
                        <div class="profile-section text-center mb-4">
                            <img src="<?= htmlspecialchars($candidate['img_url']) ?>" class="profile-img mb-3" alt="Photo de profil">
                            <h2 class="mb-1"><?= htmlspecialchars($candidate['prenom']) ?></h2>
                            <h2 class="mb-3"><?= htmlspecialchars($candidate['nom']) ?></h2>
                            <span class="badge bg-secondary">
                                <?= htmlspecialchars($candidate['status'] ?? 'Candidat') ?>
                            </span>
                        </div>

                        <!-- Documents Section -->
                        <div class="card mb-4">
                            <div class="card-header d-flex align-items-center">
                                <i class="bi bi-files me-2"></i>
                                <span>Documents</span>
                            </div>
                            <div class="card-body">
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
                                
                                <?php if (!empty($candidate['diplome_url'])): ?>
                                    <a href="<?= htmlspecialchars($candidate['diplome_url']) ?>" target="_blank" class="document-link">
                                        <i class="bi bi-file-earmark-check me-2"></i> Diplôme
                                    </a>
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
                                        <p class="form-control-plaintext"><?= htmlspecialchars($candidate['cin']) ?></p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($candidate['email']) ?></p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Téléphone</label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($candidate['telephone']) ?></p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Sexe</label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($candidate['sexe']) == 'homme' ? 'Homme' : 'Femme' ?></p>
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
                                        <p class="form-control-plaintext"><?= htmlspecialchars($candidate['diplome']) ?></p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Établissement</label>
                                        <p class="form-control-plaintext"><?= htmlspecialchars($candidate['etablissement']) ?></p>
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
                                        <p class="form-control-plaintext"><?= !empty($candidate['date_debut']) ? date('d/m/Y', strtotime($candidate['date_debut'])) : 'Non spécifiée' ?></p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Date de fin</label>
                                        <p class="form-control-plaintext"><?= !empty($candidate['date_fin']) ? date('d/m/Y', strtotime($candidate['date_fin'])) : 'Non spécifiée' ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-end mt-4">
                    <button class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#emailModal">
                        <i class="bi bi-envelope me-2"></i> Envoyer un message
                    </button>
                    
                    <!-- Boutons d'action pour le candidat -->
                    <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#acceptModal">
                        <i class="bi bi-check-circle me-2"></i> Accepter
                    </button>
                    
                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="bi bi-x-circle me-2"></i> Rejeter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'acceptation -->
    <div class="modal fade" id="acceptModal" tabindex="-1" aria-labelledby="acceptModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="acceptModalLabel">Accepter la candidature</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="process_candidate.php" method="POST">
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir accepter cette candidature?</p>
                        <input type="hidden" name="action" value="accept">
                        <input type="hidden" name="candidate_id" value="<?= $candidate_id ?>">
                        
                        <div class="mb-3">
                            <label for="stageSelect" class="form-label">Assigner à un stage:</label>
                            <select class="form-select" id="stageSelect" name="stage_id" required>
                                <option value="">Sélectionner un stage</option>
                                <?php
                                $stages_query = "SELECT id, sujet, date_debut FROM stage";
                                $stages_result = mysqli_query($conDb, $stages_query);
                                while($stage = $stages_result->fetch_assoc()): ?>
                                    <option value="<?= $stage['id'] ?>">
                                        <?= htmlspecialchars($stage['sujet']) ?> (<?= date('d/m/Y', strtotime($stage['date_debut'])) ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="supervisorSelect" class="form-label">Assigner un encadrant:</label>
                            <select class="form-select" id="supervisorSelect" name="supervisor_id" required>
                                <option value="">Sélectionner un encadrant</option>
                                <?php
                                $supervisors_query = "SELECT id, CONCAT(prenom, ' ', nom) as full_name FROM encadrant";
                                $supervisors_result = mysqli_query($conDb, $supervisors_query);
                                while($supervisor = $supervisors_result->fetch_assoc()): ?>
                                    <option value="<?= $supervisor['id'] ?>"><?= htmlspecialchars($supervisor['full_name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">Confirmer l'acceptation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de rejet -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="rejectModalLabel">Rejeter la candidature</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="process_candidate.php" method="POST">
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir rejeter cette candidature?</p>
                        <div class="mb-3">
                            <label for="rejectReason" class="form-label">Raison du rejet (optionnel):</label>
                            <textarea class="form-control" id="rejectReason" name="reject_reason" rows="3"></textarea>
                        </div>
                        <input type="hidden" name="action" value="reject">
                        <input type="hidden" name="candidate_id" value="<?= $candidate_id ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger">Confirmer le rejet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Ajoutez ce modal après les autres modals -->
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
    </script>
</body>
</html>