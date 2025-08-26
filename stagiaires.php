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
    <title>Gestion des Stagiaires - Plateforme Stagiaires</title>
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

        /*print*/
        @media print {
            body * {
                visibility: hidden;
            }
            .table-responsive, .table-responsive * {
                visibility: visible;
            }
            .table-responsive {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                height: auto;
            }
            /* Cibler spécifiquement la colonne Actions */
            th:nth-child(9), td:nth-child(9) {
                display: none !important;
            }
            table {
                font-size: 12px;
            }
            thead {
                background-color: #f8f9fa !important;
                color: #000 !important;
            }
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

                <!-- Add Button and Search -->
                <div class="d-flex justify-content-between mb-4">
                    <div>
                    <button class="btn btn-add" onclick="window.location.href='stagiaires_add.php'">
                        <i class="bi bi-plus-circle me-2"></i>Ajouter Stagiaire
                    </button>
                    <button class="btn btn-secondary ms-2" id="printTableBtn">
                        <i class="bi bi-printer me-2"></i>Imprimer le tableau
                    </button>
                    </div>
                    <div class="col-md-5">
                        <form method="get" action="<?= $_SERVER['PHP_SELF'] ?>" class="input-group">
                            <select class="form-select search-select" name="search_by" id="search_by">
                                <option value="nom" <?= isset($_GET['search_by']) && $_GET['search_by'] == 'nom' ? 'selected' : '' ?>>Nom Complet</option>
                                <option value="cin" <?= isset($_GET['search_by']) && $_GET['search_by'] == 'cin' ? 'selected' : '' ?>>CIN</option>
                                <option value="status" <?= isset($_GET['search_by']) && $_GET['search_by'] == 'status' ? 'selected' : '' ?>>Statut</option>
                            </select>
                            <input type="text" class="form-control search-input" placeholder="Rechercher..." 
                                   name="search_text" id="text_search" 
                                   value="<?= isset($_GET['search_by']) && $_GET['search_by'] != 'status' ? htmlspecialchars($_GET['search_text'] ?? '') : '' ?>">
                            <select class="form-select search-input d-none" id="select_search" name="search_status">
                                <option value="">Choisir un statut</option>
                                <option value="En cours" <?= isset($_GET['search_status']) && $_GET['search_status'] == 'En cours' ? 'selected' : '' ?>>En cours</option>
                                <option value="terminé" <?= isset($_GET['search_status']) && $_GET['search_status'] == 'terminé' ? 'selected' : '' ?>>Terminé</option>
                            </select>
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Stagiaires Table -->
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
                                        <th>Encadrant</th>
                                        <th>Filière</th>
                                        <th>Stage</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $stage_type = NULL;
                                        $current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
                                        $items_per_page = 10;
                                        $start_index = ($current_page - 1) * $items_per_page;
                                        $sql = "SELECT * FROM stagiaire WHERE est_supp = false";
                                        
                                        // Handle search functionality
                                        if($_SERVER['REQUEST_METHOD'] == 'GET' && (isset($_GET['search_text']) || isset($_GET['search_status']))) {
                                            $search = mysqli_real_escape_string($conDb, $_GET['search_by'] ?? '');
                                            $searched = '';
                                            
                                            if($search === 'status') {
                                                $searched = isset($_GET['search_status']) ? mysqli_real_escape_string($conDb, $_GET['search_status']) : '';
                                            } else {
                                                $searched = isset($_GET['search_text']) ? mysqli_real_escape_string($conDb, $_GET['search_text']) : '';
                                            }
                                            
                                            if(!empty($searched)) {
                                                switch($search) {
                                                    case 'nom': 
                                                        $sql .= " AND CONCAT(nom, ' ', prenom) LIKE '%$searched%'";
                                                        break;
                                                    case 'cin': 
                                                        $sql .= " AND cin LIKE '%$searched%'"; 
                                                        break;
                                                    case 'status': 
                                                        $sql .= " AND status = '$searched'";
                                                        break;
                                                }
                                            }
                                        }
                                        
                                        $sql .= " LIMIT $start_index, $items_per_page";
                                        $result = mysqli_query($conDb, $sql);
                                        
                                        while($row = $result->fetch_assoc()) {
                                            switch($row['type_id']) {
                                                case 1: $stage_type = 'PFE'; break;
                                                case 2: $stage_type = 'Initiation'; break;
                                                case 3: $stage_type = 'Perfectionnement'; break;
                                                case 4: $stage_type = 'ouvrier'; break;
                                                case 5: $stage_type = 'Technicien'; break;
                                            }
                                            
                                            if(empty($row['image_url'])) {
                                                switch($row['sexe']) {
                                                    case 'homme': $row['image_url'] = "images/homme_default.jpg"; break;
                                                    case 'femme': $row['image_url'] = "images/femme_default.jpg"; break;
                                                }
                                            }
                                            
                                            $sql2 = 'SELECT * FROM encadrant WHERE id = '.$row['encadrant_id'];
                                            $result2 = mysqli_query($conDb, $sql2);
                                            $encadrant = $result2->fetch_assoc();
                                            
                                            if($row['status'] == 'terminé') {
                                                echo '
                                                <tr>
                                                    <td><img src="' . $row["image_url"] . '" width="50px" height="50px" alt="no photo profile" class="rounded" onclick="window.location.href=\'stagiaires_details.php?id='.$row['id'].'\'"></td>
                                                    <td>'. htmlspecialchars($row['id']) .'</td>
                                                    <td>'. htmlspecialchars($row['nom']) ." ".htmlspecialchars($row['prenom']).'</td>
                                                    <td>'. htmlspecialchars($row['cin']) .'</td>
                                                    <td>'. htmlspecialchars($encadrant['nom']) ." ".htmlspecialchars($encadrant['prenom']).'</td>
                                                    <td>'. htmlspecialchars($row['diplome']) .'</td>
                                                    <td>'. htmlspecialchars($stage_type) .'</td>
                                                    <td><span class="status active">'. htmlspecialchars($row['status']) .'</span></td>
                                                    <td class="actions" data-id="'. htmlspecialchars($row['id']) .'">
                                                        <button class="btn btn-sm btn-outline-info action-btn" title="Détails"><i class="bi bi-eye"></i></button>
                                                        <button class="btn btn-sm btn-outline-danger action-btn" title="Supprimer"><i class="bi bi-trash"></i></button>
                                                        <button class="btn btn-sm btn-outline-primary action-btn" title="Imprimer"><i class="bi bi-printer"></i></button>
                                                    </td>
                                                </tr>';
                                            } else {
                                                echo '
                                                <tr>
                                                    <td><img src="' . $row["image_url"] . '" width="50px" height="50px" alt="no photo profile" class="rounded" onclick="window.location.href=\'stagiaires_details.php?id='.$row['id'].'\'"></td>
                                                    <td>'. htmlspecialchars($row['id']) .'</td>
                                                    <td>'. htmlspecialchars($row['nom']) ." ".htmlspecialchars($row['prenom']).'</td>
                                                    <td>'. htmlspecialchars($row['cin']) .'</td>
                                                    <td>'. htmlspecialchars($encadrant['nom']) ." ".htmlspecialchars($encadrant['prenom']).'</td>
                                                    <td>'. htmlspecialchars($row['diplome']) .'</td>
                                                    <td>'. htmlspecialchars($stage_type) .'</td>
                                                    <td><span class="status active">'. htmlspecialchars($row['status']) .'</span></td>
                                                    <td class="actions" data-id="'. htmlspecialchars($row['id']) .'">
                                                        <button class="btn btn-sm btn-outline-info action-btn" title="Détails"><i class="bi bi-eye"></i></button>
                                                        <button class="btn btn-sm btn-outline-danger action-btn" title="Supprimer"><i class="bi bi-trash"></i></button>
                                                    </td>
                                                </tr>';
                                            }
                                        }
                                    ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?= ($current_page == 1) ? 'disabled' : '' ?>">
                                    <a class="page-link text-dark" href="?page=<?= $current_page - 1 ?><?= isset($_GET['search_by']) ? '&search_by='.$_GET['search_by'] : '' ?><?= (isset($_GET['search_by']) && $_GET['search_by'] == 'status' && isset($_GET['search_status'])) ? '&search_status='.$_GET['search_status'] : '' ?><?= (isset($_GET['search_by']) && $_GET['search_by'] != 'status' && isset($_GET['search_text'])) ? '&search_text='.$_GET['search_text'] : '' ?>">Précédent</a>
                                </li>
                                <?php
                                $sql_p = 'SELECT COUNT(*) AS total FROM stagiaire WHERE est_supp = false';
                                
                                // Apply search filters to pagination count if they exist
                                if(isset($_GET['search_by'])) {
                                    $search = $_GET['search_by'];
                                    $searched = '';
                                    
                                    if($search === 'status' && isset($_GET['search_status'])) {
                                        $searched = $_GET['search_status'];
                                    } elseif(isset($_GET['search_text'])) {
                                        $searched = $_GET['search_text'];
                                    }
                                    
                                    if(!empty($searched)) {
                                        switch($search) {
                                            case 'nom': 
                                                $sql_p .= " AND CONCAT(nom, ' ', prenom) LIKE '%$searched%'";
                                                break;
                                            case 'cin': 
                                                $sql_p .= " AND cin LIKE '%$searched%'"; 
                                                break;
                                            case 'status': 
                                                $sql_p .= " AND status = '$searched'";
                                                break;
                                        }
                                    }
                                }
                                
                                $result_p = mysqli_query($conDb, $sql_p);
                                $intern_nmb = mysqli_fetch_assoc($result_p);
                                $total_pages = ceil($intern_nmb['total'] / $items_per_page);

                                for($i = 1; $i <= $total_pages; $i++) {
                                    $active = ($i == $current_page) ? ' active' : '';
                                    echo '<li class="page-item '.$active.'">
                                        <a class="page-link '.($i == $current_page ? 'bg-danger text-white' : 'text-dark').'" 
                                           href="?page='.$i.
                                           (isset($_GET['search_by']) ? '&search_by='.$_GET['search_by'] : '').
                                           ((isset($_GET['search_by']) && $_GET['search_by'] == 'status' && isset($_GET['search_status'])) ? '&search_status='.$_GET['search_status'] : '').
                                           ((isset($_GET['search_by']) && $_GET['search_by'] != 'status' && isset($_GET['search_text'])) ? '&search_text='.$_GET['search_text'] : '').'">'.$i.'</a>
                                    </li>';
                                }
                                ?>
                                <li class="page-item <?= ($current_page == $total_pages) ? 'disabled' : '' ?>">
                                    <a class="page-link text-dark" href="?page=<?= $current_page + 1 ?><?= isset($_GET['search_by']) ? '&search_by='.$_GET['search_by'] : '' ?><?= (isset($_GET['search_by']) && $_GET['search_by'] == 'status' && isset($_GET['search_status'])) ? '&search_status='.$_GET['search_status'] : '' ?><?= (isset($_GET['search_by']) && $_GET['search_by'] != 'status' && isset($_GET['search_text'])) ? '&search_text='.$_GET['search_text'] : '' ?>">Suivant</a>
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
    <script src="print_attestation/printf.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Bootstrap modal
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            let stagiaireIdToDelete = null;

            // Handle view details button
            document.querySelectorAll('.btn-outline-info.action-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.closest('td.actions').getAttribute('data-id');
                    window.location.href = 'stagiaires_details.php?id=' + id;
                });
            });

            // Handle delete button
            document.querySelectorAll('.btn-outline-danger.action-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    stagiaireIdToDelete = this.closest('td.actions').getAttribute('data-id');
                    deleteModal.show();
                });
            });

            // Handle print attestation button
            document.querySelectorAll('.btn-outline-primary.action-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    const id = this.closest('td.actions').getAttribute('data-id');
                    window.location.href = 'print.php?id=' + id;
                });
            });

            // Confirm delete action
            document.getElementById('confirmDelete').addEventListener('click', function() {
                if (stagiaireIdToDelete) {
                    window.location.href = 'stagiaires_delete.php?id=' + stagiaireIdToDelete + '&page=' + <?= $current_page ?>;
                }
            });

            // Search type management
            const searchBy = document.getElementById('search_by');
            const textSearch = document.getElementById('text_search');
            const selectSearch = document.getElementById('select_search');
            
            // Initialize based on current search type
            if(searchBy.value === 'status') {
                textSearch.classList.add('d-none');
                selectSearch.classList.remove('d-none');
            }
            
            // Handle search type change
            searchBy.addEventListener('change', function() {
                if(this.value === 'status') {
                    textSearch.classList.add('d-none');
                    selectSearch.classList.remove('d-none');
                    textSearch.value = '';
                } else {
                    textSearch.classList.remove('d-none');
                    selectSearch.classList.add('d-none');
                    selectSearch.value = '';
                }
            });

            // Print table functionality
            document.getElementById('printTableBtn').addEventListener('click', function() {
                const table = document.querySelector('.table-responsive').cloneNode(true);
                
                // Remove Actions column (9th column)
                const rows = table.querySelectorAll('tr');
                rows.forEach(row => {
                    const th = row.querySelector('th:nth-child(9)');
                    const td = row.querySelector('td:nth-child(9)');
                    if(th) th.remove();
                    if(td) td.remove();
                });
                
                const printWindow = window.open('', '', 'width=800,height=600');
                printWindow.document.write(`
                    <html>
                        <head>
                            <title>Liste des Stagiaires</title>
                            <style>
                                body { font-family: Arial; margin: 20px; }
                                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                                th { background-color: #f2f2f2; }
                                h2 { text-align: center; color: #dc3545; }
                                .print-date { text-align: right; margin-bottom: 20px; }
                            </style>
                        </head>
                        <body>
                            <h2>Liste des Stagiaires</h2>
                            <div class="print-date">Imprimé le: ${new Date().toLocaleDateString()}</div>
                `);
                printWindow.document.write(table.outerHTML);
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                
                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 500);
            });
        });
    </script>
</body>
</html>