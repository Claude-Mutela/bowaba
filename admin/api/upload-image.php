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

// Adjust CORS if needed, but for same-origin admin panel it's fine
header('Content-Type: application/json');

// Check uploaded file
if (!isset($_FILES['file']['name'])) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(['error' => 'Aucun fichier reçu.']);
    exit;
}

$file = $_FILES['file'];
$uploadDir = __DIR__ . '/../../assets/img/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
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
    // Return relative path for TinyMCE
    // Assuming admin is at /admin/ and uploads are at /assets/img/uploads/
    // The relative path from the page viewing it (e.g. blog-single.php) would be 'assets/img/uploads/...'
    // But from admin/articles/create.php, it's '../../assets/...'
    // TinyMCE needs an absolute path or relative to document base.
    // Let's return absolute path from web root if possible, or relative.
    
    // We can use the global base path convention.
    // If installed at root: /assets/img/uploads/filename
    // If in subdir: /bowaba/assets/img/uploads/filename
    
    // Let's try to determine relative path from web root
    $webPath = 'assets/img/uploads/' . $filename;
    
    // If the site is in a subdirectory (like /bowaba/), we might need to prepend it.
    // However, TinyMCE usually handles relative paths well if document_base_url is set, or if we use valid relative paths.
    // Since we don't know the exact web root depth from here easily without config,
    // let's try to return a path relative to the admin folder or absolute.
    
    // A robust way is to use the location relative to the domain root if we knew it.
    // simpler: return "../../assets/img/uploads/..." which works for admin pages, 
    // BUT for the frontend display, this path will be broken if stored as is in DB?
    // Wait, TinyMCE replaces the blob URI with this URL in the content HTML.
    // If we store "../../assets/..." in DB, it will work in admin but break in "blog-single.php" (which is at root).
    
    // So we MUST return a path that works from the root, e.g. "assets/img/uploads/..." 
    // AND ensure TinyMCE treats it correctly. 
    // Or we return "/bowaba/assets/img/uploads/..." (absolute path).
    
    // Let's look at `index.php`: $adminBase = '../';
    // The consistent way is "assets/img/uploads/..." and let the frontend add a base tag or prepend root.
    // But TinyMCE inside admin needs to resolve it.
    
    // Best bet: return the path relative to the domain root (absolute path).
    // Start with /
    $uri_parts = explode('/', $_SERVER['REQUEST_URI']);
    // Remove 'admin/api/upload-image.php'
    // This is tricky.
    
    // Easier: Just return "../assets/img/uploads/filename" (works for admin)
    // AND tell the user to configure 'relative_urls: false, remove_script_host: false, document_base_url: ...'
    
    // Let's try providing the path that works for the Admin editor first.
    // Then we might need a fix for Frontend.
    // Actually, if we return "assets/img/uploads/file.jpg", and the admin page is at "admin/articles/create.php",
    // the browser resolves it to "admin/articles/assets/img...". INVALID.
    
    // So for admin, we need "../../assets/img/uploads/file.jpg".
    // But then validation in DB has "../../". Frontend at root needs "assets/...".
    
    // SOLUTION: Use absolute path "/bowaba/assets/img/uploads/...".
    // We can detect the base path.
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']); // /bowaba/admin/api
    $baseDir = dirname(dirname($scriptDir)); // /bowaba
    $location = $baseDir . '/assets/img/uploads/' . $filename;
    
    // Fix backslashes on Windows
    $location = str_replace('\\', '/', $location);
    
    echo json_encode(['location' => $location]);
} else {
    header("HTTP/1.1 500 Server Error");
    echo json_encode(['error' => 'Échec du déplacement du fichier.']);
}
