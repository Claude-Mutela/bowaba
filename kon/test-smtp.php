<?php
/**
 * Script de diagnostic SMTP pour Bowaba
 * Exécution recommandée en CLI sur le serveur :
 *   php kon/test-smtp.php
 */

// Sécurité : n'autoriser l'accès web qu'en local ou avec une clé, ou autoriser en CLI
if (php_sapi_name() !== 'cli') {
    session_start();
    if (!isset($_SESSION['admin_id']) && (!isset($_GET['key']) || $_GET['key'] !== 'bowaba2026')) {
        http_response_code(403);
        die("Accès refusé. Exécutez ce script en ligne de commande : php kon/test-smtp.php\n");
    }
}

header('Content-Type: text/plain; charset=utf-8');

echo "====================================================\n";
echo "  DIAGNOSTIC SMTP BOWABA - " . date('Y-m-d H:i:s') . "\n";
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

// 3. Inspection des variables SMTP
$host     = env('SMTP_HOST');
$port     = env('SMTP_PORT', 465);
$user     = env('SMTP_USER');
$pass     = env('SMTP_PASS');
$from     = env('SMTP_FROM') ?: $user;
$fromName = env('SMTP_FROM_NAME', 'Contact Web');

echo "\n[3] Variables SMTP chargées :\n";
echo "    - SMTP_HOST      : " . ($host ?: "MANQUANT") . "\n";
echo "    - SMTP_PORT      : " . ($port ?: "MANQUANT") . "\n";
echo "    - SMTP_USER      : " . ($user ?: "MANQUANT") . "\n";
echo "    - SMTP_PASS      : " . ($pass ? (str_repeat('*', strlen($pass) - 2) . substr($pass, -2)) : "MANQUANT") . "\n";
echo "    - SMTP_FROM      : " . ($from ?: "MANQUANT") . (empty(env('SMTP_FROM')) && !empty($user) ? " (par défaut = SMTP_USER)" : "") . "\n";
echo "    - SMTP_FROM_NAME : {$fromName}\n";

if (empty($host) || empty($user) || empty($pass) || empty($from)) {
    echo "\n❌ ERREUR FATALE : Configuration SMTP incomplète dans le .env.\n";
    echo "   Vérifiez que le fichier .env contient bien SMTP_HOST, SMTP_USER, SMTP_PASS, SMTP_FROM.\n";
    exit(1);
}

// 4. Test DNS
echo "\n[4] Résolution DNS de '{$host}' :\n";
$ip = gethostbyname($host);
if ($ip === $host) {
    echo "    ❌ Impossible de résoudre l'adresse IP de '{$host}'. Vérifiez le nom d'hôte ou les DNS du serveur.\n";
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
    echo "       -> Cause probable : le port {$port} sortant est bloqué par le pare-feu du VPS ou l'hébergeur.\n";
} else {
    echo "    ✅ Connexion TCP établie avec succès sur le port {$port}.\n";
    $banner = fgets($socket, 512);
    if ($banner) {
        echo "    Bannière reçue : " . trim($banner) . "\n";
    }
    fclose($socket);
}

// 6. Test PHPMailer complet avec authentification
echo "\n[6] Test d'authentification PHPMailer :\n";
require_once __DIR__ . '/mailer.php';

try {
    $mail = createMailer('main');
    $mail->SMTPDebug = 2; // Niveau debug détaillé
    $mail->Debugoutput = function($str, $level) {
        echo "    [SMTP-DEBUG] {$str}\n";
    };

    echo "    -> Tentative de négociation et d'authentification...\n";
    if ($mail->smtpConnect()) {
        echo "\n🎉 SUCCÈS TOTAL : L'authentification SMTP a réussi avec brio !\n";
        echo "   Votre formulaire de contact peut maintenant envoyer des emails.\n";
        $mail->smtpClose();
    } else {
        echo "\n❌ ÉCHEC : Impossible de se connecter via PHPMailer.\n";
        echo "   Détail : " . $mail->ErrorInfo . "\n";
    }
} catch (\Throwable $e) {
    echo "\n❌ EXCEPTION LEVÉE : " . $e->getMessage() . "\n";
}

echo "\n====================================================\n";
