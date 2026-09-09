<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;

// Redirection directe si la page est accédée en GET
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: contact');
    exit();
}

// Chargement de la factory SMTP centralisée (lit les secrets depuis .env)
require_once __DIR__ . '/kon/mailer.php';

// 1. HONEYPOT CHECK (Anti-Spam)
// Si le champ masqué 'website' est rempli, c'est probablement un robot.
if (!empty($_POST['website'])) {
    // Échec silencieux : redirection simulant le succès pour dérouter le bot
    $_SESSION['success'] = 1;
    header('Location: contact');
    exit();
}

// 2. VALIDATION DES ENTRÉES
$errors = [];

$name    = isset($_POST['name']) ? trim((string)$_POST['name']) : '';
$email   = isset($_POST['email']) ? trim((string)$_POST['email']) : '';
$subject = isset($_POST['subject']) ? trim((string)$_POST['subject']) : '';
$message = isset($_POST['message']) ? trim((string)$_POST['message']) : '';

if ($name === '') {
    $errors['name'] = "Vous n'avez pas renseigné votre nom.";
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = "L'adresse email n'est pas valide.";
}

if ($subject === '') {
    $errors['subject'] = "Vous n'avez pas renseigné le sujet.";
}

if ($message === '') {
    $errors['message'] = "Vous n'avez pas écrit de message.";
}

// 3. GESTION DES ERREURS DE VALIDATION
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['inputs'] = $_POST;
    header('Location: contact');
    exit();
}

// 4. PRÉPARATION ET ENVOI DE L'EMAIL (Sécurisé dans try/catch global)
$mail = null;
try {
    // Création de l'instance PHPMailer configurée depuis .env
    $mail = createMailer('main');

    // Destinataire et Reply-To
    $recipient = env('CONTACT_RECIPIENT', env('SMTP_FROM', 'contact@bowabancongo.com'));
    $mail->addAddress($recipient);
    $mail->addReplyTo($email, $name);

    // Format HTML & Sujet
    $mail->isHTML(true);
    $mail->Subject = '[Contact Web] ' . $subject;

    // Protection XSS dans le template HTML
    $nameHtml    = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $emailHtml   = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $subjectHtml = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
    $messageHtml = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));

    $mail->Body = "
        <div style=\"font-family: Arial, sans-serif; font-size: 15px; color: #333; line-height: 1.6;\">
            <h2 style=\"color: #0b2341; border-bottom: 2px solid #ff7a00; padding-bottom: 8px;\">Nouveau message depuis le site web</h2>
            <p><strong>Nom :</strong> {$nameHtml}</p>
            <p><strong>Email :</strong> <a href=\"mailto:{$emailHtml}\">{$emailHtml}</a></p>
            <p><strong>Sujet :</strong> {$subjectHtml}</p>
            <p><strong>Message :</strong></p>
            <div style=\"background: #f8f9fa; border-left: 4px solid #ff7a00; padding: 12px 16px; margin: 12px 0;\">
                {$messageHtml}
            </div>
            <br>
            <small style=\"color: #888;\">Ce message a été envoyé via le formulaire de contact de <a href=\"https://bowabancongo.com\">bowabancongo.com</a></small>
        </div>
    ";

    // Version texte brut
    $mail->AltBody = "Nouveau message depuis bowabancongo.com\n\nNom: {$name}\nEmail: {$email}\nSujet: {$subject}\n\nMessage:\n{$message}";

    // Envoi
    $mail->send();

    $_SESSION['success'] = 1;
    unset($_SESSION['inputs']);
    header('Location: contact');
    exit();

} catch (\Throwable $e) {
    // Journalisation détaillée sans crash HTTP 500
    error_log('[post-contact] Erreur lors de l\'envoi : ' . $e->getMessage());
    if ($mail instanceof PHPMailer && !empty($mail->ErrorInfo)) {
        error_log('[post-contact] PHPMailer ErrorInfo : ' . $mail->ErrorInfo);
    }

    $techError = '';
    if (env('APP_DEBUG') === 'true' || env('APP_ENV') === 'local') {
        $detail = $e->getMessage();
        if ($mail instanceof PHPMailer && !empty($mail->ErrorInfo) && $mail->ErrorInfo !== $detail) {
            $detail .= ' (' . $mail->ErrorInfo . ')';
        }
        $techError = '<br><small class="fw-bold">[Diagnostic : ' . htmlspecialchars($detail, ENT_QUOTES, 'UTF-8') . ']</small>';
    }

    $_SESSION['errors'] = ["Une erreur technique est survenue lors de l'envoi de votre message. Veuillez réessayer ultérieurement ou nous contacter directement par téléphone au +243 816 695 000." . $techError];
    $_SESSION['inputs'] = $_POST;
    header('Location: contact');
    exit();
}
