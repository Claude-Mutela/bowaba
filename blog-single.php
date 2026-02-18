<?php
  require 'kon/conn.php'; // DB Connection

  // 1. Get Article ID
  $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
  if (!$id) {
      header("Location: blog.php");
      exit;
  }

  // 2. Fetch Article + Author + Category
  $stmt = $conn->prepare("
      SELECT a.*, u.name as author_name, c.name as category_name, c.slug as category_slug
      FROM articles a
      LEFT JOIN users u ON a.user_id = u.id
      LEFT JOIN article_categories c ON a.category_id = c.id
      WHERE a.id = :id AND (a.status = 'published' OR a.status IS NULL)
      LIMIT 1
  ");
  $stmt->execute([':id' => $id]);
  $article = $stmt->fetch(PDO::FETCH_ASSOC);

  // 404 if not found
  if (!$article) {
      header("HTTP/1.0 404 Not Found");
      echo "Article introuvable.";
      exit;
  }

  // 3. Fetch Tags
  $tagsStmt = $conn->prepare("
      SELECT t.name, t.slug 
      FROM tags t
      JOIN article_tags at ON at.tag_id = t.id
      WHERE at.article_id = :id
  ");
  $tagsStmt->execute([':id' => $id]);
  $tags = $tagsStmt->fetchAll(PDO::FETCH_ASSOC);

  // 4. Update View Count (optional, simple increment)
  $conn->prepare("UPDATE articles SET views_count = views_count + 1 WHERE id = :id")->execute([':id' => $id]);

  // Page Metas
  $title = $article['title'];
  require 'hd-ft/hd.php'; 
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title><?= htmlspecialchars($article['title']) ?> - Bowaba</title>
  <meta content="<?= htmlspecialchars($article['excerpt'] ?? '') ?>" name="description">

  <!-- Favicons -->
  <link href="assets/img/icone-bw.png" rel="icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/signle-blog.css" rel="stylesheet">

</head>

<body>
  <main id="main">

    <!-- ======= Breadcrumbs ======= -->
    <div class="top-bloc">
      <div class="content">
      <ol>
          <li><a href="index.php">Accueil</a> /</li>
          <li><a href="blog.php">Blog</a></li>
        </ol>
        <h2><?= htmlspecialchars($article['title']) ?></h2>
      </div>
    </div>
    <!-- ======= Blog Single Section ======= -->
    <section id="blog" class="blog">
      <div class="container" data-aos="fade-up">

        <div class="row">

          <div class="col-lg-8 entries">

            <article class="entry entry-single">

              <?php if (!empty($article['cover_image'])): ?>
              <div class="entry-img">
                <img src="<?= htmlspecialchars($article['cover_image']) ?>" alt="" class="img-fluid">
              </div>
              <?php endif; ?>

              <h2 class="entry-title">
                <?= htmlspecialchars($article['title']) ?>
              </h2>

              <div class="entry-meta">
                <ul>
                  <li class="d-flex align-items-center"><i class="bi bi-person"></i> <a href="#"><?= htmlspecialchars($article['author_name'] ?? 'Admin') ?></a></li>
                  <li class="d-flex align-items-center"><i class="bi bi-clock"></i> <a href="#"><time datetime="<?= $article['published_at'] ?>"><?= date('d M, Y', strtotime($article['published_at'])) ?></time></a></li>
                  <li class="d-flex align-items-center"><i class="bi bi-eye"></i> <?= $article['views_count'] ?> Vues</li>
                </ul>
              </div>

              <div class="entry-content">
                <?= $article['content'] // Raw HTML from TinyMCE ?>        
              </div>

              <div class="entry-footer">
                <?php if ($article['category_name']): ?>
                <i class="bi bi-folder"></i>
                <ul class="cats">
                  <li><a href="blog.php?category=<?= $article['category_slug'] ?>"><?= htmlspecialchars($article['category_name']) ?></a></li>
                </ul>
                <?php endif; ?>

                <?php if (!empty($tags)): ?>
                <i class="bi bi-tags"></i>
                <ul class="tags">
                  <?php foreach ($tags as $tag): ?>
                  <li><a href="blog.php?tag=<?= $tag['slug'] ?>"><?= htmlspecialchars($tag['name']) ?></a></li>
                  <?php endforeach; ?>
                </ul>
                <?php endif; ?>
              </div>

            </article><!-- End blog entry -->

          </div><!-- End blog entries list -->

          <div class="col-lg-4">

            <div class="sidebar">

               <h3 class="sidebar-title">Articles Récents</h3>
              <div class="sidebar-item recent-posts">
                <?php
                  // Fetch Recent Posts (real data)
                  $recentStmt = $conn->query("SELECT id, title, cover_image, published_at FROM articles WHERE status='published' ORDER BY published_at DESC LIMIT 5");
                  while($rec = $recentStmt->fetch(PDO::FETCH_ASSOC)):
                ?>
                <div class="post-item clearfix">
                  <?php if($rec['cover_image']): ?>
                  <img src="<?= htmlspecialchars($rec['cover_image']) ?>" alt="">
                  <?php endif; ?>
                  <h4><a href="blog-single.php?id=<?= $rec['id'] ?>"><?= htmlspecialchars($rec['title']) ?></a></h4>
                  <time datetime="<?= $rec['published_at'] ?>"><?= date('d M, Y', strtotime($rec['published_at'])) ?></time>
                </div>
                <?php endwhile; ?>
             </div><!-- End sidebar -->

          </div><!-- End blog sidebar -->

        </div>

      </div>
    </section><!-- End Blog Single Section -->

  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>
  <script src="assets/js/c_main.js"></script>

</body>

</html>
<?php
  require 'hd-ft/ft.php';
?>


    $recent_post = $conn->prepare("SELECT * FROM article GROUP BY id_art DESC LIMIT 5");
    $recent_post->execute();



  $titre = $_GET['titre_art'];
  $auteur = $_GET['auteur'];
  $contenu = $_GET['p_art1'];
  $photo = $_GET['photo_art'];
  $date_art = $_GET['date_art'];


?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>
    if(isset($title)){
      echo $title;
    }
    else{
      Bowaba n Congo
    }
  </title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/icone-bw.png" rel="icon">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/signle-blog.css" rel="stylesheet">

</head>

<body>
  <main id="main">

    <!-- ======= Breadcrumbs ======= -->
    <div class="top-bloc">
      <div class="content">
      <ol>
          <li><a href="index.php">Acceuil</a>   /</li>
          <li><a href="blog.php">Blog</a></li>
        </ol>
        <h2>Lecture de l'Article</h2>
      </div>
    </div>
    <!-- ======= Blog Single Section ======= -->
    <section id="blog" class="blog">
      <div class="container" data-aos="fade-up">

        <div class="row">

          <div class="col-lg-8 entries">

            <article class="entry entry-single">

              <div class="entry-img">
                <img src="adm/actions/image/<?php echo $photo; ?>" alt="" class="img-fluid">
              </div>

              <h2 class="entry-title">
                <p><?php echo htmlentities($titre); ?></p>
              </h2>

              <div class="entry-meta">
                <ul>
                  <li class="d-flex align-items-center"><i class="bi bi-person"></i> <a href="#"><?php echo $auteur; ?></a></li>
                  <li class="d-flex align-items-center"><i class="bi bi-clock"></i> <a href="#"><time datetime="2020-01-01"><?php echo $date_art; ?></time></a></li>
                  <li class="d-flex align-items-center"><i class="bi bi-chat-dots"></i> <a href="#">12 Commentaires</a></li>
                </ul>
              </div>

              <div class="entry-content">
                <p><?php echo nl2br(htmlentities($contenu)); ?></p>        
              </div>

              <div class="entry-footer">
                <i class="bi bi-folder"></i>
                <ul class="cats">
                  <li><a href="#">Business</a></li>
                </ul>

                <i class="bi bi-tags"></i>
                <ul class="tags">
                  <li><a href="#">Creative</a></li>
                  <li><a href="#">Tips</a></li>
                  <li><a href="#">Marketing</a></li>
                </ul>
              </div>

            </article><!-- End blog entry -->

        
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

             </div><!-- End sidebar -->

          </div><!-- End blog sidebar -->

        </div>

      </div>
    </section><!-- End Blog Single Section -->

  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>
  <script src="assets/js/c_main.js"></script>

</body>

</html>
<?php
  require'hd-ft/ft.php';
?>
