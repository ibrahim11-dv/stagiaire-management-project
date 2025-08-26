-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 19, 2025 at 05:25 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `stagiaires_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `activitie`
--

CREATE TABLE `activitie` (
  `id` int(11) NOT NULL,
  `type_name` enum('stagiaire','encadrant','stage') NOT NULL,
  `action` enum('ajouté','supprimé','modifié','postulé') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activitie`
--

INSERT INTO `activitie` (`id`, `type_name`, `action`, `created_at`, `name`) VALUES
(10, 'stagiaire', 'modifié', '2025-06-13 19:51:39', 'Maaroufi Anass'),
(11, 'stagiaire', 'ajouté', '2025-06-13 19:53:22', 'CHEHLAFI IBRAHIM'),
(12, 'stagiaire', 'supprimé', '2025-06-13 19:54:12', 'CHEHLAFI IBRAHIM'),
(13, 'encadrant', 'modifié', '2025-06-13 20:42:50', 'Benjelloun '),
(14, 'stagiaire', 'supprimé', '2025-06-15 15:32:23', 'El Amrani Aya'),
(15, 'stagiaire', 'supprimé', '2025-06-15 15:32:32', 'Azouzi Omar'),
(16, 'stagiaire', 'ajouté', '2025-06-15 15:35:05', 'mohamed serghini'),
(17, '', 'ajouté', '2025-06-16 11:10:09', 'gsddsgdsg dsgdsg'),
(18, 'stagiaire', 'modifié', '2025-06-16 11:19:13', 'El Amrani Mehdi'),
(19, 'stagiaire', 'modifié', '2025-06-17 23:58:01', 'Abbassi Leila'),
(20, '', 'ajouté', '2025-06-18 20:39:13', 'tst tst'),
(21, '', 'ajouté', '2025-06-18 20:41:41', 'ggggggggggggg gggggg'),
(22, '', 'ajouté', '2025-06-18 20:43:56', 'ggggggggggggg gggggg'),
(23, 'stagiaire', 'modifié', '2025-06-19 10:27:11', 'El Amrani Mehdi'),
(24, 'stagiaire', 'modifié', '2025-06-19 10:32:03', 'El Amrani Mehdi'),
(25, 'stagiaire', 'modifié', '2025-06-19 10:34:57', 'El Amrani Mehdi'),
(26, 'stagiaire', 'modifié', '2025-06-19 10:38:59', 'mohamed serghini'),
(27, 'stagiaire', 'modifié', '2025-06-19 10:39:35', 'Maaroufi Anass'),
(28, 'stagiaire', 'modifié', '2025-06-27 19:07:17', 'El Amrani Mehdi'),
(29, 'stagiaire', 'modifié', '2025-06-27 19:07:53', 'Mansouri Youssef'),
(30, 'stagiaire', 'modifié', '2025-06-27 19:44:05', 'El Amrani Mehdi'),
(31, 'stagiaire', 'modifié', '2025-06-30 11:21:05', 'El Amrani Mehdi'),
(32, 'stagiaire', 'modifié', '2025-07-03 01:55:35', 'El Amrani Mehdi');

-- --------------------------------------------------------

--
-- Table structure for table `encadrant`
--

CREATE TABLE `encadrant` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `poste` varchar(255) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `est_supp` tinyint(1) DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL,
  `sexe` enum('homme','femme') DEFAULT NULL,
  `status` enum('En cours','Terminé') DEFAULT 'En cours',
  `cin` varchar(255) DEFAULT NULL,
  `stage_id` int(11) DEFAULT NULL,
  `mot_de_passe` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `encadrant`
--

