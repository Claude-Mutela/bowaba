<?php
    $title='NOS SERVICES';
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
  </section><!-- End Hero -->


     <!-- ======= Services Section ======= -->
     <section id="service" class="services pt-0">
      <div class="container" data-aos="fade-up">

        <div class="section-header">
          <h2>NOS SERVICES</h2>
        </div>
    </section><!-- End Testimonials Section -->

        <div class="container" id="contente">
          <div class="row row-cols-1 row-cols-md-3 g-6">
             <?php
                 $sql_details = $conn->prepare("SELECT nomService, detService FROM bowabanc_db.services WHERE id_Servi=1;");
                 $sql_details->execute();
                 while($plus = $sql_details->fetch(PDO::FETCH_OBJ)){?>

            <div class="col">
              <div class="card">
                <div class="card-img">
                   <img src="assets/img/redaction.jpg" class="img-fluid" alt="Photo du service">
                </div>                
                <div class="card-body">
                   <a href="service-details.php?nomService=<?php  echo $plus->nomService; ?>&detService=<?php  echo $plus->detService; ?>">
                      <h5 ><?php echo $plus->nomService; ?></h5>
                   </a>
                  <p class="card-text"><?php echo substr($plus->detService, 0,200); ?> ...</p>
                
                  <a href="service-details.php?nomService=<?php  echo $plus->nomService; ?>&detService=<?php  echo $plus->detService; ?>">
                  Lire Plus
                </a>
                </div>
              </div>
            </div>
              <?php } ?>
              <?php
                 $sql_details = $conn->prepare("SELECT nomService, detService FROM bowabanc_db.services WHERE id_Servi=2;");
                 $sql_details->execute();
                 while($plus = $sql_details->fetch(PDO::FETCH_OBJ)){?>

            <div class="col">
              <div class="card">
                <div class="card-img">
                   <img src="assets/img/mentorat.jpg" class="img-fluid" alt="Photo du service">
                </div>                
                <div class="card-body">
                   <a href="service-details.php?nomService=<?php  echo $plus->nomService; ?>&detService=<?php  echo $plus->detService; ?>">
                      <h5 ><?php echo $plus->nomService; ?></h5>
                   </a>
                  <p class="card-text"><?php echo substr($plus->detService, 0,200); ?> ...</p>
                
                  <a href="service-details.php?nomService=<?php  echo $plus->nomService; ?>&detService=<?php  echo $plus->detService; ?>">
                      Lire Plus
                   </a>
                </div>
              </div>
            </div>
              <?php } ?>
              <?php
                 $sql_details = $conn->prepare("SELECT nomService, detService FROM bowabanc_db.services WHERE id_Servi=3;");
                 $sql_details->execute();
                 while($plus = $sql_details->fetch(PDO::FETCH_OBJ)){?>

            <div class="col">
              <div class="card">
                <div class="card-img">
                   <img src="assets/img/formation.jpg" class="img-fluid" alt="Photo du service">
                </div>                
                <div class="card-body">
                   <a href="service-details.php?nomService=<?php  echo $plus->nomService; ?>&detService=<?php  echo $plus->detService; ?>">
                      <h5 ><?php echo $plus->nomService; ?></h5>
                   </a>
                  <p class="card-text"><?php echo substr($plus->detService, 0,200); ?> ...</p>
                  
                  <a href="service-details.php?nomService=<?php  echo $plus->nomService; ?>&detService=<?php  echo $plus->detService; ?>">
                      Lire Plus
                   </a>
                </div>
              </div>
            </div>
              <?php } ?>
              <?php
                 $sql_details = $conn->prepare("SELECT nomService, detService FROM bowabanc_db.services WHERE id_Servi=4;");
                 $sql_details->execute();
                 while($plus = $sql_details->fetch(PDO::FETCH_OBJ)){?>

            <div class="col">
              <div class="card">
                <div class="card-img">
                   <img src="assets/img/suivi.jpg" class="img-fluid" alt="Photo du service">
                </div>                
                <div class="card-body">
                   <a href="service-details.php?nomService=<?php  echo $plus->nomService; ?>&detService=<?php  echo $plus->detService; ?>">
                      <h5 ><?php echo $plus->nomService; ?></h5>
                   </a>
                  <p class="card-text"><?php echo substr($plus->detService, 0,200); ?> ...</p>
                  
                  <a href="service-details.php?nomService=<?php  echo $plus->nomService; ?>&detService=<?php  echo $plus->detService; ?>">
                     Lire Plus
                   </a>
                </div>
              </div>
            </div>
              <?php } ?>
              <?php
                 $sql_details = $conn->prepare("SELECT nomService, detService FROM bowabanc_db.services WHERE id_Servi=5;");
                 $sql_details->execute();
                 while($plus = $sql_details->fetch(PDO::FETCH_OBJ)){?>

            <div class="col">
              <div class="card">
                <div class="card-img">
                   <img src="assets/img/comptable.jpg" class="img-fluid" alt="Photo du service">
                </div>                
                <div class="card-body">
                   <a href="service-details.php?nomService=<?php  echo $plus->nomService; ?>&detService=<?php  echo $plus->detService; ?>">
                      <h5 ><?php echo $plus->nomService; ?></h5>
                   </a>
                  <p class="card-text"><?php echo substr($plus->detService, 0,200); ?> ...</p>
                 
                    <a href="service-details.php?nomService=<?php  echo $plus->nomService; ?>&detService=<?php  echo $plus->detService; ?>">
                        Lire Plus
                    </a>
                </div>
              </div>
            </div>
              <?php } ?>
              <?php
                 $sql_details = $conn->prepare("SELECT nomService, detService FROM bowabanc_db.services WHERE id_Servi=6;");
                 $sql_details->execute();
                 while($plus = $sql_details->fetch(PDO::FETCH_OBJ)){?>

            <div class="col">
              <div class="card">
                <div class="card-img">
                   <img src="assets/img/dev.jpg" class="img-fluid" alt="Photo du service">
                </div>                
                <div class="card-body">
                   <a href="service-details.php?nomService=<?php  echo $plus->nomService; ?>&detService=<?php  echo $plus->detService; ?>">
                      <h5 ><?php echo $plus->nomService; ?></h5>
                   </a>
                  <p class="card-text"><?php echo substr($plus->detService, 0,200); ?> ...</p>
             
                    <a href="service-details.php?nomService=<?php  echo $plus->nomService; ?>&detService=<?php  echo $plus->detService; ?>">
                        Lire Plus
                    </a>
                </div>
              </div>
            </div>
              <?php } ?>
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