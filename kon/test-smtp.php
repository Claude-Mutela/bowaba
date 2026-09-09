<?php
/**
 * Script de diagnostic SMTP pour Bowaba
 * Usage CLI :
 *   php kon/test-smtp.php             -> Teste le profil 'main' (site principal)
 *   php kon/test-smtp.php fondation   -> Teste le profil 'fondation'
 */

// Sécurité : n'autoriser l'accès web qu'en local ou avec une clé, ou autoriser en CLI
if (php_sapi_name() !== 'cli') {
    session_start();
    if (!isset($_SESSION['admin_id']) && (!isset($_GET['key']) || $_GET['key'] !== 'bowaba2026')) {
        http_response_code(403);
        die("Accès refusé. Exécutez ce script en ligne de commande : php kon/test-smtp.php [main|fondation]\n");
    }
}

header('Content-Type: text/plain; charset=utf-8');

$profile = 'main';
if (php_sapi_name() === 'cli') {
    if (isset($argv[1]) && strtolower($argv[1]) === 'fondation') {
        $profile = 'fondation';
    }
} else {
    if (isset($_GET['profile']) && strtolower($_GET['profile']) === 'fondation') {
        $profile = 'fondation';
    }
}

echo "====================================================\n";
echo "  DIAGNOSTIC SMTP BOWABA [Profil : " . strtoupper($profile) . "] - " . date('Y-m-d H:i:s') . "\n";
echo "====================================================\n\n";

// 1. Détection de l'utilisateur système
if (function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
    $processUser = posix_getpwuid(posix_geteuid());
    echo "[1] Utilisateur système actuel : " . ($processUser['name'] ?? 'inconnu') . "\n";
} else {
    echo "[1] Utilisateur système actuel : " . get_current_user() . "\n";
}

// 2. Chargement du fichier .env
require_once __DIR__ . '/env.php';

$envPath = dirname(__DIR__) . '/.env';
echo "[2] Vérification du fichier .env :\n";
echo "    - Chemin : {$envPath}\n";
echo "    - Existe : " . (file_exists($envPath) ? "OUI" : "NON") . "\n";
echo "    - Lisible par le processus PHP : " . (is_readable($envPath) ? "OUI" : "NON (CRITIQUE: permissions insuffisantes !)") . "\n";

if (file_exists($envPath)) {
    $perms = substr(sprintf('%o', fileperms($envPath)), -4);
    echo "    - Permissions actuelles : {$perms}\n";
}

// 3. Inspection des variables SMTP selon le profil
if ($profile === 'fondation') {
    $prefix   = 'FONDATION_';
    $host     = env('FONDATION_SMTP_HOST');
    $port     = env('FONDATION_SMTP_PORT', 587);
    $user     = env('FONDATION_SMTP_USER');
    $pass     = env('FONDATION_SMTP_PASS');
    $from     = env('FONDATION_SMTP_FROM') ?: $user;
    $fromName = env('FONDATION_SMTP_FROM_NAME', 'Fondation-BOWABA');
} else {
    $prefix   = '';
    $host     = env('SMTP_HOST');
    $port     = env('SMTP_PORT', 587);
    $user     = env('SMTP_USER');
    $pass     = env('SMTP_PASS');
    $from     = env('SMTP_FROM') ?: $user;
    $fromName = env('SMTP_FROM_NAME', 'Contact Web');
}

echo "\n[3] Variables SMTP chargées pour [{$profile}] :\n";
echo "    - {$prefix}SMTP_HOST      : " . ($host ?: "MANQUANT") . "\n";
echo "    - {$prefix}SMTP_PORT      : " . ($port ?: "MANQUANT") . "\n";
echo "    - {$prefix}SMTP_USER      : " . ($user ?: "MANQUANT") . "\n";
echo "    - {$prefix}SMTP_PASS      : " . ($pass ? (str_repeat('*', max(0, strlen($pass) - 2)) . substr($pass, -2)) : "MANQUANT") . "\n";
echo "    - {$prefix}SMTP_FROM      : " . ($from ?: "MANQUANT") . "\n";
echo "    - {$prefix}SMTP_FROM_NAME : {$fromName}\n";

if (empty($host) || empty($user) || empty($pass) || empty($from)) {
    echo "\n❌ ERREUR FATALE : Configuration SMTP incomplète dans le .env pour le profil '{$profile}'.\n";
    echo "   Veuillez vérifier les variables {$prefix}SMTP_HOST, {$prefix}SMTP_USER, {$prefix}SMTP_PASS dans votre fichier .env.\n";
    exit(1);
}

// 4. Test DNS
echo "\n[4] Résolution DNS de '{$host}' :\n";
$ip = gethostbyname($host);
if ($ip === $host && !filter_var($host, FILTER_VALIDATE_IP)) {
    echo "    ❌ Impossible de résoudre l'adresse IP de '{$host}'. Hôte inconnu !\n";
    echo "       -> Vérifiez le nom du serveur SMTP (ex: mail.bowabancongo.com).\n";
} else {
    echo "    ✅ Résolu avec succès : {$ip}\n";
}

// 5. Test de connectivité réseau (Socket)
echo "\n[5] Test d'ouverture de port vers {$host}:{$port} :\n";
$errno = 0;
$errstr = '';
$timeout = 10;
$socket = @fsockopen($host, (int)$port, $errno, $errstr, $timeout);

if (!$socket) {
    echo "    ❌ ÉCHEC : Impossible d'ouvrir la connexion vers {$host}:{$port}.\n";
    echo "       Erreur [{$errno}] : {$errstr}\n";
    echo "       -> Cause probable : port {$port} bloqué ou serveur inaccessible.\n";
} else {
    echo "    ✅ Connexion TCP établie avec succès sur le port {$port}.\n";
    $banner = fgets($socket, 512);
    if ($banner) {
        echo "    Bannière reçue : " . trim($banner) . "\n";
    }
    fclose($socket);
}

// 6. Test PHPMailer complet avec authentification et envoi réel
echo "\n[6] Test PHPMailer avec authentification pour [{$profile}] :\n";
require_once __DIR__ . '/mailer.php';

try {
    $mail = createMailer($profile);
    $mail->SMTPDebug = 2; // Niveau debug détaillé
    $mail->Debugoutput = function($str, $level) {
        echo "    [SMTP-DEBUG] {$str}\n";
    };

    echo "    -> Tentative de négociation et d'authentification...\n";
    if ($mail->smtpConnect()) {
        echo "    ✅ Connexion et authentification réussies !\n";

        echo "\n[7] Test d'envoi réel d'un e-mail :\n";
        $testRecipient = $user;
        $mail->addAddress($testRecipient);
        $mail->Subject = "[Diagnostic Bowaba - {$profile}] Test SMTP réussi";
        $mail->Body = "Ceci est un message de test automatique pour valider l'envoi d'e-mails depuis bowabancongo.com (profil {$profile}).\nDate : " . date('Y-m-d H:i:s');
        
        echo "    -> Envoi en cours vers {$testRecipient}...\n";
        $mail->send();
        echo "\n🎉 SUCCÈS TOTAL : E-mail de test envoyé et accepté par le serveur SMTP pour [{$profile}] !\n";
        echo "   Le formulaire de contact fonctionnera parfaitement.\n";
    } else {
        echo "\n❌ ÉCHEC : Impossible de se connecter via PHPMailer.\n";
        echo "   Détail : " . $mail->ErrorInfo . "\n";
    }
} catch (\Throwable $e) {
    echo "\n❌ EXCEPTION LEVÉE : " . $e->getMessage() . "\n";
}

echo "\n====================================================\n";
