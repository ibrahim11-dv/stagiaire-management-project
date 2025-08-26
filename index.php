<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plateforme Stagiaires - Wilaya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
                <img src="assets/logo-removebg.png" width="50" height="50" class="me-2">
                Wilaya Stages
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#"><i class="bi bi-house-door me-1"></i> Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services"><i class="bi bi-briefcase me-1"></i> Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#process"><i class="bi bi-list-check me-1"></i> Procédure</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php"><i class="bi bi-box-arrow-in-right me-1"></i> Connexion</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero section -->
    <div class="hero-section text-white" style="background: url('assets/wilayaOujda.jpg') no-repeat center center/cover">
        <div class="hero-overlay"></div>
        <div class="hero-content container py-5">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="display-4 fw-bold mb-4">Bienvenue à la Plateforme des Stagiaires</h1>
                    <p class="lead mb-5">
                        La Wilaya d'Oujda met à disposition cette plateforme numérique pour faciliter la gestion 
                        des stages administratifs au sein de ses différents services. Notre mission est d'offrir 
                        aux étudiants une expérience professionnelle enrichissante.
                    </p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="login.php" class="btn btn-danger btn-lg px-4">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Espace Administrateur
                        </a>
                        <a href="#contact" class="btn btn-outline-light btn-lg px-4">
                            <i class="bi bi-info-circle me-2"></i> Plus d'informations
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- À propos -->
    <section class="about-section" id="about">
        <div class="about-overlay"></div>
        <div class="about-content container">
            <div class="row align-items-center g-4">
                <div class="col-lg-6">
                    <div class="bg-white p-4 rounded shadow">
                        <img src="https://images.unsplash.com/photo-1521791136064-7986c2920216?ixlib=rb-4.0.3&auto=format&fit=crop&w=1469&q=80" 
                             alt="Bâtiment de la wilaya" class="img-fluid rounded shadow">
                    </div>
                </div>
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-4 display-5">À propos de notre programme de stages</h2>
                    <p class="lead text-dark mb-4">
                        La wilaya s'engage à offrir des opportunités de stage de qualité pour les étudiants 
                        de la région, couvrant divers domaines administratifs.
                    </p>
                    <div class="d-flex mb-4">
                        <div class="me-4 text-danger">
                            <i class="bi bi-check-circle-fill fs-1"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold">Encadrement professionnel</h3>
                            <p class="text-dark mb-0 fs-5">Chaque stagiaire est accompagné par un tuteur dédié.</p>
                        </div>
                    </div>
                    <div class="d-flex mb-4">
                        <div class="me-4 text-danger">
                            <i class="bi bi-check-circle-fill fs-1"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold">Diversité des services</h3>
                            <p class="text-dark mb-0 fs-5">Plus de 20 services administratifs disponibles.</p>
                        </div>
                    </div>
                    <div class="d-flex">
                        <div class="me-4 text-danger">
                            <i class="bi bi-check-circle-fill fs-1"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold">Reconnaissance académique</h3>
                            <p class="text-dark mb-0 fs-5">Convention de stage et attestation officielle.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services -->
    <section class="services-section" id="services">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold">Nos Services</h2>
                <p class="lead text-muted">Découvrez les différents services administratifs disponibles pour votre stage</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card service-card border-0 shadow-sm">
                        <div class="card-body text-center p-5">
                            <div class="service-icon bg-danger bg-opacity-10 text-danger rounded-circle">
                                <i class="bi bi-people-fill fs-1"></i>
                            </div>
                            <h3 class="card-title">Ressources Humaines</h3>
                            <p class="card-text text-muted">Gestion du personnel, carrières et formations.</p>
                            <a href="#" class="btn btn-outline-danger mt-3">En savoir plus</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card service-card border-0 shadow-sm">
                        <div class="card-body text-center p-5">
                            <div class="service-icon bg-danger bg-opacity-10 text-danger rounded-circle">
                                <i class="bi bi-cash-coin fs-1"></i>
                            </div>
                            <h3 class="card-title">Finances</h3>
                            <p class="card-text text-muted">Gestion budgétaire et comptabilité publique.</p>
                            <a href="#" class="btn btn-outline-danger mt-3">En savoir plus</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card service-card border-0 shadow-sm">
                        <div class="card-body text-center p-5">
                            <div class="service-icon bg-danger bg-opacity-10 text-danger rounded-circle">
                                <i class="bi bi-pc-display fs-1"></i>
                            </div>
                            <h3 class="card-title">Informatique</h3>
                            <p class="card-text text-muted">Gestion des systèmes informatiques et support technique.</p>
                            <a href="#" class="btn btn-outline-danger mt-3">En savoir plus</a>
                        </div>
                    </div>
                </div>
                <!-- Additional services -->
                <div class="col-md-4">
                    <div class="card service-card border-0 shadow-sm">
                        <div class="card-body text-center p-5">
                            <div class="service-icon bg-danger bg-opacity-10 text-danger rounded-circle">
                                <i class="bi bi-building fs-1"></i>
                            </div>
                            <h3 class="card-title">Urbanisme</h3>
                            <p class="card-text text-muted">Planification urbaine et aménagement du territoire.</p>
                            <a href="#" class="btn btn-outline-danger mt-3">En savoir plus</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card service-card border-0 shadow-sm">
                        <div class="card-body text-center p-5">
                            <div class="service-icon bg-danger bg-opacity-10 text-danger rounded-circle">
                                <i class="bi bi-recycle fs-1"></i>
                            </div>
                            <h3 class="card-title">Service Environnement</h3>
                            <p class="card-text text-muted">Gestion des déchets et protection de la nature.</p>
                            <a href="#" class="btn btn-outline-danger mt-3">En savoir plus</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card service-card border-0 shadow-sm">
                        <div class="card-body text-center p-5">
                            <div class="service-icon bg-danger bg-opacity-10 text-danger rounded-circle">
                                <i class="bi bi-book fs-1"></i>
                            </div>
                            <h3 class="card-title">Éducation</h3>
                            <p class="card-text text-muted">Gestion des établissements scolaires et programmes éducatifs.</p>
                            <a href="#" class="btn btn-outline-danger mt-3">En savoir plus</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Processus -->
    <section class="process-section" id="process">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-4 fw-bold text-white">Comment postuler ?</h2>
                <p class="lead text-white-50">Procédure simple et transparente pour votre stage</p>
            </div>
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card process-card h-100 border-0 bg-danger text-white">
                        <div class="card-body text-center p-4">
                            <div class="process-step bg-white text-danger rounded-circle fw-bold">1</div>
                            <h3>Préparer son dossier</h3>
                            <p class="small">CV, lettre de motivation, convention</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card process-card h-100 border-0 bg-white">
                        <div class="card-body text-center p-4">
                            <div class="process-step bg-danger text-white rounded-circle fw-bold">2</div>
                            <h3 class="text-dark">Dépôt de demande</h3>
                            <p class="small text-muted">Via la plateforme ou en personne</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card process-card h-100 border-0 bg-white">
                        <div class="card-body text-center p-4">
                            <div class="process-step bg-danger text-white rounded-circle fw-bold">3</div>
                            <h3 class="text-dark">Examen du dossier</h3>
                            <p class="small text-muted">Validation par le service</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card process-card h-100 border-0 bg-white">
                        <div class="card-body text-center p-4">
                            <div class="process-step bg-danger text-white rounded-circle fw-bold">4</div>
                            <h3 class="text-dark">Début du stage</h3>
                            <p class="small text-muted">Accueil et intégration</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5">
                <a href="postuler.php" class="btn btn-danger btn-lg px-5 py-3">Postuler maintenant</a>
            </div>
        </div>
    </section>

    <!-- Contact -->
    <section id="contact" class="py-5 bg-white">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="fw-bold mb-4">Contactez la cellule des stages</h2>
                    <p class="lead text-muted mb-5">
                        Pour toute question concernant le programme de stages de la wilaya
                    </p>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <i class="bi bi-geo-alt fs-2 text-danger mb-3"></i>
                                <h5>Adresse</h5>
                                <p class="small text-muted mb-0">Siège de la Wilaya, Oujda</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <i class="bi bi-telephone fs-2 text-danger mb-3"></i>
                                <h5>Téléphone</h5>
                                <p class="small text-muted mb-0">025 12 34 56</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <i class="bi bi-envelope fs-2 text-danger mb-3"></i>
                                <h5>Email</h5>
                                <p class="small text-muted mb-0">stages@wilaya-oujda.dz</p>
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