<?php
    session_start();
    $title='CONTACTEZ-NOUS';
    $nav='contact';
    require'hd-ft/hd.php';
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>

    <meta content="" name="description">
  <meta content="" name="keywords">

 <!-- Favicons -->
 <link href="assets/img/icone-bw.png" rel="icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!--  CSS Files -->
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!--  CSS File -->
  <link href="assets/css/style-contact.css" rel="stylesheet">
</head>
<body>

      <!-- ======= Hero Section ======= -->
  <section id="hero">
    <div class="hero-container">
      <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">

        <ol class="carousel-indicators" id="hero-carousel-indicators"></ol>

        <div class="carousel-inner" role="listbox">

          <!-- Slide 1 -->
          <div class="carousel-item active" style="background-image: url('assets/img/carte.jpeg');">
            <div class="carousel-container">
              <div class="carousel-content container">
                <h2 class="animate__animated animate__fadeInDown"><span>Contactez-vous</span></h2>
                <!-- <p class="animate__animated animate__fadeInUp">S'en ai pour nous un plaisir...</p> -->
              </div>
            </div>
          </div>
  </section><!-- End Hero -->
  <!-- ======= Contact Section ======= -->
  <section id="contact" class="contact">
      <div class="container">

        <div class="section-title">
          <h2 data-aos="fade-up">Contact</h2>
          <p data-aos="fade-up">Besoin d'entrer directement en contact avec nous? <br> Choissez par quel moyen le faire à travers les dfifférents moyens réportoriés ci-dessous</p>
        </div>

        <div class="row justify-content-center">

          <div class="col-xl-3 col-lg-4 mt-4" data-aos="fade-up">
            <div class="info-box">
              <i class="bx bx-map"></i>
              <h3>Notre Adresse</h3>
              <p>
                01, Avenue LUAMBO-MAKIADI, Gallerie ATTOUÉ, <br>
                local 307, Kin-Mazière, Commune de la Gombe. 
              </p>
            </div>
          </div>

          <div class="col-xl-3 col-lg-4 mt-4" data-aos="fade-up" data-aos-delay="100">
            <div class="info-box">
              <i class="bx bx-envelope"></i>
              <h3>Notre Mail</h3>
              <p><a href="mailto:contact@bowaba.com.com">contact@bowabancongo.com</a></p>
              <p><a href="mailto:contact@bowaba.com.com">ceo@bowabancongo.com</a></p>
            </div>
          </div>
          <div class="col-xl-3 col-lg-4 mt-4" data-aos="fade-up" data-aos-delay="200">
            <div class="info-box">
              <i class="bx bx-phone-call"></i>
              <h3>Appelez-nous au:</h3>
              <p>
                <a href="tel:243816695000 ">+243 816 695 000</a>
              </p>

            </div>
          </div>
        </div>

        <div class="row justify-content-center" data-aos="fade-up" data-aos-delay="300">
          
          <div class="col-xl-9 col-lg-12 mt-4">
             <!-- Gestion des erreurs stockées dans la  $_session -->
          <?php if(array_key_exists('errors', $_SESSION)): ?>
                <div class="alert alert-danger">
                    <?= implode('<br>', $_SESSION['errors']); ?>
                </div>
                
            <?php unset($_SESSION['errors']); endif; ?>
            
          
            <?php if(array_key_exists('success', $_SESSION)): ?>
                <div class="alert alert-success">
                   Votre Email a bien été envoyé !
                </div>
                
            <?php unset($_SESSION['success']); endif; ?>

            <form action="post-contact.php" method="post" role="form">
              <div class="row">
                <div class="col-md-6 form-group">
                  <input type="text" name="name" class="form-control" id="name" placeholder="Votre Nom et Prénom" required value="<?= isset($_SESSION['inputs']) ? $_SESSION['inputs']['name'] : ''; ?>">
                </div>
                <div class="col-md-6 form-group mt-3 mt-md-0">
                  <input type="email" class="form-control" name="email" id="email" placeholder="Email" required value="<?= isset($_SESSION['inputs']) ? $_SESSION['inputs']['email'] : ''; ?>">
                </div>
              </div>
              <div class="form-group mt-3">
                <textarea class="form-control" name="message" rows="5" placeholder="Votre Message" required>
                  <?= isset($_SESSION['inputs']) ? $_SESSION['inputs']['message'] : ''; ?>
                </textarea>
              </div>
              <div class="text-center">
                <button type="submit">Envoyez votre message</button>
              </div>
            </form>

      </div>
    </section><!-- End Contact Section -->


  <!-- JS Files -->
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!--  JS File -->
  <script src="assets/js/main.js"></script>
  <script src="assets/js/c_main.js"></script></body>
<?php
    require'hd-ft/ft.php';
?>
</html>
<?php 
//Netoyage des sessions
unset($_SESSION['inputs']);
unset($_SESSION['success']);
unset($_SESSION['errors']);

?>  