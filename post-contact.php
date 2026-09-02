<?php
session_start();

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader or require files manually if not using Composer
require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$errors = [];

// 1. HONEYPOT CHECK (Anti-Spam)
// If the hidden field 'website' is filled, it's likely a bot.
if (!empty($_POST['website'])) {
    // Silent fail: Redirect as if successful to fool the bot, but don't send email.
    $_SESSION['success'] = 1;
    header('Location: contact');
    exit();
}

// 2. INPUT VALIDATION
if (!isset($_POST['name']) || trim($_POST['name']) === '') {
    $errors['name'] = "Vous n'avez pas renseigné votre nom.";
}

if (!isset($_POST['email']) || trim($_POST['email']) === '' || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = "L'adresse email n'est pas valide.";
}

if (!isset($_POST['subject']) || trim($_POST['subject']) === '') {
    $errors['subject'] = "Vous n'avez pas renseigné le sujet.";
}

if (!isset($_POST['message']) || trim($_POST['message']) === '') {
    $errors['message'] = "Vous n'avez pas écrit de message.";
}

// 3. ERROR HANDLING OR PROCESSING
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['inputs'] = $_POST;
    header('Location: contact');
    exit();
} 

// 4. SANITIZATION & EMAIL SENDING
$name = trim($_POST['name']);
$email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
$subject = trim($_POST['subject']);
$message = trim($_POST['message']);

$mail = new PHPMailer(true);

try {
    //Server settings
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'mail.bowabancongo.com';                 //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'contact@bowabancongo.com';             //SMTP username
    $mail->Password   = 'Contact@bowaba';                       //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to
    $mail->CharSet    = 'UTF-8';                                //Encodage UTF-8 pour accents français
    $mail->Encoding   = 'base64';

    //Recipients
    $mail->setFrom('contact@bowabancongo.com', 'Contact Web');
    $mail->addAddress('contact@bowabancongo.com');     //Add a recipient
    $mail->addReplyTo($email, $name);

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = '[Contact Web] ' . $subject;
    
    // HTML Message Body
    $nameHtml    = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $emailHtml   = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $subjectHtml = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
    $messageHtml = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));

    $mail->Body    = "
        <h2>Nouveau message depuis le site web</h2>
        <p><strong>Nom:</strong> {$nameHtml}</p>
        <p><strong>Email:</strong> {$emailHtml}</p>
        <p><strong>Sujet:</strong> {$subjectHtml}</p>
        <p><strong>Message:</strong><br>{$messageHtml}</p>
        <br>
        <small>Ce message a été envoyé via le formulaire de contact de bowabancongo.com</small>
    ";
    
    // Plain Text Alt Body
    $mail->AltBody = "Nouveau message de {$name} ({$email})\n\nSujet: {$subject}\n\nMessage:\n{$message}";

    $mail->send();
    
    $_SESSION['success'] = 1;
    unset($_SESSION['inputs']);
    header('Location: contact');
    exit();
    
} catch (Exception $e) {
    error_log('[post-contact] PHPMailer error: ' . $mail->ErrorInfo);
    $_SESSION['errors'] = ["Une erreur technique est survenue lors de l'envoi du message. Veuillez réessayer plus tard."];
    $_SESSION['inputs'] = $_POST;
    header('Location: contact');
    exit();
}





