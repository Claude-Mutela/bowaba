<?php
// Protection contre les redéfinitions de BASE_URL
if (!defined('BASE_URL')) {
    $host = $_SERVER['HTTP_HOST'] ?? 'bowabancongo.com';

    // Détection HTTPS robuste (compatible Nginx, Apache, Cloudflare, Traefik reverse proxy)
    $isHttps = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
            || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
            || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) === 'on');

    // En production sur bowabancongo.com, toujours forcer HTTPS pour les crawlers de réseaux sociaux
    if (strpos($host, 'bowabancongo.com') !== false) {
        define('BASE_URL', 'https://' . $host . '/');
    } else {
        $protocol = $isHttps ? "https://" : "http://";
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/';
        $scriptDir  = str_replace('\\', '/', dirname($scriptName));

        // En local sous Apache/MAMP (ex: /bowaba/)
        if (preg_match('#^(/[^/]+)#', $scriptName, $matches) && !in_array($matches[1], ['/admin', '/fondation', '/assets'])) {
            $basePath = $matches[1];
        } else {
            $basePath = ($scriptDir === '/' || $scriptDir === '.') ? '' : $scriptDir;
        }

        $baseUrl = rtrim($protocol . $host . $basePath, '/') . '/';
        define('BASE_URL', $baseUrl);
    }
}

