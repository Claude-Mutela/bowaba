<?php
// Détection automatique de l'URL de base pour gérer les rewrites et les assets
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];

// Détermine le chemin du dossier racine du projet
// En local wamp/mamp : /bowaba/
// En prod racine : /
$scriptName = $_SERVER['SCRIPT_NAME'];
$scriptDir = dirname($scriptName);

// Nettoyage des backslashes (Windows)
$scriptDir = str_replace('\\', '/', $scriptDir);

// Retirer les éventuels sous-dossiers (admin, etc.) pour revenir à la racine du site si nécessaire
// Ici, on suppose que config.php est appelé depuis la racine ou via un include qui connait le contexte.
// Pour plus de sûreté, on peut définir manuellement en PROD si l'auto-détection échoue.
// define('BASE_URL', 'https://bowabancongo.com/'); 

// Auto-détection simple basée sur le fait que index.php est à la racine
// Si on est dans /admin/, on veut peut-être la racine du site quand même.
// Pour l'instant, on se base sur le dossier courant du script exécuté, 
// MAIS attention aux rewrites.

// APPROCHE ROBUSTE :
// On définit la racine par rapport à l'emplacement de ce fichier config.php ? 
// Non, car il est dans /kon/.

// On va utiliser une approche flexible :
// Si PROD (domaine bowabancongo.com), c'est la racine.
if (strpos($host, 'bowabancongo.com') !== false) {
    define('BASE_URL', $protocol . $host . '/');
} else {
    // LOCAL (localhost ou autre) -> on essaie de garder le sous-dossier s'il existe
    // On suppose que le site est dans le dossier parent de 'kon' ?
    // Non, le plus simple est de définir manuellement pour le local si l'auto-détection foire.
    // Mais pour MAMP par défaut : localhost/bowaba/
    
    // On prend le dirname du script en cours, et on remonte jusqu'à trouver 'bowaba' ou on prend la racine
    // Simplification : on utilise le dossier du script courant en s'assurant de finir par /
    
    // Correction pour les rewrites :
    // Quand on appelle /blog/mon-article, le script exécuté est /bowaba/blog-single.php
    // Donc dirname est /bowaba. C'est correct !
    
    $baseUrl = $protocol . $host . $scriptDir;
    
    // Assurer le slash final
    $baseUrl = rtrim($baseUrl, '/') . '/';
    
    define('BASE_URL', $baseUrl);
}
?>
