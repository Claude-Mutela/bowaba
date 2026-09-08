<?php
/**
 * env.php — Chargeur universel et résilient de fichier .env (Local et Production)
 * ─────────────────────────────────────────────────────────────────────────────
 * Recherche le fichier .env dans les emplacements standards :
 *  1. Racine du projet       → /var/www/bowaba/.env   (VPS) ou C:\MAMP\htdocs\bowaba\.env (Local)
 *  2. Parent du projet       → /var/www/.env
 *  3. DocumentRoot serveur   → $_SERVER['DOCUMENT_ROOT']/.env
 *
 * Définit les variables dans putenv(), $_ENV et $_SERVER, et fournit la fonction env().
 */

if (!defined('BOWABA_ENV_LOADED')) {
    define('BOWABA_ENV_LOADED', true);

    (function () {
        // Racine du projet = parent du dossier /kon/
        $projectRoot = dirname(__DIR__);

        // Emplacements candidats, du plus spécifique au plus général
        $rawCandidates = [
            $projectRoot . '/.env',
            dirname($projectRoot) . '/.env',
            dirname($projectRoot, 2) . '/.env',
            (!empty($_SERVER['DOCUMENT_ROOT']) ? rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . '/.env' : null),
            (!empty($_SERVER['DOCUMENT_ROOT']) ? rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . '/bowaba/.env' : null),
        ];

        // Dédupliquer les chemins valides
        $candidates = [];
        foreach ($rawCandidates as $c) {
            if ($c !== null && !in_array($c, $candidates, true)) {
                $candidates[] = str_replace('\\', '/', $c);
            }
        }

        $envFile = null;
        $unreadableFile = null;

        foreach ($candidates as $path) {
            if (is_file($path)) {
                if (is_readable($path)) {
                    $envFile = $path;
                    break;
                } else {
                    $unreadableFile = $path;
                }
            }
        }

        // Diagnostic critique pour la production :
        // Si le fichier existe mais que PHP (ex: www-data) ne peut pas le lire
        if ($envFile === null && $unreadableFile !== null) {
            error_log("[ENV ERROR] Fichier .env détecté à '{$unreadableFile}' mais PHP n'a pas les droits de lecture. Solution VPS : exécutez 'chmod 640 {$unreadableFile}' et 'chown deploy:www-data {$unreadableFile}'.");
        }

        // Si aucun fichier .env lisible n'a été trouvé, on s'arrête (les variables système restent utilisables)
        if ($envFile === null) {
            return;
        }

        $lines = @file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            error_log("[ENV ERROR] Impossible de lire le contenu de {$envFile}.");
            return;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || $line[0] === ';') {
                continue;
            }

            $pos = strpos($line, '=');
            if ($pos === false) {
                continue;
            }

            $key   = trim(substr($line, 0, $pos));
            $value = trim(substr($line, $pos + 1));

            if ($key === '') {
                continue;
            }

            // Gestion des guillemets éventuels autour de la valeur
            $len = strlen($value);
            if ($len >= 2) {
                $first = $value[0];
                $last  = $value[$len - 1];
                if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                    $value = substr($value, 1, -1);
                } else {
                    // Si non entre guillemets, supprimer les commentaires en fin de ligne (# ...)
                    $commentPos = strpos($value, '#');
                    if ($commentPos !== false) {
                        $value = trim(substr($value, 0, $commentPos));
                    }
                }
            } elseif ($len === 1 && ($value === '"' || $value === "'")) {
                $value = '';
            }

            // Injecter dans putenv, $_ENV et $_SERVER
            putenv("{$key}={$value}");
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;
        }
    })();
}

if (!function_exists('env')) {
    /**
     * Récupère la valeur d'une variable d'environnement avec fallback.
     *
     * @param string $key Nom de la variable
     * @param mixed $default Valeur de repli si la variable est absente ou vide
     * @return mixed
     */
    function env(string $key, $default = null)
    {
        // 1. Vérifier $_ENV
        if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
            return $_ENV[$key];
        }

        // 2. Vérifier $_SERVER
        if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
            return $_SERVER[$key];
        }

        // 3. Vérifier getenv()
        $val = getenv($key);
        if ($val !== false && $val !== '') {
            return $val;
        }

        // 4. Si la valeur a été explicitement définie à vide ('')
        if (array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }
        if (array_key_exists($key, $_SERVER)) {
            return $_SERVER[$key];
        }
        if ($val !== false) {
            return $val;
        }

        return $default;
    }
}