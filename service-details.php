<?php
    $title='DETAILS SERVICE';
    $nav='details-service';
    require'hd-ft/hd.php';

    $service_name = $_GET['nomService'];
    $service_desc = $_GET['detService'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>

    <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/icone-bw.png" rel="icon">
  <link href="assets/img/icone-bw.png" rel="apple-touch-icon">

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
          <li class="active"><?= htmlspecialchars($service_name) ?></li>
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
                        <!-- Icone ou visuel décoratif (optionnel) -->
                        <div class="service-icon mb-4 text-center">
                           <i class="bx bx-layer fs-1 text-primary"></i> 
                        </div>
                        
                        <div class="content-body">
                            <!-- Affichage du contenu brut (attention : XSS si non sécurisé, mais nécessaire pour le rendu HTML) -->
                            <?php if($service_desc): ?>
                                <div class="lead mb-4 service-text-content">
                                    <?= $service_desc ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">Aucune description disponible pour ce service.</p>
                            <?php endif; ?>
                        </div>

                        <!-- CTA Section -->
                        <div class="service-cta mt-5 p-4 bg-light rounded-3 text-center">
                            <h3>Intéressé par ce service ?</h3>
                            <p>Contactez-nous pour en discuter ou obtenir un devis personnalisé.</p>
                            <a href="contact.php" class="btn btn-primary btn-lg mt-2">Nous contacter</a>
                            <!-- Back button removed as requested -->
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