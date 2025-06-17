<?php
    $title='BLOG';
    $nav='blog';
    require'hd-ft/hd.php';
    require'kon/conn.php';

    $sql_select_blog = $conn->prepare("SELECT * FROM article LIMIT 5");
    $sql_select_blog->execute();

    $recent_post = $conn->prepare("SELECT * FROM article GROUP BY id_art DESC LIMIT 5");
    $recent_post->execute();



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

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!--  CSS Files -->
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/blog.css" rel="stylesheet">
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
                <h2 class="animate__animated animate__fadeInDown"><span>Nos Articles</span></h2>
              </div>
            </div>
          </div>
  </section><!-- End Hero -->

    <main id="main">
    <!-- ======= Blog Section ======= -->
    <section id="blog" class="blog">
      <div class="container" data-aos="fade-up">
          
        <div class="row">
        
          <div class="col-lg-8 entries">

          <?php  while($articles = $sql_select_blog->fetch(PDO::FETCH_OBJ)){?>

          <?php  $d_date = strtotime($articles->date_art);?>

            <article class="entry">

              <div class="entry-img">
                <img src="adm/actions/image/<?php echo $articles->photo_art; ?>" alt="" class="img-fluid">
              </div>

              <h2 class="entry-title">
                <a href="blog-single.php?titre_art=<?php  echo $articles->titre_art; ?>&auteur=<?php  echo $articles->auteur; ?>&p_art1=<?php echo nl2br(htmlentities($articles->p_art1)); ?>&photo_art=<?php echo $articles->photo_art; ?>&date_art=<?php echo $articles->date_art; ?>"><?php echo nl2br(htmlentities($articles->titre_art)); ?></a>
              </h2>

              <div class="entry-meta">
                <ul>
                  <li class="d-flex align-items-center"><i class="bi bi-person"></i> <a href="blog-single.html"><?php echo $articles->auteur; ?></a></li>
                  <li class="d-flex align-items-center"><i class="bi bi-clock"></i> <a href="blog-single.html"><time datetime="2020-01-01"><?php echo date('d-m-Y h:i', $d_date);?></time></a></li>
                  <li class="d-flex align-items-center"><i class="bi bi-chat-dots"></i> <a href="blog-single.html">12 Commentaires</a></li>
                </ul>
              </div>

              <div class="entry-content">
                <p>
                  <?php echo nl2br(htmlentities(substr($articles->p_art1, 0,370))); ?>  ...
                </p>
                <div class="read-more">
                  <a href="blog-single.php?titre_art=<?php  echo $articles->titre_art; ?>&auteur=<?php  echo $articles->auteur; ?>&p_art1=<?php echo $articles->p_art1; ?>&photo_art=<?php echo $articles->photo_art; ?>&date_art=<?php echo $articles->date_art; ?>">Lire plus</a>
                </div>
              </div>

            </article><!-- End blog entry -->
            <?php } ?>

            <div class="blog-pagination">
              <ul class="justify-content-center">
                <li><a href="#">1</a></li>
                <li class="active"><a href="#">2</a></li>
                <li><a href="#">3</a></li>
              </ul>
            </div>

          </div><!-- End blog entries list -->

          <div class="col-lg-4">

            <div class="sidebar">
              <h3 class="sidebar-title">Articles Récents</h3>
              <div class="sidebar-item recent-posts">

              <?php  while($article = $recent_post->fetch(PDO::FETCH_OBJ)){?>

              <?php  $d_dat = strtotime($article->date_art);?>

                <div class="post-item clearfix">
                  <img src="adm/actions/image/<?php echo $article->photo_art; ?>" alt="">
                  <h4><a href="blog-single.php?titre_art=<?php  echo $article->titre_art; ?>&auteur=<?php  echo $article->auteur; ?>&p_art1=<?php echo $article->p_art1; ?>&photo_art=<?php echo $article->photo_art; ?>&date_art=<?php echo $article->date_art; ?>"><?php echo $article->titre_art; ?></a></h4>
                  <time datetime="2020-01-01"><?php echo date('d-m-Y h:i', $d_dat);?></time>
                </div>
              <?php };?>
               

              </div><!-- End sidebar recent posts-->
            </div><!-- End sidebar -->

          </div><!-- End blog sidebar -->

        </div>

      </div>
    </section><!-- End Blog Section -->

    </main><!-- End #main -->



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