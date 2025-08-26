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
    <title>Gestion des Stages - Plateforme Stagiaires</title>
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
        .status-active {
            background-color: #d1e7dd;
            color: #0f5132;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 0.85rem;
        }
        .status-inactive {
            background-color: #f8d7da;
            color: #842029;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 0.85rem;
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
        .page-item.active .page-link {
            background-color: #dc3545 !important; /* Red background */
            border-color: #dc3545 !important; /* Red border */
            color: white !important; /* White text */
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

        .page-item.active .page-link {
            background-color: #dc3545 !important; /* Red background */
            border-color: #dc3545 !important; /* Red border */
            color: white !important; /* White text */
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
                        <a class="nav-link active" href="stages.php">
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
                    <h2 class="mb-0">Gestion des Stages</h2>
                    <div class="d-flex align-items-center">
                        <span class="me-3"></span>
                        <img src="" class="rounded-circle">
                    </div>
                </div>

                <!-- Add Button and Search -->
                <div class="d-flex justify-content-between mb-4">
                    <button class="btn btn-add" onclick="window.location.href='stage_add.php'">
                        <i class="bi bi-plus-circle me-2"></i>Ajouter Stage
                    </button>
                    <div class="col-md-5">
                        <form method="get" action="<?= $_SERVER['PHP_SELF'] ?>" class="input-group">
                            <select class="form-select search-select" name="search_by">
                                <option value="sujet">Sujet</option>
                                <option value="annee">Année Universitaire</option>
                                <option value="type">Type</option>
                            </select>
                            <input type="text" class="form-control search-input" placeholder="Rechercher..." name="search_term" value="<?= isset($_GET['search_term']) ? htmlspecialchars($_GET['search_term']) : '' ?>">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Stages Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Sujet</th>
                                        <th>Date Début</th>
                                        <th>Date Fin</th>
                                        <th>Objectifs</th>
                                        <th>Année Universitaire</th>
                                        <th>Type</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
                                        $items_per_page = 10;
                                        $start_index = ($current_page - 1) * $items_per_page;
                                        $sql = "SELECT s.*, t.intitule as type_nom 
                                                FROM stage s 
                                                JOIN type_stage t ON s.type_id = t.id 
                                                WHERE s.est_supp = 0";
                                        
                                        // Add search conditions if search term is provided
                                        if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['search_term']) && !empty($_GET['search_term'])) {
                                            $search = mysqli_real_escape_string($conDb, $_GET['search_by']);
                                            $searched = mysqli_real_escape_string($conDb, $_GET['search_term']);
                                            
                                            switch($search) {
                                                case 'sujet': 
                                                    $sql .= ' AND s.sujet LIKE "%'.$searched.'%"';
                                                    break;
                                                case 'annee': 
                                                    $sql .= ' AND s.annee_universitaire LIKE "%'.$searched.'%"';
                                                    break;
                                                case 'type': 
                                                    $sql .= ' AND t.intitule LIKE "%'.$searched.'%"';
                                                    break;
                                            }
                                        }
                                        
                                        $sql .= " LIMIT $start_index, $items_per_page";
                                        $result = mysqli_query($conDb, $sql);
                                        
                                        while($row = $result->fetch_assoc()) {
                                            echo '
                                            <tr>
                                                <td>'. htmlspecialchars($row['id']) .'</td>
                                                <td>'. htmlspecialchars($row['sujet']) .'</td>
                                                <td>'. htmlspecialchars($row['date_debut']) .'</td>
                                                <td>'. htmlspecialchars($row['date_fin']) .'</td>
                                                <td>'. htmlspecialchars(substr($row['objectifs'], 0, 50)) .'...</td>
                                                <td>'. htmlspecialchars($row['annee_universitaire']) .'</td>
                                                <td>'. htmlspecialchars($row['type_nom']) .'</td>
                                                <td class="actions" data-id="'. htmlspecialchars($row['id']) .'">
                                                    <button class="btn btn-sm btn-outline-info action-btn" title="Détails"><i class="bi bi-eye"></i></button>
                                                    <button class="btn btn-sm btn-outline-danger action-btn" title="Supprimer"><i class="bi bi-trash"></i></button>
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
                                <li class="page-item <?= ($current_page == 1) ? 'disabled' : '' ?>">
                                    <a class="page-link text-dark" href="?page=<?= $current_page - 1 ?><?= isset($_GET['search_by']) ? '&search_by='.$_GET['search_by'] : '' ?><?= isset($_GET['search_term']) ? '&search_term='.$_GET['search_term'] : '' ?>">Précédent</a>
                                </li>
                                <?php
                                $sql_p = 'SELECT COUNT(*) AS total FROM stage s JOIN type_stage t ON s.type_id = t.id WHERE s.est_supp = 0';
                                
                                // Add search conditions to count query if search term is provided
                                if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['search_term']) && !empty($_GET['search_term'])) {
                                    $search = mysqli_real_escape_string($conDb, $_GET['search_by']);
                                    $searched = mysqli_real_escape_string($conDb, $_GET['search_term']);
                                    
                                    switch($search) {
                                        case 'sujet': 
                                            $sql_p .= ' AND s.sujet LIKE "%'.$searched.'%"';
                                            break;
                                        case 'annee': 
                                            $sql_p .= ' AND s.annee_universitaire LIKE "%'.$searched.'%"';
                                            break;
                                        case 'type': 
                                            $sql_p .= ' AND t.intitule LIKE "%'.$searched.'%"';
                                            break;
                                    }
                                }
                                
                                $result_p = mysqli_query($conDb, $sql_p);
                                $stage_nmb = mysqli_fetch_assoc($result_p);
                                $total_pages = ceil($stage_nmb['total'] / 10);
                                
                                $search_by = isset($_GET['search_by']) ? $_GET['search_by'] : '';
                                $search_term = isset($_GET['search_term']) ? $_GET['search_term'] : '';
                                
                                for($i = 1; $i <= $total_pages; $i++) {
                                    $active = ($i == $current_page) ? 'active' : '';
                                    echo '<li class="page-item '.$active.'">
                                        <a class="page-link '.($i == $current_page ? 'bg-danger text-white' : 'text-dark').'" href="?page='.$i.'&search_by='.$search_by.'&search_term='.$search_term.'">'.$i.'</a>
                                    </li>';
                                }
                                ?>
                                <li class="page-item <?= ($current_page == $total_pages) ? 'disabled' : '' ?>">
                                    <a class="page-link text-dark" href="?page=<?= $current_page + 1 ?><?= isset($_GET['search_by']) ? '&search_by='.$_GET['search_by'] : '' ?><?= isset($_GET['search_term']) ? '&search_term='.$_GET['search_term'] : '' ?>">Suivant</a>
                                </li>
                            </ul>
                        </nav>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal (Improved Design) -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
        <!-- Modal Header -->
        <div class="modal-header bg-gradient-danger text-white border-0">
            <h5 class="modal-title fw-bold">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirmation
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        
        <!-- Modal Body -->
        <div class="modal-body py-4">
            <div class="text-center mb-3">
            <i class="bi bi-trash3-fill text-danger" style="font-size: 3rem;"></i>
            </div>
            <h6 class="text-center mb-3 fw-bold">Êtes-vous sûr de vouloir supprimer ce stagiaire ?</h6>
            <p class="text-muted text-center small">
            Cette action est irréversible. Toutes les données associées seront perdues.
            </p>
        </div>
        
        <!-- Modal Footer -->
        <div class="modal-footer border-0 justify-content-center">
            <button type="button" class="btn btn-outline-secondary px-4 rounded-pill" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-2"></i> Annuler
            </button>
            <button type="button" class="btn btn-danger px-4 rounded-pill" id="confirmDelete">
            <i class="bi bi-trash3-fill me-2"></i> Supprimer
            </button>
        </div>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestionnaire pour le bouton Voir détails
            document.querySelectorAll('.btn-outline-info.action-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.closest('td.actions').getAttribute('data-id');
                    window.location.href = 'stage_details.php?id=' + id;
                });
            });
            
            // Gestionnaire pour le bouton Supprimer
            //bootstraps modal

            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            let stageid = null;

            // Gestionnaire pour le bouton Supprimer
            document.querySelectorAll('.btn-outline-danger.action-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    stageid = this.closest('td.actions').getAttribute('data-id');
                    deleteModal.show(); // Show the modal
                });
            });

            // Handle the "Confirm Delete" button click
            document.getElementById('confirmDelete').addEventListener('click', function() {
                if (stageid) {
                    window.location.href = 'stage_delete.php?id=' + stageid+'&page='+<?php echo $current_page?>;
                }
            });
        });
    </script>
</body>
</html>