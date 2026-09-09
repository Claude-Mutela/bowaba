<?php
require_once __DIR__ . '/../kon/config.php';

// ── 1. SEO & Titres ──────────────────────────────────────────────────────────
$siteName = 'Bowaba n Congo';
if (!empty($pageTitle)) {
    $seoTitle = (strpos($pageTitle, 'Bowaba') !== false) ? $pageTitle : $pageTitle . ' - ' . $siteName;
} else {
    $seoTitle = $siteName . ' - 1er Incubateur & Academy multidisciplinaire en RDC';
}

// ── 2. Meta Description (nettoyée, sans balises HTML, max 200 caractères) ────
$rawDesc = !empty($pageDesc) ? $pageDesc : 'Bowaba n Congo est le 1er incubateur & academy multidisciplinaire en RDC : entrepreneuriat, formation professionnelle, agrobusiness, solutions digitales et accompagnement de projets.';
$cleanDesc = trim(preg_replace('/\s+/', ' ', strip_tags($rawDesc)));
$descLen = function_exists('mb_strlen') ? mb_strlen($cleanDesc) : strlen($cleanDesc);
if ($descLen > 200) {
    $cleanDesc = (function_exists('mb_substr') ? mb_substr($cleanDesc, 0, 197) : substr($cleanDesc, 0, 197)) . '...';
}

$keywords = !empty($pageKeywords) ? $pageKeywords : 'Incubateur, Bowaba n Congo, RDC, Kinshasa, Formation professionnelle, Agrobusiness, Coaching MPME, Création de sites web, Design graphique, Suivi et évaluation de projets';

// ── 3. Canonical URL & Open Graph URL ─────────────────────────────────────────
if (empty($pageUrl)) {
    $reqUri = $_SERVER['REQUEST_URI'] ?? '/';
    $canonicalUrl = rtrim(BASE_URL, '/') . '/' . ltrim($reqUri, '/');
} else {
    $canonicalUrl = (strpos($pageUrl, 'http://') === 0 || strpos($pageUrl, 'https://') === 0)
        ? $pageUrl
        : rtrim(BASE_URL, '/') . '/' . ltrim($pageUrl, '/');
}

// ── 4. Open Graph Image (Article Image ou Logo Bowaba par défaut) ─────────────
$defaultLogoRel = 'assets/img/logo/logo-bw.png';
$targetImage = !empty($pageImage) ? $pageImage : $defaultLogoRel;

$imgWidth  = 1200;
$imgHeight = 630;
$imgMime   = 'image/jpeg';
$imagePathOnDisk = null;

if (strpos($targetImage, 'http://') === 0 || strpos($targetImage, 'https://') === 0) {
    $ogImageUrl = $targetImage;
} else {
    $cleanRel = ltrim($targetImage, '/');
    $ogImageUrl = rtrim(BASE_URL, '/') . '/' . $cleanRel;
    $imagePathOnDisk = __DIR__ . '/../' . $cleanRel;
}

// Si le fichier existe localement sur le disque, on extrait ses dimensions réelles
if ($imagePathOnDisk && file_exists($imagePathOnDisk)) {
    $imgInfo = @getimagesize($imagePathOnDisk);
    if ($imgInfo) {
        $imgWidth  = $imgInfo[0];
        $imgHeight = $imgInfo[1];
        $imgMime   = $imgInfo['mime'] ?? 'image/jpeg';
    }
} elseif (strpos($ogImageUrl, '.png') !== false) {
    $imgMime = 'image/png';
    $imgWidth  = 2640;
    $imgHeight = 1004;
} elseif (strpos($ogImageUrl, '.webp') !== false) {
    $imgMime = 'image/webp';
}

