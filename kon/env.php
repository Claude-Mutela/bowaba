<?php
/**
 * env.php — Chargeur universel de fichier .env (Local et Production)
 * ─────────────────────────────────────────────────────────────────────
 */

(function () {
    // Détecte la racine du projet de manière fiable (que le script soit à la racine ou dans /kon/)
    $rootDir = realpath(__DIR__ . '/..') ?: dirname(__DIR__);
    
    // Si on est dans le dossier /kon/, on remonte d'un niveau supplémentaire vers la racine
    if (basename(__DIR__) === 'kon') {
        $rootDir = realpath(__DIR__ . '/../..') ?: dirname(__DIR__, 2);
    }

    $envFile = $rootDir . '/.env';

    // Si les variables sont déjà chargées par le serveur, on ne fait rien
    if (getenv('APP_ENV') !== false) {
        return;
    }

    // Si le fichier .env n'existe pas, on sort
    if (!is_file($envFile)) {
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

        if (strlen($value) >= 2) {
            $first = $value[0];
            $last  = $value[strlen($value) - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        if (!isset($_ENV[$key]) && getenv($key) === false) {
            $_ENV[$key] = $value;
            putenv("{$key}={$value}");
        }
    }
})();