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

// Get data for the chart
$chartData = [];
$chartQuery = "SELECT s.sujet AS name, COUNT(st.id) AS count 
               FROM stage s
               LEFT JOIN stagiaire st ON s.id = st.stage_id AND st.est_supp = false
               GROUP BY s.id";
$chartResult = mysqli_query($conDb, $chartQuery);

while ($row = $chartResult->fetch_assoc()) {
    $chartData[] = $row;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Plateforme Stagiaires</title>
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
        .card-counter {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
                        <a class="nav-link active" href="dashboard.php">
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
                    <h2 class="mb-0">Tableau de bord</h2>
                    <div class="d-flex align-items-center">
                        <span class="me-3"></span>
                        <img src="" class="rounded-circle" >
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card card-counter bg-primary text-white mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Stagiaires</h6>
                                        <h2 class="mb-0">
                                        <?php
                                        $sql="SELECT COUNT(*) AS total FROM stagiaire WHERE est_supp=false";
                                        $result=mysqli_query($conDb,$sql);
                                        $nmbrStagiaire=$result->fetch_assoc();
                                        echo $nmbrStagiaire['total'];
                                        ?>
                                        </h2>
                                    </div>
                                    <i class="bi bi-people fs-1 opacity-50"></i>
                                </div>
                                <div class="mt-2">
                                    <span class="badge bg-light text-primary">Actifs:
                                    <?php
                                        $sql="SELECT COUNT(*) AS total FROM stagiaire WHERE est_supp=false AND status='En cours' ";
                                        $result=mysqli_query($conDb,$sql);
                                        $nmbrStagiaire=$result->fetch_assoc();
                                        echo $nmbrStagiaire['total'];
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-counter bg-success text-white mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Encadrants</h6>
                                        <h2 class="mb-0">
                                        <?php
                                        $sql="SELECT COUNT(*) AS total FROM encadrant WHERE est_supp=false  ";
                                        $result=mysqli_query($conDb,$sql);
                                        $nmbrStagiaire=$result->fetch_assoc();
                                        echo $nmbrStagiaire['total'];
                                        ?>
                                        </h2>
                                    </div>
                                    <i class="bi bi-person-badge fs-1 opacity-50"></i>
                                </div>
                                <div class="mt-2">
                                    <span class="badge bg-light text-success">Actifs:
                                    <?php
                                        $sql="SELECT COUNT(*) AS total FROM encadrant WHERE est_supp=false AND status='En cours'  ";
                                        $result=mysqli_query($conDb,$sql);
                                        $nmbrStagiaire=$result->fetch_assoc();
                                        echo $nmbrStagiaire['total'];
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-counter bg-warning text-dark mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Stages en cours</h6>
                                        <h2 class="mb-0">
                                        <?php
                                        $sql="SELECT COUNT(*) AS total FROM stage  ";
                                        $result=mysqli_query($conDb,$sql);
                                        $nmbrStagiaire=$result->fetch_assoc();
                                        echo $nmbrStagiaire['total'];
                                        ?>
                                        </h2>
                                    </div>
                                    <i class="bi bi-briefcase fs-1 opacity-50"></i>
                                </div>
                                <div class="mt-2">
                                    <span class="badge bg-light text-warning">terminé :
                                    <?php
                                    $currentDate = date('Y-m-d');
                                    $sql = "SELECT COUNT(*) AS total FROM stage WHERE date_fin < '$currentDate'";
                                    $result = mysqli_query($conDb, $sql);
                                    $terminer=$result->fetch_assoc();
                                    echo $terminer['total'];
                                    ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card card-counter bg-danger text-white mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-title">Demandes</h6>
                                        <h2 class="mb-0">
                                            <?php
                                            $query = "SELECT COUNT(*) as demand_count FROM postule ";
                                            $result = $conDb->query($query);
                                            $row = $result->fetch_assoc();
                                            $demandCount = $row['demand_count'];
                                            echo $demandCount;
                                            ?>
                                        </h2>
                                    </div>
                                    <i class="bi bi-envelope fs-1 opacity-50"></i>
                                </div>
                                <div class="mt-2">
                                    <span class="badge bg-light text-danger">À traiter</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities and Stats -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Activités récentes</h5>
                            </div>
                            
                            <div class="card-body">
                                <div class="list-group">
                                    
                                    <?php
                                    $sql_event = 'SELECT * FROM activitie ORDER BY created_at DESC';
                                    $resultEvent = mysqli_query($conDb, $sql_event);
                                    $nmbrEvent = mysqli_num_rows($resultEvent);
                                    $limit=6;
                                    $i=0;
                                    while($row=$resultEvent->fetch_assoc()){
                                        if($i++>$limit)break;
                                        if($row['action']=='ajouté'){
                                            echo'<div class="list-group-item border-0">
                                                <div class="d-flex">
                                                    <i class="bi bi-person-plus text-success fs-4 me-3"></i>
                                                    <div>
                                                        <h6 class="mb-1">Nouveau '.$row['type_name'].'</h6>
                                                        <p class="mb-1 small text-muted">'.strtolower($row['name']).' a été ajouté dans la section '.$row['type_name'].'</p>
                                                        <small class="text-muted">'.$row['created_at'].'</small>
                                                    </div>
                                                </div>
                                            </div>';
                                        }

                                        if($row['action']=='supprimé'){
                                            echo'<div class="list-group-item border-0">
                                                <div class="d-flex">
                                                    <i class="bi bi-x-circle text-danger fs-4 me-3"></i>
                                                    <div>
                                                        <h6 class="mb-1">Suppression du '.$row['type_name'].'</h6>
                                                        <p class="mb-1 small text-muted">'.strtolower($row['name']).' a été supprimé de la section '.$row['type_name'].'</p>
                                                        <small class="text-muted">'.$row['created_at'].'</small>
                                                    </div>
                                                </div>
                                            </div>';
                                        }
                                        if($row['action']=='modifié'){
                                            echo'<div class="list-group-item border-0">
                                                <div class="d-flex">
                                                    <i class="bi bi-pencil-square text-info fs-4 me-3"></i>
                                                    <div>
                                                        <h6 class="mb-1">modification du '.$row['type_name'].'</h6>
                                                        <p class="mb-1 small text-muted">'.strtolower($row['name']).' a été modifier de la section '.$row['type_name'].'</p>
                                                        <small class="text-muted">'.$row['created_at'].'</small>
                                                    </div>
                                                </div>
                                            </div>';
                                        }


                                    }
                                    ?>

                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Répartition des stagiaires par stage</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="serviceChart" height="200"></canvas>
                                <div class="mt-3" id="chartLegend">
                                    <!-- Dynamic legend will be inserted here by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart initialization with dynamic data
        document.addEventListener('DOMContentLoaded', function() {
            const chartData = <?php echo json_encode($chartData); ?>;
            
            const labels = chartData.map(item => item.name);
            const data = chartData.map(item => parseInt(item.count));
            
            // Colors array - make sure you have enough colors
            const backgroundColors = [
                '#dc3545', '#fd7e14', '#20c997', 
                '#0d6efd', '#6f42c1', '#ffc107',
                '#17a2b8', '#28a745', '#6610f2',
                '#e83e8c', '#6c757d', '#343a40'
            ].slice(0, labels.length);
            
            // Create chart
            const ctx = document.getElementById('serviceChart').getContext('2d');
            const serviceChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: backgroundColors,
                        borderWidth: 0
                    }]
                },
                options: {
                    cutout: '70%',
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
            
            // Create custom legend
            const legendContainer = document.getElementById('chartLegend');
            let legendHTML = '';
            let total=0;
            chartData.forEach((item, index) => {
                const totalCount = data.reduce((a, b) => a + b, 0);
                const percentage = (totalCount > 0) ? (parseInt(item.count) / totalCount * 100).toFixed(1) : 0;
                legendHTML += `
                    <div class="d-flex justify-content-between mb-2">
                        <span><span class="legend-color" style="display:inline-block; width:12px; height:12px; background-color:${backgroundColors[index]}; margin-right:5px;"></span>${item.name}</span>
                        <span class="fw-bold">${percentage}%</span>
                    </div>
                `;

            });
            
            legendContainer.innerHTML = legendHTML;
        });
    </script>
</body>
</html>