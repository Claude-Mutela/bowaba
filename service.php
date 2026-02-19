<?php
    $pageTitle='Nos Services - Bowaba n Congo';
    $pageDesc='Découvrez l\'ensemble des services proposés par Bowaba n Congo : Incubateur, Formation, Développement Web, Marketing, et plus.';
    $nav='service';
    require'hd-ft/hd.php';
    require'kon/conn.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Services - Bowaba</title>

    <meta content="Services, Incubateur, Formation, Web, Design, RDC" name="keywords">

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
  <link href="assets/css/service.css" rel="stylesheet">
</head>
<body>
    
      <!-- ======= Hero Section ======= -->
  <section id="hero">
    <div class="hero-container">
      <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">

        <ol class="carousel-indicators" id="hero-carousel-indicators"></ol>

        <div class="carousel-inner" role="listbox">

          <!-- Slide 1 -->
          <div class="carousel-item active" style="background-image: url('assets/img/slide/slide-2.png');">
            <div class="carousel-container">
              <div class="carousel-content container">
                <h2 class="animate__animated animate__fadeInDown"><span>Nos Services</span></h2>
                <p class="animate__animated animate__fadeInUp">Nous mettons à votre disposition toute une gamme de services pour la réussite de vos projets</p>
              </div>
            </div>
          </div>
          </div>
  </section><!-- End Hero -->


     <!-- ======= Services Section ======= -->
     <section id="service" class="services pt-0">
      <div class="container" data-aos="fade-up">

        <div class="section-header">
          <h2>NOS SERVICES</h2>
        </div>
    </section><!-- End Testimonials Section -->

        <div class="container" id="contente">
          <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
             <?php
                 // Service Data Fetching
                 try {
                     $stmt = $conn->query("SELECT * FROM services ORDER BY display_order ASC, id ASC");
                     $services = $stmt->fetchAll(PDO::FETCH_OBJ);
                 } catch (PDOException $e) {
                     echo "<div class='alert alert-danger'>Erreur de chargement des services : " . htmlspecialchars($e->getMessage()) . "</div>";
                     $services = [];
                 }

                 // Fallback Image Mapping (in case DB images are empty)
                 $serviceImages = [
                     1 => 'assets/img/redaction.jpg',
                     2 => 'assets/img/mentorat.jpg',
                     3 => 'assets/img/formation.jpg',
                     4 => 'assets/img/suivi.jpg',
                     5 => 'assets/img/comptable.jpg',
                     6 => 'assets/img/dev.jpg',
                 ];
                 
                 foreach($services as $service): 
                    // Use DB image if exists, otherwise fallback to mapping or default
                    if (!empty($service->image) && file_exists($service->image)) {
                        $imgSrc = $service->image;
                    } elseif (isset($serviceImages[$service->id])) {
                        $imgSrc = $serviceImages[$service->id];
                    } else {
                        $imgSrc = 'assets/img/hero-bg.jpg'; // Ultimate fallback
                    }

                    // Content preparation
                    $title = $service->title;
                    $description = $service->description ?? ''; // Use description for summary
                    // If description is empty, try truncating content
                    if (empty($description) && !empty($service->content)) {
                        $description = substr(strip_tags($service->content), 0, 150);
                    }
                    
                    // Link to details (using slug)
                    $slug = $service->slug ?? 'service-' . $service->id; // Fallback slug if missing
                    $link = "service/" . $slug;
                 ?>

            <div class="col">
              <div class="service-card h-100">
                <div class="service-img-wrapper">
                   <img src="<?= htmlspecialchars($imgSrc) ?>" class="img-fluid" alt="<?= htmlspecialchars($title) ?>">
                   <div class="overlay">
                       <a href="<?= $link ?>" class="btn-read-more">Découvrir <i class="bi bi-arrow-right"></i></a>
                   </div>
                </div>                
                <div class="card-body">
                   <h3 class="service-title">
                       <a href="<?= $link ?>"><?= htmlspecialchars($title) ?></a>
                   </h3>
                   <p class="service-text">
                       <?= htmlspecialchars(substr($description, 0, 120)) ?>...
                   </p>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0">
                    <a href="<?= $link ?>" class="text-primary fw-bold text-decoration-none learn-more-link">
                        En savoir plus <i class="bi bi-chevron-right small"></i>
                    </a>
                </div>
              </div>
            </div>
              <?php endforeach; ?>
          </div>
        </div>

  <!-- JS Files -->
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!--  JS File -->
  <script src="assets/js/main.js"></script>
  <script src="assets/js/c_main.js"></script>
</body>
<?php
    require'hd-ft/ft.php';
?>
</html>