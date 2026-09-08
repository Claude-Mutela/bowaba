<?php
/**
 * Role-Based Access Control — admin/partials/permissions.php
 *
 * Defines the permission matrix and helper functions.
 * Included automatically via auth.php (no need to include manually).
 *
 * Roles:
 *   admin  — Full access to everything
 *   editor — Articles (all) + Categories (read/write). No Services, no Users.
 *   author — Can see all articles & services (read-only). Create/edit own articles (draft only).
 */

// ── Permission Matrix ──────────────────────────────────────────────────────
$PERMISSIONS = [
    'dashboard' => ['admin', 'editor', 'author'],

    // Articles
    'articles.view_all'  => ['admin', 'editor', 'author'],  // see all authors' articles
    'articles.view_own'  => ['admin', 'editor', 'author'],// see own articles
    'articles.create'    => ['admin', 'editor', 'author'],
    'articles.edit_all'  => ['admin', 'editor'],          // edit any article
    'articles.edit_own'  => ['admin', 'editor', 'author'],// edit own articles only
    'articles.delete'    => ['admin', 'editor'],
    'articles.publish'   => ['admin', 'editor'],          // set status=published/archived
    'articles.feature'   => ['admin', 'editor'],          // toggle is_featured

    // Categories
    'categories.view'    => ['admin', 'editor', 'author'],
    'categories.create'  => ['admin', 'editor'],
    'categories.edit'    => ['admin', 'editor'],
    'categories.delete'  => ['admin', 'editor'],

    // Services
    'services.view'      => ['admin', 'editor', 'author'],
    'services.create'    => ['admin', 'editor'],
    'services.edit'      => ['admin', 'editor'],
    'services.delete'    => ['admin', 'editor'],

    // Users
    'users.view'         => ['admin'],
    'users.create'       => ['admin'],
    'users.edit'         => ['admin'],
    'users.delete'       => ['admin'],

    // Projects (Portfolio)
    'projects.view'      => ['admin', 'editor', 'author'],
    'projects.create'    => ['admin', 'editor'],
    'projects.edit'      => ['admin', 'editor'],
    'projects.delete'    => ['admin', 'editor'],

    // Partners (Entreprises)
    'partners.view'      => ['admin', 'editor', 'author'],
    'partners.create'    => ['admin', 'editor'],
    'partners.edit'      => ['admin', 'editor'],
    'partners.delete'    => ['admin', 'editor'],

    // Testimonials (Clients)
    'testimonials.view'  => ['admin', 'editor', 'author'],
    'testimonials.create'=> ['admin', 'editor'],
    'testimonials.edit'  => ['admin', 'editor'],
    'testimonials.delete'=> ['admin', 'editor'],

    // Students (Apprenants)
    'students.view'      => ['admin', 'editor', 'author'],
    'students.create'    => ['admin', 'editor'],
    'students.edit'      => ['admin', 'editor'],
    // Training Programs (Formations)
    'training_programs.view'      => ['admin', 'editor', 'author'],
    'training_programs.create'    => ['admin', 'editor'],
    'training_programs.edit'      => ['admin', 'editor'],
    'training_programs.delete'    => ['admin', 'editor'],
];

// ── Helper Functions ───────────────────────────────────────────────────────

/**
 * Check if the current user has a specific permission.
 *
 * @param  string $permission  e.g. 'articles.delete'
 * @return bool
 */
function can(string $permission): bool {
    global $PERMISSIONS, $adminUser;
    $role = $adminUser['role'] ?? 'author';
    return in_array($role, $PERMISSIONS[$permission] ?? []);
}

/**
 * Abort with a 403 page if the user lacks the required permission.
 *
 * @param string $permission
 * @param string $message    Optional custom message
 */
function requirePermission(string $permission, string $message = ''): void {
    if (!can($permission)) {
        http_response_code(403);
        $msg = $message ?: 'Vous n\'avez pas les droits pour accéder à cette page.';
        // If header.php has already been included, show a styled alert
        if (defined('ADMIN_HEADER_LOADED')) {
            echo '<div class="admin-alert admin-alert-error" style="margin:40px auto;max-width:600px;">
                    <i class="bi bi-shield-lock-fill" style="font-size:20px;"></i>
                    <div>
                      <strong>Accès refusé (403)</strong><br>
                      ' . htmlspecialchars($msg) . '
                      <br><br>
                      <a href="javascript:history.back()" style="color:var(--bbc-blue);">
                        <i class="bi bi-arrow-left"></i> Retour
                      </a>
                    </div>
                  </div>';
            include __DIR__ . '/footer.php';
        } else {
            // Minimal error page before header is loaded
            echo '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8">
                  <title>Accès refusé</title>
                  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
                  </head><body class="bg-light d-flex align-items-center justify-content-center" style="min-height:100vh;">
                  <div class="text-center p-5">
                    <div style="font-size:64px;">🔒</div>
                    <h1 class="h3 fw-bold mt-3">Accès refusé</h1>
                    <p class="text-muted">' . htmlspecialchars($msg) . '</p>
                    <a href="javascript:history.back()" class="btn btn-primary">← Retour</a>
                  </div></body></html>';
        }
        exit;
    }
}

/**
 * Check if the current user owns a resource (by user_id field).
 *
 * @param  int|string $resourceUserId  The user_id stored on the resource
 * @return bool
 */
function isOwner($resourceUserId): bool {
    global $adminUser;
    return (int)$adminUser['id'] === (int)$resourceUserId;
}

/**
 * Check if user can edit a specific article (owns it OR has edit_all permission).
 *
 * @param  int|string $articleUserId
 * @return bool
 */
function canEditArticle($articleUserId): bool {
    return can('articles.edit_all') || (can('articles.edit_own') && isOwner($articleUserId));
}
