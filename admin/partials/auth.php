<?php
/**
 * Admin Auth Guard — admin/partials/auth.php
 *
 * Include this file at the very top of every protected admin page
 * (BEFORE any output). It starts the session and redirects to
 * login.php if the user is not authenticated.
 *
 * Usage:
 *   require_once __DIR__ . '/../partials/auth.php';   // from admin sub-pages
 *   require_once __DIR__ . '/partials/auth.php';       // from admin/index.php
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine the path to login.php relative to the current script
$_adminRoot = str_replace('\\', '/', dirname(__DIR__)); // …/admin
$_selfDir   = str_replace('\\', '/', dirname($_SERVER['SCRIPT_FILENAME']));

// How many levels deep are we from admin/ ?
$_depth = substr_count(str_replace($_adminRoot, '', $_selfDir), '/');
$_loginPath = str_repeat('../', $_depth) . 'login.php';

if (empty($_SESSION['admin_logged_in'])) {
    // Preserve the requested URL so we can redirect back after login
    $redirect = urlencode($_SERVER['REQUEST_URI']);
    header('Location: ' . $_loginPath . '?redirect=' . $redirect);
    exit;
}

// Expose session data as convenient globals
$adminUser = [
    'id'   => $_SESSION['admin_user_id']   ?? 0,
    'name' => $_SESSION['admin_user_name'] ?? 'Administrateur',
    'role' => $_SESSION['admin_user_role'] ?? 'admin',
];

// Regenerate session ID periodically (every 30 min) to prevent fixation
if (!isset($_SESSION['last_regenerated'])) {
    $_SESSION['last_regenerated'] = time();
} elseif (time() - $_SESSION['last_regenerated'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['last_regenerated'] = time();
}

// Load permission helpers
require_once __DIR__ . '/permissions.php';
