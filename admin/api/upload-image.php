<?php
/**
 * TinyMCE Image Upload Handler — admin/api/upload-image.php
 */
require_once __DIR__ . '/../../kon/conn.php';
require_once __DIR__ . '/../partials/auth.php'; // Verify session and RBAC

// Enforce authentication
if (!isset($adminUser) || !can('articles.create')) {
    header("HTTP/1.1 403 Forbidden");
    echo json_encode(['error' => 'Non autorisé.']);
    exit;
}

header('Content-Type: application/json');

// Check uploaded file
if (!isset($_FILES['file']['name']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $errCode = $_FILES['file']['error'] ?? -1;
    $msg = ($errCode === UPLOAD_ERR_INI_SIZE || $errCode === UPLOAD_ERR_FORM_SIZE)
        ? 'Fichier trop volumineux (dépasse upload_max_filesize).'
        : 'Aucun fichier reçu ou erreur de transfert.';
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(['error' => $msg]);
    exit;
}

$file = $_FILES['file'];
$uploadDir = __DIR__ . '/../../assets/img/uploads/';
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0775, true);
}

// Validation
$allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowedExts)) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(['error' => 'Format non supporté (jpg, png, gif, webp).']);
    exit;
}

if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(['error' => 'Fichier trop volumineux (max 5Mo).']);
    exit;
}

// Generate unique filename
$filename = uniqid('upload_') . '.' . $ext;
$targetPath = $uploadDir . $filename;

if (move_uploaded_file($file['tmp_name'], $targetPath)) {
    @chmod($targetPath, 0664);

    // Détection de la racine web (support Local sous-dossier ex: /bowaba/ et Prod racine ex: /)
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']); // /bowaba/admin/api ou /admin/api
    $baseDir   = dirname(dirname($scriptDir));      // /bowaba ou /
    $baseDir   = str_replace('\\', '/', $baseDir);

    if ($baseDir === '/' || $baseDir === '.' || $baseDir === '\\') {
        $baseDir = '';
    } else {
        $baseDir = rtrim($baseDir, '/');
    }

    // URL absolue depuis la racine du domaine (NE DOIT JAMAIS COMMENCER PAR //)
    $location = $baseDir . '/assets/img/uploads/' . $filename;

    echo json_encode(['location' => $location]);
} else {
    error_log("[UPLOAD ERROR] Impossible d'enregistrer dans {$uploadDir}. Is dir: " . (is_dir($uploadDir) ? 'oui' : 'non') . ", Writable: " . (is_writable($uploadDir) ? 'oui' : 'non'));
    header("HTTP/1.1 500 Server Error");
    echo json_encode(['error' => "Échec de l'enregistrement de l'image sur le serveur. Vérifiez les permissions du dossier assets/img/uploads/."]);
}
