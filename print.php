<?php
include("config.php");
if(isset($_GET['id'])) {
    $stagiaireId = $_GET['id'];
    $sql_s = 'SELECT * FROM stagiaire WHERE id='.$stagiaireId;
    $result = mysqli_query($conDb,$sql_s);
    $stagiaireInfo = $result->fetch_assoc();
    
    $sql_e = 'SELECT * FROM encadrant WHERE id='.$stagiaireInfo['encadrant_id'];
    $result_e = mysqli_query($conDb,$sql_e);
    $encadrantInfo = $result_e->fetch_assoc();
    
    $sql_st = 'SELECT * FROM stage WHERE id='.$stagiaireInfo['stage_id'];
    $result_st = mysqli_query($conDb,$sql_st);
    $stageInfo = $result_st->fetch_assoc();
    
    $sql_sv = 'SELECT * FROM service WHERE id='.$encadrantInfo['service_id'];
    $result_sv = mysqli_query($conDb,$sql_sv);
    $serviceInfo = $result_sv->fetch_assoc();
    
    $dateDebut = new DateTime($stageInfo['date_debut']);
    $dateFin = new DateTime($stageInfo['date_fin']);
    $interval = $dateDebut->diff($dateFin);
    $dureeStage = $interval->format('%m mois et %d jours');
} else {
    echo '<script>alert("Erreur dans l\'importation des informations"); window.history.back();</script>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attestation de Stage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        :root {
            --primary-color: #d10000;
            --secondary-color: #333;
            --light-color: #f8f9fa;
        }
        body {
            font-family: 'Times New Roman', serif;
            line-height: 1.4;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .attestation-container {
            background-color: white;
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 0;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
        }
        .header {
            text-align: center;
            padding: 10px 0 5px 0;
            border-bottom: 2px solid var(--primary-color);
            margin-top: 0;
        }
        .header h2 {
            font-size: 22px;
            font-weight: bold;
            margin: 5px 0;
        }
        .content {
            padding: 10px 20px;
            font-size: 14px;
            margin-top: 0;
        }
        .info-box {
            background-color: var(--light-color);
            border-left: 3px solid var(--primary-color);
            padding: 12px;
            margin-bottom: 12px;
        }
        .info-item {
            margin-bottom: 4px;
            display: flex;
        }
        .info-label {
            font-weight: bold;
            min-width: 120px;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin: 12px 0;
            color: var(--primary-color);
        }
        ul {
            padding-left: 20px;
            margin-bottom: 8px;
        }
        li {
            margin-bottom: 4px;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
        }
        .signature-box {
            text-align: center;
            width: 30%;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 80%;
            margin: 8px auto;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            margin-top: 15px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
        }
        .action-buttons {
            text-align: center;
            margin: 15px auto;
        }
        .btn {
            margin: 0 5px;
            padding: 8px 15px;
        }

        @media print {
            body, html {
                margin: 0 !important;
                padding: 0 !important;
            }
            .attestation-container {
                box-shadow: none;
                border: none;
                margin: 0 auto;
                padding: 0;
                width: 100%;
                height: auto;
            }
            .action-buttons {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container py-2">
        <div class="attestation-container" id="attestation-content">
            <div class="header">
                <h2>ATTESTATION DE STAGE PROFESSIONNEL</h2>
                <p>Document Officiel</p>
            </div>

            <div class="content">
                <div class="info-box">
                    <div class="info-item">
                        <span class="info-label">Nom et Prénom :</span>
                        <span><?= $stagiaireInfo['nom'].' '.$stagiaireInfo['prenom'] ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">CIN/Passport :</span>
                        <span><?= $stagiaireInfo['cin'] ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Établissement :</span>
                        <span><?= $stagiaireInfo['etablissement'] ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Filière :</span>
                        <span><?= $stagiaireInfo['diplome'] ?></span>
                    </div>
                </div>

                <p>Je soussigné(e), <strong><?= $encadrantInfo['nom'].' '.$encadrantInfo['prenom'] ?></strong>, <?= $encadrantInfo['poste'] ?> au sein du service <strong><?= $serviceInfo['nom'] ?></strong>, atteste que :</p>

                <p>Monsieur/Madame <strong><?= $stagiaireInfo['nom'].' '.$stagiaireInfo['prenom'] ?></strong> a effectué un stage dans notre structure du <strong><?= date('d/m/Y', strtotime($stageInfo['date_debut'])) ?></strong> au <strong><?= date('d/m/Y', strtotime($stageInfo['date_fin'])) ?></strong>, soit une durée de <strong><?= $dureeStage ?></strong>.</p>

                <p>Ce stage avait pour objectifs :</p>
                <ul>
                    <li><?= $stageInfo['objectifs'] ?></li>
                    <li>Mise en pratique des connaissances théoriques</li>
                    <li>Développement des compétences professionnelles</li>
                </ul>

                <p>Pendant cette période, le(la) stagiaire a fait preuve de :</p>
                <ul>
                    <li>Professionalisme et sérieux</li>
                    <li>Capacité d'adaptation</li>
                    <li>Motivation constante</li>
                </ul>

                <p>Les résultats obtenus ont été tout à fait satisfaisants.</p>

                <p>La présente attestation est délivrée à l'intéressé(e) pour servir et valoir ce que de droit.</p>

                <div class="signature-section">
                    <div class="signature-box">
                        <p>Le Stagiaire</p>
                        <div class="signature-line"></div>
                        <p><strong><?= $stagiaireInfo['nom'].' '.$stagiaireInfo['prenom'] ?></strong></p>
                    </div>
                    <div class="signature-box">
                        <p>Fait à Oujda, le <?= date('d/m/Y') ?></p>
                        <p>L'Encadrant</p>
                        <div class="signature-line"></div>
                        <p><strong><?= $encadrantInfo['nom'].' '.$encadrantInfo['prenom'] ?></strong></p>
                        <p><?= $encadrantInfo['poste'] ?></p>
                    </div>
                    <div class="signature-box">
                        <p>Le Directeur</p>
                        <div class="signature-line"></div>
                        <p><strong>[Nom du Directeur]</strong></p>
                        <p>[Fonction]</p>
                    </div>
                </div>

                <div class="footer">
                    <p><strong>Entreprise/Organisme</strong> - Adresse - Téléphone - Email</p>
                </div>
            </div>
        </div>

        <div class="action-buttons">
            <button onclick="generatePDF()" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf"></i> Générer PDF
            </button>
            <button onclick="window.print()" class="btn btn-primary">
                <i class="bi bi-printer"></i> Imprimer
            </button>
            <button onclick="window.history.back()" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Retour
            </button>
        </div>
    </div>

    <script>
    function generatePDF() {
        // Optimisation pour éliminer l'espace blanc
        const originalStyles = [];
        document.querySelectorAll('style, link[rel="stylesheet"]').forEach(style => {
            originalStyles.push({
                element: style,
                disabled: style.disabled
            });
            style.disabled = true;
        });

        const element = document.getElementById('attestation-content');
        const opt = {
            margin: [2, 5, 5, 5], // Marges très réduites (top, right, bottom, left)
            filename: 'attestation_stage_<?= $stagiaireInfo["nom"] ?>_<?= $stagiaireInfo["prenom"] ?>.pdf',
            image: { type: 'jpeg', quality: 1 },
            html2canvas: { 
                scale: 2,
                scrollY: 0,
                windowHeight: element.scrollHeight,
                ignoreElements: (el) => el.classList.contains('action-buttons')
            },
            jsPDF: { 
                unit: 'mm', 
                format: 'a4',
                orientation: 'portrait'
            }
        };

        // Solution ultime pour éliminer l'espace blanc
        const originalOverflow = document.body.style.overflow;
        document.body.style.overflow = 'visible';
        document.body.style.padding = '0';
        document.body.style.margin = '0';

        html2pdf().set(opt).from(element).save().then(() => {
            // Restaure les styles originaux
            originalStyles.forEach(style => {
                style.element.disabled = style.disabled;
            });
            document.body.style.overflow = originalOverflow;
        });
    }
    </script>
</body>
</html>