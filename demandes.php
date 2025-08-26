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

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Demandes - Plateforme Stagiaires</title>
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
        .btn-add {
            background-color: #dc3545;
            color: white;
        }
        .btn-add:hover {
            background-color: #bb2d3b;
            color: white;
        }
        img{
            cursor: pointer;
        }
        .action-btn {
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 2px;
        }
        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
        }

        .table tbody tr {
            height: 60px;
        }

        .card-body {
            min-height: 800px; /* Adjust this value as needed */
            display: flex;
            flex-direction: column;
        }
        .table-responsive {
            flex: 1;
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #dc3545, #b02a37);
        }

       .modal-content {
            border-radius: 12px;
            overflow: hidden;
        }

       .rounded-pill {
            border-radius: 50px !important;
        }

        /* Animation */
        .modal.fade .modal-dialog {
            transform: translateY(-20px);
            transition: transform 0.3s ease-out;
        }

      .modal.show .modal-dialog {
            transform: translateY(0);
        }

        /*pagination */
        .page-item.active .page-link {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: white !important;
        }
        
        /* Modal success colors */
        .modal-success .modal-header {
            background-color: #28a745;
            color: white;
        }
        
        /* Modal danger colors */
        .modal-danger .modal-header {
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
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="stagiaires.php">
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
                        <a class="nav-link active" href="demandes.php">
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
                    <h2 class="mb-0">Gestion des Demandes</h2>
                    <div class="d-flex align-items-center">
                        <span class="me-3"></span>
                        <img src="" class="rounded-circle" >
                    </div>
                </div>

                <!-- Search -->
                <div class="d-flex justify-content-between mb-4">
                    <div></div> <!-- Empty div for alignment -->
                    <div class="col-md-5">
                        <form method="get" action="<?= $_SERVER['PHP_SELF'] ?>" class="input-group">
                            <select class="form-select search-select" name="search_by" id="search_by">
                                <option value="nom">Nom Complet</option>
                                <option value="cin">CIN</option>
                            </select>
                            <input type="text" class="form-control search-input" placeholder="Rechercher..." name="search_term" id="text_search">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Demandes Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Profile</th>
                                        <th>ID</th>
                                        <th>Nom Complet</th>
                                        <th>CIN</th>
                                        <th>Sexe</th>
                                        <th>Etablissement</th>
                                        <th>Filière</th>
                                        <th>Type de Stage</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
                                        $items_per_page = 10;
                                        $start_index = ($current_page - 1) * $items_per_page;
                                        $sql = "SELECT * FROM postule WHERE 1=1";
                                        if($_SERVER['REQUEST_METHOD']=='GET' && isset($_GET['search_term']) && !empty($_GET['search_term']) ){
                                            $search=mysqli_real_escape_string($conDb, $_GET['search_by']);
                                            $searched=mysqli_real_escape_string($conDb, $_GET['search_term']);
                                                switch($search){
                                                    case 'nom': $sql .= ' AND CONCAT(nom, " ", prenom) LIKE "%'.$searched.'%"';break;
                                                    case 'cin': $sql .= ' AND cin LIKE "%'.$searched.'%"';break;
                                                }
                                        }
                                        $sql .= " LIMIT $start_index, $items_per_page";
                                        $result = mysqli_query($conDb, $sql);
                                        while($row = $result->fetch_assoc()) {

                                            switch($row['type_id']){
                                            case 1 : $stage_type='PFE';
                                                break;
                                            case 2 : $stage_type='Initiation';
                                                break;
                                            case 3 : $stage_type='Perfectionnement';
                                                break;
                                            case 4 : $stage_type='ouvrier';
                                                break;
                                            case 5 : $stage_type='Technicien';
                                                break;
                                        }

                                            // Set default image based on gender
                                            if(empty($row['img_url'])) {
                                                $row['img_url'] = ($row['sexe'] == 'homme') ? "images/homme_default.jpg" : "images/femme_default.jpg";
                                            }
                                            
                                            echo '
                                            <tr>
                                                <td><img src="'.$row['img_url'].'" width="50px" height="50px" alt="no photo profile" class="rounded" onclick="window.location.href=\'demande_details.php?id='.$row['id'].'\'"></td>
                                                <td>'.htmlspecialchars($row['id']).'</td>
                                                <td>'.htmlspecialchars($row['nom']).' '.htmlspecialchars($row['prenom']).'</td>
                                                <td>'.htmlspecialchars($row['cin']).'</td>
                                                <td>'.htmlspecialchars($row['sexe']).'</td>
                                                <td>'.htmlspecialchars($row['etablissement']).'</td>
                                                <td>'.htmlspecialchars($row['diplome']).'</td>
                                                <td>'.htmlspecialchars($stage_type).'</td>
                                                <td class="actions" data-id="'.htmlspecialchars($row['id']).'">
                                                    <button class="btn btn-sm btn-outline-info action-btn" title="Détails"><i class="bi bi-eye"></i></button>
                                                    <button class="btn btn-sm btn-outline-success action-btn" title="Accepter"><i class="bi bi-check-circle"></i></button>
                                                    <button class="btn btn-sm btn-outline-danger action-btn" title="Refuser"><i class="bi bi-x-circle"></i></button>
                                                </td>
                                            </tr>';
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <?php
                                $current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
                                $search_by = isset($_GET['search_by']) ? $_GET['search_by'] : '';
                                $search_term = isset($_GET['search_term']) ? $_GET['search_term'] : '';
                                $sql_p = 'SELECT COUNT(*) AS total FROM postule WHERE 1=1';
                                if($_SERVER['REQUEST_METHOD']=='GET' && isset($_GET['search_term']) && !empty($_GET['search_term']) ){
                                    $search=mysqli_real_escape_string($conDb, $_GET['search_by']);
                                    $searched=mysqli_real_escape_string($conDb, $_GET['search_term']);
                                    switch($search){
                                        case 'nom': $sql_p .= ' AND CONCAT(nom, " ", prenom) LIKE "%'.$searched.'%"';break;
                                        case 'cin': $sql_p .= ' AND cin LIKE "%'.$searched.'%"'; break;
                                    }
                                }
                                $result_p = mysqli_query($conDb, $sql_p);
                                $demande_nmb = mysqli_fetch_assoc($result_p);
                                $total_pages = ceil($demande_nmb['total'] / 10);

                                // Previous button
                                echo '<li class="page-item '.($current_page == 1 ? 'disabled' : '').'">
                                    <a class="page-link text-dark" href="?page='.($current_page - 1).'&search_by='.$search_by.'&search_term='.$search_term.'">Précédent</a>
                                </li>';

                                // Page numbers
                                for($i = 1; $i <= $total_pages; $i++) {
                                    $active = ($i == $current_page) ? ' active' : '';
                                    echo '<li class="page-item '.$active.'">
                                        <a class="page-link '.($i == $current_page ? 'bg-danger text-white' : 'text-dark').'" href="?page='.$i.'&search_by='.$search_by.'&search_term='.$search_term.'">'.$i.'</a>
                                    </li>';
                                }

                                // Next button
                                echo '<li class="page-item '.($current_page == $total_pages ? 'disabled' : '').'">
                                    <a class="page-link text-dark" href="?page='.($current_page + 1).'&search_by='.$search_by.'&search_term='.$search_term.'">Suivant</a>
                                </li>';
                                ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="actionModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0" id="modalHeader">
                    <h5 class="modal-title fw-bold text-white" id="modalTitle">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmation
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="text-center mb-3">
                        <i class="bi bi-question-circle-fill" id="modalIcon" style="font-size: 3rem;"></i>
                    </div>
                    <h6 class="text-center mb-3 fw-bold" id="modalMessage">Êtes-vous sûr de vouloir effectuer cette action ?</h6>
                    <p class="text-muted text-center small" id="modalDescription">
                        Cette action sera enregistrée dans le système.
                    </p>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-outline-secondary px-4 rounded-pill" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i> Annuler
                    </button>
                    <button type="button" class="btn px-4 rounded-pill" id="confirmAction">
                        <i class="bi bi-check-circle me-2"></i> Confirmer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const actionModal = new bootstrap.Modal(document.getElementById('actionModal'));
            let demandeIdToAction = null;
            let actionType = null;

            // Gestionnaire pour le bouton Détails
            document.querySelectorAll('.btn-outline-info.action-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.closest('td.actions').getAttribute('data-id');
                    window.location.href = 'demande_details.php?id=' + id;
                });
            });

            // Gestionnaire pour le bouton Accepter
            document.querySelectorAll('.btn-outline-success.action-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    demandeIdToAction = this.closest('td.actions').getAttribute('data-id');
                    actionType = 'accept';
                    
                    // Update modal content for accept action
                    document.getElementById('modalHeader').className = 'modal-header bg-success text-white border-0';
                    document.getElementById('modalTitle').innerHTML = '<i class="bi bi-check-circle-fill me-2"></i> Accepter la demande';
                    document.getElementById('modalIcon').className = 'bi bi-check-circle-fill text-success';
                    document.getElementById('modalMessage').textContent = 'Êtes-vous sûr de vouloir accepter cette demande ?';
                    document.getElementById('modalDescription').textContent = 'Le stagiaire sera ajouté à la liste des stagiaires.';
                    document.getElementById('confirmAction').className = 'btn btn-success px-4 rounded-pill';
                    
                    actionModal.show();
                });
            });

            // Gestionnaire pour le bouton Refuser
            document.querySelectorAll('.btn-outline-danger.action-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    demandeIdToAction = this.closest('td.actions').getAttribute('data-id');
                    actionType = 'refuse';
                    
                    // Update modal content for refuse action
                    document.getElementById('modalHeader').className = 'modal-header bg-danger text-white border-0';
                    document.getElementById('modalTitle').innerHTML = '<i class="bi bi-x-circle-fill me-2"></i> Refuser la demande';
                    document.getElementById('modalIcon').className = 'bi bi-x-circle-fill text-danger';
                    document.getElementById('modalMessage').textContent = 'Êtes-vous sûr de vouloir refuser cette demande ?';
                    document.getElementById('modalDescription').textContent = 'La demande sera marquée comme refusée.';
                    document.getElementById('confirmAction').className = 'btn btn-danger px-4 rounded-pill';
                    
                    actionModal.show();
                });
            });

            // Handle the "Confirm Action" button click
            document.getElementById('confirmAction').addEventListener('click', function() {
                if (demandeIdToAction && actionType) {
                    window.location.href = 'demande_action.php?id=' + demandeIdToAction + '&action=' + actionType ;
                }
            });
        });
    </script>
</body>
</html>