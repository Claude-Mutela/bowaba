<?php
/**
 * conn.php — Connexion PDO à la base de données
 * ─────────────────────────────────────────────
 * Les credentials sont lus depuis les variables d'environnement (.env en local,
 * variables système en production). Ne jamais écrire de mot de passe dans ce fichier.
 *
 * Configuration requise dans .env :
 *   DB_HOST, DB_NAME, DB_USER, DB_PASS
 */

require_once __DIR__ . '/env.php';

$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbName = getenv('DB_NAME');
$dbUser = getenv('DB_USER');
$dbPass = getenv('DB_PASS');

if (empty($dbName) || empty($dbUser)) {
    error_log('[DB] Variables d\'environnement DB manquantes. Vérifier le fichier .env.');
    http_response_code(500);
    exit('Service temporairement indisponible.');
}

try {
    $conn = new PDO(
        "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    // Ne jamais afficher le message brut en production
    error_log('[DB] Connection failed: ' . $e->getMessage());
    http_response_code(500);
    exit('Service temporairement indisponible.');
}
