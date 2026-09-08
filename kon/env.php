<?php
/**
 * env.php — Chargeur universel de fichier .env (Local et Production)
 * ─────────────────────────────────────────────────────────────────────
 * Cherche le fichier .env dans plusieurs emplacements par ordre de priorité :
 *
 *  1. Racine du projet       → /var/www/bowaba/.env   (local MAMP)
 *  2. Parent du projet       → /var/www/.env          (prod VPS actuel)
 *  3. Parent+1 du projet     → /.env                  (configs serveur avancées)
 *
 * En production, si APP_ENV est déjà défini dans l'environnement système
 * (Nginx, Apache SetEnv, etc.), ce fichier ne fait rien.
 */

(function () {
    // Si les variables sont déjà chargées par le serveur, on ne fait rien
    if (getenv('APP_ENV') !== false) {
        return;
    }

    // Racine du projet = parent de /kon/
    $projectRoot = dirname(__DIR__);

    // Emplacements candidats, du plus spécifique au plus général
    $candidates = [
        $projectRoot . '/.env',               // local  : /htdocs/bowaba/.env
        dirname($projectRoot) . '/.env',       // prod   : /var/www/.env
        dirname($projectRoot, 2) . '/.env',    // avancé : /var/.env
    ];

    $envFile = null;
    foreach ($candidates as $path) {
        if (is_file($path) && is_readable($path)) {
            $envFile = $path;
            break;
        }
    }

    // Aucun fichier .env trouvé → on laisse le serveur gérer les variables
    if ($envFile === null) {
        return;
    }

    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }

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