<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once __DIR__ . '/../kon/config.php'; ?>
    <base href="<?= BASE_URL ?>">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
      <?php if(isset($pageTitle)) { echo htmlspecialchars($pageTitle) . ' - Bowaba'; } else { echo 'Bowaba n Congo'; } ?>
    </title>
    <meta content="<?= htmlspecialchars($pageDesc ?? 'Bowaba n Congo - Entreprise de services et solutions digitales.') ?>" name="description">
    <meta content="Bowaba, Congo, Digital, Services, Formation, Web" name="keywords">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= htmlspecialchars($pageUrl ?? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>">
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle ?? 'Bowaba n Congo') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDesc ?? 'Bowaba n Congo - Entreprise de services et solutions digitales.') ?>">
    <?php if(isset($pageImage) && !empty($pageImage)): ?>
    <meta property="og:image" content="<?= (strpos($pageImage, 'http') === 0) ? $pageImage : (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/bowaba/" . $pageImage ?>">
    <?php else: ?>
    <meta property="og:image" content="<?= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/bowaba/assets/img/logo/logo-bw.png" ?>">
    <?php endif; ?>

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?= htmlspecialchars($pageUrl ?? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>">
    <meta property="twitter:title" content="<?= htmlspecialchars($pageTitle ?? 'Bowaba n Congo') ?>">
    <meta property="twitter:description" content="<?= htmlspecialchars($pageDesc ?? 'Bowaba n Congo - Entreprise de services et solutions digitales.') ?>">
    <?php if(isset($pageImage) && !empty($pageImage)): ?>
    <meta property="twitter:image" content="<?= (strpos($pageImage, 'http') === 0) ? $pageImage : (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/bowaba/" . $pageImage ?>">
    <?php endif; ?>
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
        <h1><a href="index.php"><img src="assets/img/logo/logo-bw.png" alt=""></a></h1>
      </div>

      <nav id="navbar" class="navbar">
        <ul>
          <li><a class="nav-link scrollto <?php if($nav === 'index'):?>active <?php endif; ?>" href="index.php">Accueil</a></li>
          <li><a class="nav-link scrollto <?php if($nav === 'about'):?>active <?php endif; ?>" href="about.php">Apropos</a></li>
          <li><a class="nav-link scrollto <?php if($nav === 'service'):?>active <?php endif; ?>" href="service.php">Services</a></li>
          <li><a class="nav-link scrollto <?php if($nav === 'blog'):?>active <?php endif; ?>" href="blog.php">Blog</a></li>
          <li><a class="nav-link scrollto <?php if($nav === 'fondation'):?>active <?php endif; ?>" href="https://fondation.bowabancongo.com/" target="_blank">Fondation</a></li>
          <li><a class="nav-link scrollto <?php if($nav === 'contact'):?>active <?php endif; ?>" href="contact.php">Contact</a></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav><!-- .navbar -->

    </div>
  </header><!-- End Header -->


  <script src="assets/js/main.js"></script>
</body>
</html>