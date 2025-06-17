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
    
     <!-- ======= Breadcrumbs ======= -->
    <div class="top-bloc">
      <div class="content">
      <ol>
          <li><a href="index.php">Accueil</a>   /</li>
          <li><a href="service.php">Retour aux Services</a></li>
        </ol>
        <h2></h2>
      </div>
  </div>
    <!-- =======Contenu du service ======= -->

    <section class="description_service">
       <div class="container-sm" id="container">
      <div class="box" id="content">
        <div class="card" id="card">
          <div class="card-body">
            <h5 class="card-title"><?php echo $service_name;?></h5>
              <p class="card-text"><?php echo $service_desc;?></p>
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