$ogType = !empty($ogType) ? $ogType : 'website';
$ogImageAlt = $seoTitle;
$nav = $nav ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <base href="<?= BASE_URL ?>">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="<?= htmlspecialchars($cleanDesc, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="keywords" content="<?= htmlspecialchars($keywords, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="author" content="Bowaba n Congo">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8') ?>">

    <!-- Favicons -->
    <link rel="icon" type="image/png" href="assets/img/icone-bw.png">
    <link rel="apple-touch-icon" href="assets/img/icone-bw.png">

    <!-- Open Graph / Facebook / WhatsApp / LinkedIn -->
    <meta property="og:site_name" content="<?= htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:locale" content="fr_FR">
    <meta property="og:type" content="<?= htmlspecialchars($ogType, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:title" content="<?= htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($cleanDesc, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:image" content="<?= htmlspecialchars($ogImageUrl, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:image:secure_url" content="<?= htmlspecialchars($ogImageUrl, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:image:type" content="<?= htmlspecialchars($imgMime, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:image:width" content="<?= (int)$imgWidth ?>">
    <meta property="og:image:height" content="<?= (int)$imgHeight ?>">
    <meta property="og:image:alt" content="<?= htmlspecialchars($ogImageAlt, ENT_QUOTES, 'UTF-8') ?>">
<?php if ($ogType === 'article' && !empty($articleData)): ?>
<?php if (!empty($articleData['published_at'])): ?>
    <meta property="article:published_time" content="<?= htmlspecialchars(date('c', strtotime($articleData['published_at'])), ENT_QUOTES, 'UTF-8') ?>">
<?php endif; ?>
<?php if (!empty($articleData['author_name'])): ?>
    <meta property="article:author" content="<?= htmlspecialchars($articleData['author_name'], ENT_QUOTES, 'UTF-8') ?>">
<?php endif; ?>
<?php if (!empty($articleData['category_name'])): ?>
    <meta property="article:section" content="<?= htmlspecialchars($articleData['category_name'], ENT_QUOTES, 'UTF-8') ?>">
<?php endif; ?>
<?php if (!empty($articleData['tags']) && is_array($articleData['tags'])): ?>
<?php foreach ($articleData['tags'] as $t): 
    $tName = is_array($t) ? ($t['name'] ?? '') : $t;
    if ($tName): ?>
    <meta property="article:tag" content="<?= htmlspecialchars($tName, ENT_QUOTES, 'UTF-8') ?>">
<?php endif; endforeach; ?>
<?php endif; ?>
<?php endif; ?>

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@bowabancongo">
    <meta name="twitter:title" content="<?= htmlspecialchars($seoTitle, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($cleanDesc, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:image" content="<?= htmlspecialchars($ogImageUrl, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:image:alt" content="<?= htmlspecialchars($ogImageAlt, ENT_QUOTES, 'UTF-8') ?>">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Core Template CSS (Topbar, Header, Navbar, Footer, Base Elements) -->
    <link href="assets/css/global.css" rel="stylesheet">

    <!-- Inline Header Logo Safeguard -->
    <style>
      #header .logo img {
        max-height: 55px;
        width: auto;
        max-width: 220px;
        object-fit: contain;
        display: block;
      }
      @media (max-width: 991px) {
        #header .logo img {
          max-height: 45px;
          max-width: 180px;
        }
      }
    </style>

    <!-- Page Specific CSS -->
<?php if (!empty($pageCss) && $pageCss !== 'assets/css/global.css'): ?>
    <link href="<?= htmlspecialchars($pageCss, ENT_QUOTES, 'UTF-8') ?>" rel="stylesheet">
<?php endif; ?>
</head>
<body>

  <!-- ======= Top Bar ======= -->
  <section id="topbar" class="d-flex align-items-center">
    <div class="container d-flex justify-content-center justify-content-md-between">
      <div class="contact-info d-flex align-items-center">
        <i class="bi bi-envelope d-flex align-items-center"><a href="mailto:contact@bowabancongo.com">contact@bowabancongo.com</a></i>
        <i class="bi bi-phone d-flex align-items-center ms-4"><span> +243 816 695 000 </span></i>
      </div>
    </div>
  </section>

  <!-- ======= Header ======= -->
  <header id="header" class="d-flex align-items-center">
    <div class="container d-flex align-items-center justify-content-between">

      <div class="logo">
        <a href="index"><img src="assets/img/logo/logo-bw.png" alt="Bowaba n Congo Logo"></a>
      </div>

      <nav id="navbar" class="navbar">
        <ul>
          <li><a class="nav-link scrollto <?= ($nav === 'index') ? 'active' : '' ?>" href="index">Accueil</a></li>
          <li><a class="nav-link scrollto <?= ($nav === 'about') ? 'active' : '' ?>" href="about">À propos</a></li>
          <li><a class="nav-link scrollto <?= ($nav === 'service' || $nav === 'services' || $nav === 'details-service') ? 'active' : '' ?>" href="services">Services</a></li>
          <li><a class="nav-link scrollto <?= ($nav === 'blog') ? 'active' : '' ?>" href="blog">Blog</a></li>
          <li><a class="nav-link scrollto <?= ($nav === 'fondation') ? 'active' : '' ?>" href="fondation/" target="_blank">Fondation</a></li>
          <li><a class="nav-link scrollto <?= ($nav === 'contact') ? 'active' : '' ?>" href="contact">Contact</a></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav><!-- .navbar -->

    </div>
  </header><!-- End Header -->