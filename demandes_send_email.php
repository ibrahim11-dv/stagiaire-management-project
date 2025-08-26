<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: logout.php");
    exit();
}

require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';
require 'phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
include("config.php");

// Récupérer les données du formulaire
$to = $_POST['email'];
$subject = $_POST['subject'];
$message = $_POST['message'];
$candidate_id = intval($_POST['candidate_id']);

$mail = new PHPMailer(true);

try {
    // Paramètres du serveur SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Remplacez par votre serveur SMTP
    $mail->SMTPAuth = true;
    $mail->Username = 'brahimchahlafi273@gmail.com'; // Remplacez par votre email SMTP
    $mail->Password = 'ggnl wiwn sudl pczk'; // Remplacez par votre mot de passe SMTP
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Destinataires
    $mail->setFrom('brahimchahlafi273@gmail.com', 'Plateforme Stages');
    $mail->addAddress($to);

    // Contenu
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $message;

    $mail->send();
    $_SESSION['success_message'] = "Email envoyé avec succès au candidat.";
} catch (Exception $e) {
    $_SESSION['error_message'] = "Erreur lors de l'envoi de l'email: {$mail->ErrorInfo}";
}

header("Location: demande_details.php?id=" . $candidate_id);
exit();
?>