<?php
    require 'kon/conn.php';

    // Get Slug
    $slug = filter_input(INPUT_GET, 'slug', FILTER_SANITIZE_SPECIAL_CHARS);
    
    // Fetch Service
    $service = null;
    if ($slug) {
        $stmt = $conn->prepare("SELECT * FROM services WHERE slug = :slug AND status = 'active' LIMIT 1");
        $stmt->execute([':slug' => $slug]);
        $service = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 404 if not found
    if (!$service) {
        // Fallback: try ID if slug fails (for backward compatibility)
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if($id){
             $stmt = $conn->prepare("SELECT slug FROM services WHERE id=:id AND status='active'");
             $stmt->execute([':id'=>$id]);
             $found = $stmt->fetchColumn();
             if($found){
                 header("Location: service/" . $found, true, 301);
                 exit;
             }
        }
        
        header("HTTP/1.0 404 Not Found");
        require 'hd-ft/hd.php'; // Load header to show navbar even on 404
        echo '<div class="container my-5 text-center"><h1>Service introuvable</h1><p>Ce service n\'existe pas ou a été retiré.</p><a href="service.php" class="btn btn-primary">Retour aux services</a></div>';
        require 'hd-ft/ft.php';
        exit;
    }

    // Meta Tags
    $title = $service['title'];
    $pageTitle = $service['title'];
    $pageDesc  = $service['description'];
    $pageImage = $service['image']; // For OG
    $nav='details-service';
    
    require'hd-ft/hd.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($service['title']) ?> - Bowaba</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!--  CSS Files -->
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Main CSS  -->
    <link href="assets/css/detail-service.css" rel="stylesheet">
</head>
<body>
    
    <!-- ======= Breadcrumbs Strip ======= -->
    <div class="top-bloc small-header">
      <div class="content">
        <!-- Desktop: Breadcrumb complet avec titre -->
        <ol class="d-none d-md-flex">
          <li><a href="index.php">Accueil</a> /</li>
          <li><a href="service.php">Services</a> /</li>
          <li class="active"><?= htmlspecialchars($service['title']) ?></li>
        </ol>
        
        <!-- Mobile: Lien retour simple sans titre -->
        <div class="d-md-none">
            <a href="service.php" style="color: #fff; font-weight: 600; text-decoration: none; display: flex; align-items: center;">
                <i class="bx bx-left-arrow-alt fs-4 me-2"></i> Retour aux services
            </a>
        </div>
      </div>
    </div>

    <!-- ======= Contenu du service ======= -->
    <section class="service-details-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="service-content-wrapper">
                        <!-- Icone ou visuel décoratif -->
                        <div class="service-icon mb-4 text-center">
                           <?php if($service['icon']): ?>
                               <i class="<?= htmlspecialchars($service['icon']) ?> fs-1 text-primary"></i>
                           <?php else: ?>
                               <i class="bx bx-layer fs-1 text-primary"></i> 
                           <?php endif; ?>
                        </div>

                        <?php if($service['image']): ?>
                        <div class="service-image mb-4 text-center">
                            <img src="<?= htmlspecialchars($service['image']) ?>" alt="<?= htmlspecialchars($service['title']) ?>" class="img-fluid rounded shadow-sm">
                        </div>
                        <?php endif; ?>
                        
                        <div class="content-body">
                            <h1 class="text-center mb-4"><?= htmlspecialchars($service['title']) ?></h1>

                            <?php if($service['description']): ?>
                                <div class="lead mb-4 service-intro text-center">
                                    <?= nl2br(htmlspecialchars($service['description'])) ?>
                                </div>
                            <?php endif; ?>

                            <?php if($service['content']): ?>
                                <div class="service-text-content">
                                    <?= $service['content'] // Raw HTML from TinyMCE ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- CTA Section -->
                        <div class="service-cta mt-5 p-4 bg-light rounded-3 text-center">
                            <h3>Intéressé par ce service ?</h3>
                            <p>Contactez-nous pour en discuter ou obtenir un devis personnalisé.</p>
                            <a href="contact.php" class="btn btn-primary btn-lg mt-2">Nous contacter</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

  <!-- JS Files -->
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <script src="assets/js/main.js"></script>
  <script src="assets/js/c_main.js"></script>
</body>
<?php
    require'hd-ft/ft.php';
?>
</html>