INSERT INTO `encadrant` (`id`, `nom`, `prenom`, `email`, `telephone`, `poste`, `service_id`, `est_supp`, `image_url`, `sexe`, `status`, `cin`, `stage_id`, `mot_de_passe`) VALUES
(1, 'El Amrani', 'Mohamed', 'm.elamrani@entreprise.ma', '0612345678', 'Chef de projet', 5, 0, 'images/tom and jerry GIF.gif', 'homme', 'En cours', 'Q123450', 4, NULL),
(2, 'Benjelloun', 'Youssef', 'y.benjelloun@entreprise.ma', '0623456789', 'Développeur senior', 2, 0, 'images/homme_default.jpg', 'homme', 'Terminé', 'W654321', 1, NULL),
(3, 'Alaoui', 'Fatima Zahra', 'fz.alaoui@entreprise.ma', '0634567890', 'Responsable marketing', 3, 0, NULL, 'femme', 'En cours', 'TY098765', 2, NULL),
(4, 'Belhaj', 'Karim', 'k.belhaj@entreprise.ma', '0645678901', 'Comptable', 4, 0, NULL, 'homme', 'En cours', 'G543216', 3, NULL),
(5, 'Cherkaoui', 'Leila', 'l.cherkaoui@entreprise.ma', '0656789012', 'Ingénieur R&D', 6, 0, NULL, 'femme', 'En cours', 'B623541', 3, NULL),
(6, 'Bouzidi', 'Omar', 'o.bouzidi@entreprise.ma', '0667890123', 'Responsable logistique', 7, 0, NULL, 'homme', 'En cours', 'K067895', 5, NULL),
(7, 'El Ouafi', 'Amina', 'a.elouafi@entreprise.ma', '0678901234', 'RH Manager', 1, 0, NULL, 'femme', 'En cours', 'AV625431', 5, NULL),
(10, 'Benali', 'Youssef', 'youssef.benali@example.com', '0654321987', 'Technicien', 3, 1, NULL, 'homme', '', 'CD654321', 2, 'youssef2024'),
(11, 'Khadiri', 'Amine', 'amine.khadiri@example.com', '0701020304', 'Chef de projet', 1, 1, NULL, 'homme', '', 'EF789012', 3, 'amine!secure');

-- --------------------------------------------------------

--
-- Table structure for table `postule`
--

CREATE TABLE `postule` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `cin` varchar(10) NOT NULL,
  `sexe` enum('homme','femme') DEFAULT 'homme',
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `mot_de_pass` varchar(255) NOT NULL,
  `diplome` varchar(255) DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `img_url` varchar(500) DEFAULT NULL,
  `cart_url` varchar(500) DEFAULT NULL,
  `cv_url` varchar(500) DEFAULT NULL,
  `lettre_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `etablissement` varchar(255) DEFAULT NULL,
  `type_id` int(255) DEFAULT NULL
) ;

--
-- Dumping data for table `postule`
--

INSERT INTO `postule` (`id`, `nom`, `prenom`, `cin`, `sexe`, `telephone`, `email`, `mot_de_pass`, `diplome`, `date_debut`, `date_fin`, `img_url`, `cart_url`, `cv_url`, `lettre_url`, `created_at`, `etablissement`, `type_id`) VALUES
(17, 'ggggggggggggg', 'gggggg', 'addaad', 'homme', 'adad', 'brahimchahlafi273@gmail.com', 'brahim123', 'as', '2025-06-18', '2025-12-17', 'uploads/images/img_addaad_1750275836.jpeg', 'uploads/cartes/carte_addaad_1750275836.pdf', 'uploads/cv/cv_addaad_1750275836.pdf', 'uploads/lettres/lettre_addaad_1750275836.pdf', '2025-06-18 19:43:56', 'as', 1);

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE `service` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `responsable` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service`
--

INSERT INTO `service` (`id`, `nom`, `responsable`, `description`) VALUES
(1, 'Ressources Humaines', 'Amina El Ouafi', 'Gestion des recrutements, contrats et formations'),
(2, 'Informatique', 'Youssef Benjelloun', 'Support technique et développement logiciel'),
(3, 'Marketing', 'Fatima Zahra Alaoui', 'Stratégie digitale et communication'),
(4, 'Comptabilité', 'Karim Belhaj', 'Gestion financière et paie'),
(5, 'Production', 'Hassan El Fassi', 'Fabrication et contrôle qualité'),
(6, 'Recherche & Développement', 'Leila Cherkaoui', 'Innovation et projets technologiques'),
(7, 'Logistique', 'Omar Bouzidi', 'Gestion des stocks et livraisons');

-- --------------------------------------------------------

--
-- Table structure for table `stage`
--

