<?php
/**
 * Admin Layout — Head partial
 * Include at the top of every admin page.
 * Required vars: $pageTitle, $activePage
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> — BowaBanCongo CMS</title>
  <meta name="robots" content="noindex, nofollow">

  <!-- Bootstrap 5.3 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Raleway:wght@600;700;800&display=swap" rel="stylesheet">
  <!-- Admin CSS -->
  <link rel="stylesheet" href="<?= $adminBase ?? '../' ?>assets/css/admin.css">
</head>
<body class="admin-body">

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ======= SIDEBAR ======= -->
<aside class="admin-sidebar" id="adminSidebar">

  <!-- Brand -->
  <a href="<?= $adminBase ?? '../' ?>index.php" class="sidebar-brand">
    <img src="<?= $adminBase ?? '../' ?>../assets/img/logo/logo-bw.png" alt="BowaBanCongo">
    <div class="sidebar-brand-text">
      <span class="sidebar-brand-name">BowaBanCongo</span>
      <span class="sidebar-brand-sub">Administration</span>
    </div>
  </a>

  <!-- Navigation -->
  <nav class="sidebar-nav">

    <div class="sidebar-section-label">Principal</div>

    <a href="<?= $adminBase ?? '../' ?>index.php"
       class="nav-link <?= ($activePage ?? '') === 'dashboard' ? 'active' : '' ?>">
      <i class="bi bi-grid-1x2-fill"></i>
      <span>Tableau de bord</span>
    </a>

    <div class="sidebar-section-label">Contenu</div>

    <a href="<?= $adminBase ?? '../' ?>articles/index.php"
       class="nav-link <?= ($activePage ?? '') === 'articles' ? 'active' : '' ?>">
      <i class="bi bi-file-earmark-richtext"></i>
      <span>Articles</span>
      <span class="badge-count" id="sidebarArticleCount">—</span>
    </a>

    <a href="<?= $adminBase ?? '../' ?>categories/index.php"
       class="nav-link <?= ($activePage ?? '') === 'categories' ? 'active' : '' ?>">
      <i class="bi bi-tags-fill"></i>
      <span>Catégories</span>
    </a>

    <a href="<?= $adminBase ?? '../' ?>services/index.php"
       class="nav-link <?= ($activePage ?? '') === 'services' ? 'active' : '' ?>">
      <i class="bi bi-briefcase-fill"></i>
      <span>Services</span>
    </a>

    <div class="sidebar-section-label">Gestion</div>

    <a href="<?= $adminBase ?? '../' ?>users/index.php"
       class="nav-link <?= ($activePage ?? '') === 'users' ? 'active' : '' ?>">
      <i class="bi bi-people-fill"></i>
      <span>Utilisateurs</span>
    </a>

    <div class="sidebar-section-label">Système</div>

    <a href="<?= $adminBase ?? '../' ?>../index.php" class="nav-link" target="_blank">
      <i class="bi bi-box-arrow-up-right"></i>
      <span>Voir le site</span>
    </a>

    <a href="<?= $adminBase ?? '../' ?>../login.php" class="nav-link">
      <i class="bi bi-box-arrow-left"></i>
      <span>Déconnexion</span>
    </a>

  </nav>

  <!-- User Footer -->
  <div class="sidebar-footer">
    <div class="sidebar-user">
      <div class="sidebar-user-avatar">A</div>
      <div class="sidebar-user-info">
        <div class="sidebar-user-name">Administrateur</div>
        <div class="sidebar-user-role">Super Admin</div>
      </div>
    </div>
  </div>

</aside>
<!-- End Sidebar -->

<!-- ======= MAIN CONTENT ======= -->
<div class="admin-main">

  <!-- Top Bar -->
  <header class="admin-topbar">
    <!-- Mobile menu button -->
    <button class="topbar-btn mobile-menu-btn" id="mobileMenuBtn" style="display:none;" title="Menu">
      <i class="bi bi-list"></i>
    </button>

    <div class="topbar-title">
      <?= htmlspecialchars($pageTitle ?? 'Dashboard') ?>
    </div>

    <div class="topbar-actions">
      <a href="<?= $adminBase ?? '../' ?>articles/create.php" class="topbar-btn" title="Nouvel article">
        <i class="bi bi-plus-lg"></i>
      </a>
      <button class="topbar-btn has-notif" title="Notifications">
        <i class="bi bi-bell"></i>
      </button>
      <a href="<?= $adminBase ?? '../' ?>../index.php" class="topbar-btn" title="Voir le site" target="_blank">
        <i class="bi bi-globe"></i>
      </a>
    </div>
  </header>
  <!-- End Top Bar -->

  <!-- Page Content starts here — closed in footer.php -->
  <main class="admin-content">
