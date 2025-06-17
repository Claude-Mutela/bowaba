<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
      <?php  
          if(isset($title)){
            echo $title;
          }
          else{
            echo'Bowaba n Congo';
          }?>
    </title>
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
        <h1><a href="index.php"><img src="assets/img/logo/logo-bw.png" alt=""></a></h1>
      </div>

      <nav id="navbar" class="navbar">
        <ul>
          <li><a class="nav-link scrollto <?php if($nav === 'index'):?>active <?php endif; ?>" href="index.php">Accueil</a></li>
          <li><a class="nav-link scrollto <?php if($nav === 'service'):?>active <?php endif; ?>" href="service.php">Services</a></li>
          <li><a class="nav-link scrollto <?php if($nav === 'about'):?>active <?php endif; ?>" href="about.php">Apropos</a></li>
          <li><a class="nav-link scrollto <?php if($nav === 'blog'):?>active <?php endif; ?>" href="blog.php">Blog</a></li>
            <li><a class="nav-link scrollto <?php if($nav === 'fondation'):?>active <?php endif; ?>" href="#">Fondation</a></li>
          <li><a class="nav-link scrollto <?php if($nav === 'contact'):?>active <?php endif; ?>" href="contact.php">Contact</a></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle"></i>
      </nav><!-- .navbar -->

    </div>
  </header><!-- End Header -->


  <script src="assets/js/main.js"></script>
</body>
</html>