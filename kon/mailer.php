<?php
/**
 * mailer.php — Factory PHPMailer centralisée
 * ────────────────────────────────────────────
 * Fournit createMailer(string $profile): PHPMailer
 *
 * Profils disponibles :
 *   'main'      → SMTP bowabancongo.com      (formulaire contact principal)
 *   'fondation' → SMTP fondation.bowabancongo.com
 *
 * Usage :
 *   require_once __DIR__ . '/kon/mailer.php';
 *   $mail = createMailer('main');
 *   // Configurer destinataires, sujet, corps → $mail->send();
 */

require_once __DIR__ . '/env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Chargement des classes PHPMailer (sans Composer)
$_phpmailerBase = dirname(__DIR__) . '/phpmailer/src/';
require_once $_phpmailerBase . 'Exception.php';
require_once $_phpmailerBase . 'PHPMailer.php';
require_once $_phpmailerBase . 'SMTP.php';
unset($_phpmailerBase);

/**
 * Crée et retourne un objet PHPMailer configuré pour le profil demandé.
 *
 * @param  string $profile  'main' ou 'fondation'
 * @return PHPMailer
 * @throws \InvalidArgumentException si le profil est inconnu
 * @throws Exception si la configuration SMTP est incomplète
 */
function createMailer(string $profile = 'main'): PHPMailer
{
    // ── Sélection du profil ──────────────────────────────────────────────
    switch ($profile) {
        case 'main':
            $host     = env('SMTP_HOST');
            $port     = (int) (env('SMTP_PORT', 465));
            $user     = env('SMTP_USER');
            $pass     = env('SMTP_PASS');
            $from     = env('SMTP_FROM');
            $fromName = env('SMTP_FROM_NAME', 'Contact Web');
            break;

        case 'fondation':
            $host     = env('FONDATION_SMTP_HOST');
            $port     = (int) (env('FONDATION_SMTP_PORT', 465));
            $user     = env('FONDATION_SMTP_USER');
            $pass     = env('FONDATION_SMTP_PASS');
            $from     = env('FONDATION_SMTP_FROM');
            $fromName = env('FONDATION_SMTP_FROM_NAME', 'Fondation-BOWABA');
            break;

        default:
            throw new \InvalidArgumentException("Profil mailer inconnu : \"{$profile}\"");
    }

    // ── Validation des variables obligatoires ───────────────────────────
    if (empty($host) || empty($user) || empty($pass) || empty($from)) {
        // On ne révèle pas les valeurs manquantes dans le log public
        error_log("[mailer] Configuration SMTP incomplète pour le profil \"{$profile}\". Vérifier le fichier .env.");
        throw new Exception("Configuration SMTP incomplète. Contacter l'administrateur.");
    }

    // ── Instanciation PHPMailer ──────────────────────────────────────────
    $mail = new PHPMailer(true); // true = exceptions activées

    // Serveur SMTP
    $mail->isSMTP();
    $mail->Host       = $host;
    $mail->SMTPAuth   = true;
    $mail->Username   = $user;
    $mail->Password   = $pass;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Port 465 → SMTPS
    $mail->Port       = $port;

    // Encodage
    $mail->CharSet  = 'UTF-8';
    $mail->Encoding = 'base64';

    // Expéditeur par défaut
    $mail->setFrom($from, $fromName);

    return $mail;
}