CREATE TABLE `stage` (
  `id` int(11) NOT NULL,
  `sujet` varchar(255) NOT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `objectifs` text DEFAULT NULL,
  `annee_universitaire` year(4) DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL,
  `est_supp` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stage`
--

INSERT INTO `stage` (`id`, `sujet`, `date_debut`, `date_fin`, `objectifs`, `annee_universitaire`, `type_id`, `est_supp`) VALUES
(1, 'Développement d\'une application web', '2025-06-05', '2025-07-06', 'Création d\'une plateforme de gestion de stages', '2025', 2, 0),
(2, 'Analyse de données marketing', '2025-05-01', '2025-08-31', 'Étude des tendances clients avec Python', '2025', 2, 0),
(3, 'Sécurité informatique', '2025-03-10', '2025-09-10', 'Audit de sécurité d\'un système d\'information', '2025', 3, 0),
(4, 'Gestion de projet agile', '2025-04-05', '2025-07-20', 'Mise en place d\'une méthodologie Scrum', '2025', 1, 0),
(5, 'Développement mobile', '2025-05-15', '2025-08-30', 'Création d\'une application Android', '2025', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `stagiaire`
--

CREATE TABLE `stagiaire` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `cin` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `mot_de_passe` varchar(255) DEFAULT NULL,
  `diplome` varchar(255) DEFAULT NULL,
  `etablissement` varchar(255) DEFAULT NULL,
  `sexe` enum('homme','femme','autre') NOT NULL DEFAULT 'homme',
  `stage_id` int(11) DEFAULT NULL,
  `encadrant_id` int(11) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `status` enum('En cours','terminé') NOT NULL DEFAULT 'En cours',
  `type_id` int(11) DEFAULT NULL,
  `image_url` varchar(1000) DEFAULT NULL,
  `est_supp` tinyint(1) NOT NULL DEFAULT 0,
  `signed_attestation_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stagiaire`
--

INSERT INTO `stagiaire` (`id`, `nom`, `prenom`, `cin`, `email`, `mot_de_passe`, `diplome`, `etablissement`, `sexe`, `stage_id`, `encadrant_id`, `telephone`, `status`, `type_id`, `image_url`, `est_supp`, `signed_attestation_url`) VALUES
(1, 'El Amrani', 'Mehdi', 'RF125623', 'mehdi.elamruani@gmail.com', 'MehdiPass123!', 'Bac Sciences Maths\'', 'Lycée Ibn Sina, Rabat', 'homme', 1, 1, '0612345678', 'terminé', 1, 'images/Dragon Ball 90S GIF by LnR motion (2).gif', 0, 'uploads/signed_attestations/attestation_1_1751044750.pdf'),
(2, 'sergami', 'Amina', 'CD789512', 'amina.benk@outlook.com', 'Amina@2024', 'Licence en Informatique', 'Université Mohammed V, Casablanca', 'femme', 2, 2, '0523456789', 'En cours', 3, NULL, 0, NULL),
(3, 'Mansouri', 'Youssef', 'EF345678', 'youssef.mansouri@hotmail.com', 'Youssef_456', 'Bac Technique', 'Lycée Technique, Fès', 'homme', 1, 1, '0623456781', 'En cours', 1, 'images/Tired Tom And Jerry GIF.gif', 0, NULL),
(4, 'Rahmouni', 'Leila', 'GH901234', 'leila.rahmouni@gmail.com', 'LeilaRah2024', 'Master en Marketing', 'ENCG, Marrakech', 'femme', 3, 3, '0611223344', 'En cours', 3, NULL, 0, NULL),
(5, 'Khalfi', 'Karim', 'IJ567890', 'karim.khalfi@mail.com', 'KarimK789!', 'Bac Lettres Modernes', 'Lycée Descartes, Tanger', 'homme', 3, 7, '0677889900', 'En cours', 3, NULL, 0, NULL),
(6, 'el amrani', 'ahmed', 'AB123456', 'ahmed.elamrani@gmail.com', 'Ahmed123!', 'Baccalauréat Sciences Physiques', 'Lycée Mohammed V, Casablanca', 'homme', 1, 1, '0612345674', 'En cours', 1, 'images/homme_default.jpg', 0, NULL),
(7, 'benali', 'fatima', 'CD789012', 'fatima.benali@outlook.com', 'Fatima@2024', 'Licence en Informatique', 'Université Hassan II, Rabat', 'femme', 2, 2, '0523456789', 'terminé', 2, 'images/femme_default.jpg', 0, NULL),
(8, 'chehlafi', 'ibrahim', 'F771509', 'ibrahim.chehlafi.07@gmail.com', 'brahim123', 'conception et Developpement des logiciels', 'école Superieure de technologie', 'homme', 4, 2, '0669462131', 'En cours', 2, NULL, 1, NULL),
(9, 'Haddou', 'Mohamed', 'ZZ123456', 'mohamed.haddou@gmail.com', 'MohamedH@2024', 'Licence en Informatique', 'Université Ibn Zohr, Agadir', 'homme', 1, 2, '0612345679', 'En cours', 1, NULL, 0, NULL),
(10, 'el kouchi', 'youssef', 'AB151362', 'youssef.elkouchi@gmail.com', 'Exemple@2024', 'informatique embarqué', 'École Nationale des Sciences Appliquées (ENSA) - Agadir', 'homme', 4, 2, '0612345132', 'En cours', 5, NULL, 1, NULL),
(11, 'abdesamad', 'mazyan', 'U981245', 'abdesamad.mazyan.12@gmail.com', 'abdoabdo', 'Licence en englais', 'esefo', 'homme', 2, 7, '0687123499', 'En cours', 2, NULL, 0, NULL),
(12, 'Alaoui', 'Mohamed', 'AA987654', 'mohamed.alaoui@gmail.com', 'Mohamed@2024', 'Licence Mathématiques', 'Université Cadi Ayyad, Marrakech', 'homme', 1, 1, '0611122233', 'En cours', 1, NULL, 0, NULL),
(13, 'Bennani', 'Sara', 'BB123789', 'sara.bennani@outlook.com', 'Sara#2024', 'Master en Gestion', 'Université Hassan II, Casablanca', 'femme', 3, 3, '0622233344', 'En cours', 3, NULL, 0, NULL),
(14, 'Naciri', 'Youssef', 'CC456123', 'youssef.naciri@gmail.com', 'Youssef_456', 'Bac Sciences Physiques', 'Lycée Allal El Fassi, Rabat', 'homme', 2, 1, '0633344455', 'En cours', 2, NULL, 0, NULL),
(15, 'Abbassi', 'Leila', 'DD789321', 'leila.abbassi@gmail.com', 'Leila2024', 'Licence en Biologie', 'Université Ibn Tofail, Kénitra', 'femme', 1, 1, '0644455566', 'En cours', 1, 'images/femme_default.jpg', 0, NULL),
(16, 'El Idrissi', 'Hamza', 'EE321654', 'hamza.elidrissi@gmail.com', 'Hamza@789', 'DUT Informatique', 'EST Salé', 'homme', 2, 2, '0655566677', 'En cours', 2, NULL, 0, NULL),
(17, 'Tazi', 'Imane', 'FF654987', 'imane.tazi@hotmail.com', 'Imane2024', 'Licence en Droit', 'Université Mohammed V, Rabat', 'femme', 3, 7, '0666677788', 'En cours', 3, NULL, 0, NULL),
(18, 'Rhani', 'Omar', 'GG987321', 'omar.rhani@gmail.com', 'OmarRhani@2024', 'Bac Technique', 'Lycée Technique, Meknès', 'homme', 1, 1, '0677788899', 'En cours', 1, NULL, 0, NULL),
(22, 'El Idrissi', 'Mohamed', 'AA123456', 'mohamed.idrissi@gmail.com', 'Momo@2024', 'Bac Sciences Maths', 'Lycée Al Khawarizmi, Casablanca', 'homme', 1, 1, '0611223344', 'En cours', 1, NULL, 0, NULL),
(23, 'Bennis', 'Salma', 'BB654321', 'salma.bennis@gmail.com', 'Salma@2024', 'Licence en Informatique', 'Université Hassan II, Casablanca', 'femme', 1, 2, '0619876543', 'En cours', 2, NULL, 0, NULL),
(24, 'Tazi', 'Youssef', 'CC789012', 'youssef.tazi@hotmail.com', 'Youssef2024', 'DUT Informatique', 'EST Casablanca', 'homme', 2, 3, '0678123456', 'En cours', 3, NULL, 0, NULL),
(25, 'Amrani', 'Ikram', 'DD456789', 'ikram.amrani@gmail.com', 'Ikram@2024', 'Master en Marketing', 'ENCG Kénitra', 'femme', 1, 1, '0633214567', 'En cours', 1, NULL, 0, NULL),
(26, 'Fakir', 'Oussama', 'EE147852', 'ous.fakir@gmail.com', 'Fakir@123', 'Bac Technique', 'Lycée Technique, Tétouan', 'homme', 1, 2, '0623124578', 'En cours', 4, NULL, 0, NULL),
(27, 'Lachgar', 'Sanae', 'FF321789', 'sanae.lachgar@gmail.com', 'Sanae@2024', 'Licence Mathématiques', 'Université Ibn Tofail, Kénitra', 'femme', 2, 3, '0687123498', 'En cours', 5, NULL, 0, NULL),
(28, 'Ait Taleb', 'Hamza', 'GG951357', 'hamza.a.taleb@gmail.com', 'HamzaTaleb@2024', 'DUT Réseaux et Télécoms', 'EST Agadir', 'homme', 2, 1, '0601237890', 'En cours', 2, NULL, 0, NULL),
(29, 'El Fassi', 'Nada', 'HH789456', 'nada.elfassi@gmail.com', 'Nada@2024', 'Licence en Biologie', 'Université Mohammed V, Rabat', 'femme', 1, 2, '0654321789', 'En cours', 1, NULL, 0, NULL),
(30, 'Boulahfa', 'Yassine', 'II147852', 'yassine.boulahfa@gmail.com', 'Yassine2024', 'Master Informatique', 'Université Chouaib Doukkali, El Jadida', 'homme', 1, 1, '0698123456', 'En cours', 1, NULL, 0, NULL),
(31, 'Zahidi', 'Meriem', 'JJ963852', 'meriem.zahidi@gmail.com', 'Meriem@2024', 'Licence en Gestion', 'Université Ibn Tofail, Kénitra', 'femme', 2, 2, '0678123590', 'En cours', 2, NULL, 0, NULL),
(32, 'Berrada', 'Amine', 'KK741852', 'amine.berrada@gmail.com', 'Amine2024', 'Bac Lettres Modernes', 'Lycée Qualifiant, Marrakech', 'homme', 1, 1, '0687451230', 'En cours', 3, NULL, 0, NULL),
(33, 'Jebari', 'Asmae', 'LL258963', 'asmae.jebari@gmail.com', 'Asmae@2024', 'Licence en Physique', 'Université Ibn Zohr, Agadir', 'femme', 2, 3, '0674125987', 'En cours', 2, NULL, 0, NULL),
(34, 'Kharbouch', 'Hicham', 'MM753159', 'hicham.kharbouch@gmail.com', 'Hicham@2024', 'Master en Finance', 'ENCG Casablanca', 'homme', 1, 1, '0697539512', 'En cours', 4, NULL, 0, NULL),
(35, 'Naciri', 'Fatima', 'NN456321', 'fatima.naciri@gmail.com', 'Fatima@2024', 'Bac Sciences Physiques', 'Lycée Ibn Al Banna, Fès', 'femme', 2, 2, '0647891320', 'En cours', 5, NULL, 0, NULL),
(36, 'Alaoui', 'Imane', 'YY852963', 'imane.alaoui@gmail.com', 'Imane@2024', 'Licence en Chimie', 'Université Mohammed I, Oujda', 'femme', 2, 3, '0678123451', 'En cours', 1, NULL, 0, NULL),
(37, 'Maaroufi', 'Anass', 'ZZ963741', 'anass.maaroufi@gmail.com', 'Anass@2024', 'Bac Sciences Maths', 'Lycée Technique, Meknès', 'homme', 1, 1, '0612348756', 'En cours', 3, 'images/Théâtre_dOpéra_Spatial.png', 0, NULL),
(38, 'ibrahim', 'test', 'H657438', 'brahimchehlafi.concours@gmail.com', 'ibrahim', 'doctorat', 'salta3 burger', 'homme', 2, 6, '0669462131', 'En cours', 1, 'images/2d1ee07e-78a4-43c5-accc-a10b2f5d32f4.jpeg', 1, NULL),
(40, 'sgsgsd', 'gdsgdsgsd', 'H6574434', 'brahimchehlafi.concours@gmail.com', 'brbrbr', 'doctorat', 'salta3 burger', 'homme', 2, 6, '0669462131', 'En cours', 3, 'images/.jpeg', 1, NULL),
(41, 'test', 'test1', 'G121453', 'test.test1@gmail.com', 'brahim123', 'conception et Developpement des logiciels', 'ecole Superieure de technologie', 'homme', 5, 6, '0612345678', 'En cours', 1, '', 1, NULL),
(42, 'CHEHLAFI', 'IBRAHIM', 'G41243523', 'brahimchehlafi.concours@gmail.com', 'NIIGGGA', 'conception et Developpement des logiciels', 'salta3 burger', 'homme', 4, 5, '0669462131', 'En cours', 4, '', 1, NULL),
(43, 'Bennani', 'Youssef', 'AB12345', 'youssef.bennani@example.com', 'Youssef@2024', 'Licence Info', 'Université Hassan II', 'homme', 4, 1, '0612345000', 'En cours', 1, NULL, 0, NULL),
(44, 'El Khalfi', 'Karim', 'AB12346', 'karim.khalfi@example.com', 'Karim@2024', 'Licence Info', 'Université Hassan II', 'homme', 4, 2, '0612345001', 'En cours', 1, NULL, 0, NULL),
(45, 'El Amrani', 'Mohamed', 'AB12347', 'mohamed.elamrani@example.com', 'Mohamed@2024', 'Master Marketing', 'ENCG Casablanca', 'homme', 4, 3, '0612345002', 'En cours', 1, NULL, 0, NULL),
(46, 'Benjelloun', 'Amine', 'AB12348', 'amine.benjelloun@example.com', 'Amine@2024', 'Master Info', 'Université Hassan I', 'homme', 4, 4, '0612345003', 'En cours', 1, NULL, 0, NULL),
(47, 'Rahmouni', 'Leila', 'AB12349', 'leila.rahmouni@example.com', 'Leila@2024', 'Licence Anglais', 'Université Mohammed V', 'femme', 4, 5, '0612345004', 'En cours', 1, NULL, 0, NULL),
(48, 'Belhaj', 'Omar', 'AB12350', 'omar.belhaj@example.com', 'Omar@2024', 'Licence Economie', 'Université Ibn Zohr', 'homme', 4, 1, '0612345005', 'En cours', 1, NULL, 0, NULL),
(49, 'Ouafi', 'Fatima', 'AB12351', 'fatima.ouafi@example.com', 'Fatima@2024', 'Master RH', 'Université Hassan II', 'femme', 4, 2, '0612345006', 'En cours', 1, NULL, 0, NULL),
(50, 'Cherkaoui', 'Leila', 'AB12352', 'leila.cherkaoui@example.com', 'Leila@2024', 'Master R&D', 'Université Hassan I', 'femme', 4, 3, '0612345007', 'En cours', 1, NULL, 0, NULL),
(51, 'Bouzidi', 'Omar', 'AB12353', 'omar.bouzidi@example.com', 'Omar@2024', 'Licence Logistique', 'Université Cadi Ayyad', 'homme', 4, 4, '0612345008', 'En cours', 1, NULL, 0, NULL),
(52, 'Khadiri', 'Amine', 'AB12354', 'amine.khadiri@example.com', 'Amine@2024', 'Licence Maths', 'Université Ibn Zohr', 'homme', 4, 5, '0612345009', 'En cours', 1, NULL, 0, NULL),
(53, 'Azouzi', 'Aya', 'AB12355', 'aya.azouzi@example.com', 'Aya@2024', 'Licence Physique', 'Université Hassan II', 'femme', 4, 1, '0612345010', 'En cours', 1, NULL, 0, NULL),
(54, 'El Malki', 'Youssef', 'AB12356', 'youssef.elmalki@example.com', 'Youssef@2024', 'Licence Info', 'Université Mohammed V', 'homme', 4, 2, '0612345011', 'En cours', 1, NULL, 0, NULL),
(55, 'Ait Benali', 'Karim', 'AB12357', 'karim.aitbenali@example.com', 'Karim@2024', 'Licence Maths', 'Université Hassan II', 'homme', 4, 3, '0612345012', 'En cours', 1, NULL, 0, NULL),
(56, 'Bennouna', 'Fatima', 'AB12358', 'fatima.bennouna@example.com', 'Fatima@2024', 'Licence RH', 'Université Cadi Ayyad', 'femme', 4, 4, '0612345013', 'En cours', 1, NULL, 0, NULL),
(57, 'Zahraoui', 'Leila', 'AB12359', 'leila.zahraoui@example.com', 'Leila@2024', 'Master Info', 'Université Hassan II', 'femme', 4, 5, '0612345014', 'En cours', 1, NULL, 0, NULL),
(58, 'El Yousfi', 'Karim', 'AC12345', 'karim.elyousfi@example.com', 'Karim@2024', 'Licence Info', 'Université Ibn Zohr', 'homme', 5, 1, '0612345015', 'En cours', 1, NULL, 0, NULL),
(59, 'Azami', 'Youssef', 'AC12346', 'youssef.azami@example.com', 'Youssef@2024', 'Master RH', 'Université Hassan I', 'homme', 5, 2, '0612345016', 'En cours', 1, NULL, 0, NULL),
(60, 'Bakkali', 'Leila', 'AC12347', 'leila.bakkali@example.com', 'Leila@2024', 'Licence Logistique', 'Université Mohammed V', 'femme', 5, 3, '0612345017', 'En cours', 1, NULL, 0, NULL),
(61, 'Bouzidi', 'Amine', 'AC12348', 'amine.bouzidi@example.com', 'Amine@2024', 'Licence Maths', 'Université Cadi Ayyad', 'homme', 5, 4, '0612345018', 'En cours', 1, NULL, 0, NULL),
(62, 'Cherkaoui', 'Aya', 'AC12349', 'aya.cherkaoui@example.com', 'Aya@2024', 'Licence Physique', 'Université Hassan II', 'femme', 5, 5, '0612345019', 'En cours', 1, NULL, 0, NULL),
(63, 'El Malki', 'Omar', 'AC12350', 'omar.elmalki@example.com', 'Omar@2024', 'Licence RH', 'Université Hassan II', 'homme', 5, 1, '0612345020', 'En cours', 1, NULL, 0, NULL),
(64, 'Ait Benali', 'Fatima', 'AC12351', 'fatima.aitbenali@example.com', 'Fatima@2024', 'Master Info', 'Université Cadi Ayyad', 'femme', 5, 2, '0612345021', 'En cours', 1, NULL, 0, NULL),
(65, 'Bennouna', 'Karim', 'AC12352', 'karim.bennouna@example.com', 'Karim@2024', 'Licence Logistique', 'Université Hassan II', 'homme', 5, 3, '0612345022', 'En cours', 1, NULL, 0, NULL),
(66, 'Zahraoui', 'Amine', 'AC12353', 'amine.zahraoui@example.com', 'Amine@2024', 'Licence Maths', 'Université Ibn Zohr', 'homme', 5, 4, '0612345023', 'En cours', 1, NULL, 0, NULL),
(67, 'El Ouafi', 'Leila', 'AC12354', 'leila.elouafi@example.com', 'Leila@2024', 'Licence Info', 'Université Hassan II', 'femme', 5, 5, '0612345024', 'En cours', 1, NULL, 0, NULL),
(68, 'Benali', 'Aya', 'AC12355', 'aya.benali@example.com', 'Aya@2024', 'Master RH', 'Université Hassan I', 'femme', 5, 1, '0612345025', 'En cours', 1, NULL, 0, NULL),
(69, 'Bouzidi', 'Youssef', 'AC12356', 'youssef.bouzidi@example.com', 'Youssef@2024', 'Licence Logistique', 'Université Mohammed V', 'homme', 5, 2, '0612345026', 'En cours', 1, NULL, 0, NULL),
(70, 'Cherkaoui', 'Amine', 'AC12357', 'amine.cherkaoui@example.com', 'Amine@2024', 'Licence Maths', 'Université Ibn Zohr', 'homme', 5, 3, '0612345027', 'En cours', 1, NULL, 0, NULL),
(71, 'El Amrani', 'Aya', 'AC12358', 'aya.elamrani@example.com', 'Aya@2024', 'Licence Physique', 'Université Hassan II', 'femme', 5, 4, '0612345028', 'En cours', 1, NULL, 1, NULL),
(72, 'Azouzi', 'Omar', 'AC12359', 'omar.azouzi@example.com', 'Omar@2024', 'Licence RH', 'Université Hassan II', 'homme', 5, 5, '0612345029', 'En cours', 1, NULL, 1, NULL),
(73, 'mohamed', 'serghini', 'tttt', 'mehdi.elamruani@gmail.com', 'qq', 'tttttt', 'tttttttt', 'homme', 4, 1, '0612345678', 'En cours', 1, 'images/Tired Tom And Jerry GIF.gif', 0, NULL),
(74, 'Ali', 'Bennani', 'AA12345', 'ali.bennani@example.com', '0', 'Licence', 'Université Mohammed V de Rabat', 'homme', 2, 1, '0612345678', 'En cours', 1, '0', 0, NULL),
(77, 'Youssef', 'Alaoui', 'EE56789', 'youssef.alaoui@example.com', '$2y$10$rL1TOxP0BfXRTjYWa.RI3.mD3zpr9t0vTsjYBihVU1kNNI0m3jyh.', 'Licence', 'Université Abdelmalek Essaâdi de Tétouan', 'homme', 4, 2, '0656789012', 'En cours', 5, '', 0, NULL),
(78, 'Nada', 'Fikri', 'FF67890', 'nada.fikri@example.com', '$2y$10$5K0rr0qeCE2VvWnroD08l.4z1j31UIlbXUxNzsJG6bcWAQ5AtkCfy', 'Master', 'Université Moulay Ismaïl de Meknès', 'femme', 1, 4, '0667890123', 'En cours', 1, '', 0, NULL),
(79, 'Hamza', 'Moussaoui', 'GG78901', 'hamza.moussaoui@example.com', '$2y$10$PUEujY3W68pesSNbUohWhemLQJNZ.cpRSk/xBgx6UKB812SC3OshS', 'Licence', 'Université Sidi Mohammed Ben Abdellah de Fès', 'homme', 2, 2, '0678901234', 'En cours', 2, '', 0, NULL),
(80, 'Khadija', 'Naciri', 'HH89012', 'khadija.naciri@example.com', '$2y$10$3Y3s.t7TsMd5OJrSwGUMxuDxHdilOGZRrl/V.wVoRYQczI3E/ZV6a', 'Master', 'Université Sultan Moulay Slimane de Beni Mellal', 'femme', 3, 1, '0689012345', 'En cours', 3, '', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `type_stage`
--

CREATE TABLE `type_stage` (
  `id` int(11) NOT NULL,
  `intitule` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `type_stage`
--

INSERT INTO `type_stage` (`id`, `intitule`) VALUES
(1, 'PFE'),
(2, 'Initiation'),
(3, 'Perfectionnement'),
(4, 'Ouvrier'),
(5, 'Technicien');

-- --------------------------------------------------------

--
-- Table structure for table `type_user`
--

CREATE TABLE `type_user` (
  `id` int(11) NOT NULL,
  `nom` enum('stagiaire','admin','candidat','encadrant') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `type_user`
--

INSERT INTO `type_user` (`id`, `nom`) VALUES
(1, 'admin'),
(2, 'stagiaire'),
(3, 'candidat'),
(4, 'encadrant');

-- --------------------------------------------------------

--
-- Table structure for table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `prenom` varchar(50) DEFAULT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `cin` varchar(10) NOT NULL,
  `identifiant` varchar(10) DEFAULT NULL,
  `postule_id` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `mot_de_passe`, `email`, `prenom`, `nom`, `telephone`, `photo`, `cin`, `identifiant`, `postule_id`, `type`) VALUES
(1, 'brahim123', 'ibrahim.chehlafi.07@gmail.com', 'ibrahim', 'chehlafi', '0669462131', 'images/7fe73312-81d5-454e-8250-f0e53b35a76f.jpeg', 'asas12', 'qq', NULL, 1),
(2, 's909', 'souadzaidi67@gmail.com', 'souad', 'zaidi', '08738389', NULL, '1fg213', NULL, NULL, 1),
(5, 'brahim123', 'brahimchahlafi273@gmail.com', 'gggggg', 'ggggggggggggg', 'adad', 'uploads/images/img_addaad_1750275836.jpeg', 'addaad', 'gggggggggg', 17, 3),
(6, 'aaa', 'aaa', 'aaa', 'aaa', 'aaa', NULL, 'aaa', 'aaaa', 1, 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activitie`
--
ALTER TABLE `activitie`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `encadrant`
--
ALTER TABLE `encadrant`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `stage_id` (`stage_id`);

--
-- Indexes for table `postule`
--
ALTER TABLE `postule`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cin` (`cin`),
  ADD KEY `type_id` (`type_id`);

--
-- Indexes for table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stage`
--
ALTER TABLE `stage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type_id` (`type_id`);

--
-- Indexes for table `stagiaire`
--
ALTER TABLE `stagiaire`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cin` (`cin`),
  ADD KEY `stage_id` (`stage_id`),
  ADD KEY `encadrant_id` (`encadrant_id`),
  ADD KEY `fk_stagiaire_type_stage` (`type_id`);

--
-- Indexes for table `type_stage`
--
ALTER TABLE `type_stage`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `type_user`
--
ALTER TABLE `type_user`
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cin` (`cin`),
  ADD UNIQUE KEY `postule_id` (`postule_id`),
  ADD UNIQUE KEY `postule_id_2` (`postule_id`),
  ADD KEY `type` (`type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activitie`
--
ALTER TABLE `activitie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `encadrant`
--
ALTER TABLE `encadrant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `postule`
--
ALTER TABLE `postule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `service`
--
ALTER TABLE `service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `stage`
--
ALTER TABLE `stage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `stagiaire`
--
ALTER TABLE `stagiaire`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `type_stage`
--
ALTER TABLE `type_stage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `type_user`
--
ALTER TABLE `type_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `encadrant`
--
ALTER TABLE `encadrant`
  ADD CONSTRAINT `encadrant_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`),
  ADD CONSTRAINT `encadrant_ibfk_2` FOREIGN KEY (`stage_id`) REFERENCES `stage` (`id`);

--
-- Constraints for table `postule`
--
ALTER TABLE `postule`
  ADD CONSTRAINT `postule_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `type_stage` (`id`);

--
-- Constraints for table `stage`
--
ALTER TABLE `stage`
  ADD CONSTRAINT `stage_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `type_stage` (`id`);

--
-- Constraints for table `stagiaire`
--
ALTER TABLE `stagiaire`
  ADD CONSTRAINT `fk_stagiaire_type_stage` FOREIGN KEY (`type_id`) REFERENCES `type_stage` (`id`),
  ADD CONSTRAINT `stagiaire_ibfk_1` FOREIGN KEY (`stage_id`) REFERENCES `stage` (`id`),
  ADD CONSTRAINT `stagiaire_ibfk_2` FOREIGN KEY (`encadrant_id`) REFERENCES `encadrant` (`id`);

--
-- Constraints for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD CONSTRAINT `utilisateurs_ibfk_1` FOREIGN KEY (`type`) REFERENCES `type_user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
