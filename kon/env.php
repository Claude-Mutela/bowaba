<?php
/**
 * env.php — Chargeur natif de fichier .env (sans dépendance Composer)
 * ─────────────────────────────────────────────────────────────────────
 * Charge les variables depuis le fichier .env situé à la racine du projet
 * et les injecte dans $_ENV et getenv().
 *
 * Usage : require_once __DIR__ . '/env.php';
 *         puis : getenv('SMTP_PASS') ou $_ENV['SMTP_PASS']
 *
 * En production (VPS/Nginx), les variables d'env peuvent être définies
 * directement dans la config serveur. Dans ce cas, ce fichier ne fait rien.
 */

(function () {
    // Chemin absolu vers la racine du projet (parent de /kon/)
    $envFile = dirname(__DIR__) . '/.env';

    // Si les variables sont déjà chargées par le serveur, on ne fait rien
    if (getenv('APP_ENV') !== false) {
        return;
    }

    // Si le fichier .env n'existe pas (ex: production sans .env), on sort
    if (!is_file($envFile)) {
        return;
    }

    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // Ignorer les commentaires
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }

        // Parser KEY=VALUE (la valeur peut contenir des =)
        $pos = strpos($line, '=');
        if ($pos === false) {
            continue;
        }

        $key   = trim(substr($line, 0, $pos));
        $value = trim(substr($line, $pos + 1));

        // Retirer les guillemets éventuels autour de la valeur
        if (strlen($value) >= 2) {
            $first = $value[0];
            $last  = $value[strlen($value) - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        // Ne pas écraser une variable déjà définie dans l'environnement système
        if (!isset($_ENV[$key]) && getenv($key) === false) {
            $_ENV[$key] = $value;
            putenv("{$key}={$value}");
        }
    }
})();
