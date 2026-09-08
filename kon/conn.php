<?php
/**
 * conn.php — Connexion PDO universelle à la base de données (Local & Production)
 * ─────────────────────────────────────────────────────────────────────────────
 * Les identifiants sont chargés depuis le fichier .env via kon/env.php.
 *
 * Variables attendues dans .env :
 *   DB_HOST   (défaut: localhost)
 *   DB_PORT   (défaut: 3306)
 *   DB_NAME   (obligatoire)
 *   DB_USER   (obligatoire)
 *   DB_PASS   (mot de passe)
 *   DB_SOCKET (optionnel, pour socket UNIX personnalisé sur VPS)
 */

require_once __DIR__ . '/env.php';

$dbHost   = trim((string) env('DB_HOST', 'localhost'));
$dbPort   = trim((string) env('DB_PORT', '3306'));
$dbName   = trim((string) env('DB_NAME', ''));
$dbUser   = trim((string) env('DB_USER', ''));
$dbPass   = (string) env('DB_PASS', '');
$dbSocket = trim((string) env('DB_SOCKET', ''));

// Validation des variables indispensables
if ($dbName === '' || $dbUser === '') {
    $missing = [];
    if ($dbName === '') $missing[] = 'DB_NAME';
    if ($dbUser === '') $missing[] = 'DB_USER';

    error_log('[DB ERROR] Configuration incomplète dans .env. Variable(s) manquante(s) : ' . implode(', ', $missing));

    $isLocal = (env('APP_ENV') === 'local' || env('APP_DEBUG') === 'true' || in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1']));
    http_response_code(500);
    if ($isLocal) {
        exit('<strong>Erreur de configuration DB :</strong> Veuillez renseigner ' . implode(' et ', $missing) . ' dans votre fichier <code>.env</code>.');
    }
    exit('Service temporairement indisponible.');
}

// Options PDO pour la sécurité, l'encodage et la performance
$pdoOptions = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',
];

$conn = null;

// Construction du DSN principal
if ($dbSocket !== '') {
    $dsn = "mysql:unix_socket={$dbSocket};dbname={$dbName};charset=utf8mb4";
} else {
    $portPart = ($dbPort !== '') ? ";port={$dbPort}" : '';
    $dsn = "mysql:host={$dbHost}{$portPart};dbname={$dbName};charset=utf8mb4";
}

try {
    $conn = new PDO($dsn, $dbUser, $dbPass, $pdoOptions);
} catch (PDOException $e) {
    // Mécanisme de fallback intelligent pour les serveurs Linux VPS :
    // Si la connexion via 'localhost' échoue (ex: socket MySQL non trouvé, code 2002),
    // on tente automatiquement la connexion TCP sur 127.0.0.1 (et vice-versa).
    $fallbackHost = null;
    if ($dbSocket === '') {
        if ($dbHost === 'localhost') {
            $fallbackHost = '127.0.0.1';
        } elseif ($dbHost === '127.0.0.1') {
            $fallbackHost = 'localhost';
        }
    }

    if ($fallbackHost !== null) {
        try {
            $fallbackPortPart = ($dbPort !== '') ? ";port={$dbPort}" : '';
            $fallbackDsn = "mysql:host={$fallbackHost}{$fallbackPortPart};dbname={$dbName};charset=utf8mb4";
            $conn = new PDO($fallbackDsn, $dbUser, $dbPass, $pdoOptions);
        } catch (PDOException $e2) {
            // Le fallback a également échoué, on conserve l'erreur d'origine
        }
    }

    if ($conn === null) {
        error_log('[DB ERROR] Échec de connexion : ' . $e->getMessage());

        $isLocal = (env('APP_ENV') === 'local' || env('APP_DEBUG') === 'true' || in_array($_SERVER['SERVER_NAME'] ?? '', ['localhost', '127.0.0.1']));
        http_response_code(500);

        if ($isLocal) {
            exit('<h3>Erreur de connexion MySQL</h3><p>' . htmlspecialchars($e->getMessage()) . '</p><p>Hôte testé : <code>' . htmlspecialchars($dbHost) . '</code> | Base : <code>' . htmlspecialchars($dbName) . '</code> | Utilisateur : <code>' . htmlspecialchars($dbUser) . '</code></p>');
        }

        exit('Service temporairement indisponible.');
    }
}
