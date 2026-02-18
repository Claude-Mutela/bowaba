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
    <div class="top-bloc small-header">
      <div class="content">
      <ol>
          <li><a href="index.php">Accueil</a> /</li>
          <li><a href="blog.php">Blog</a> /</li>
          <li class="active"><?= htmlspecialchars($article['category_name'] ?? 'Article') ?></li>
        </ol>
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

              <h1 class="entry-title">
                <?= htmlspecialchars($article['title']) ?>
              </h1>

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

            <!-- Social Share Section -->
            <div class="entry-share mt-5 pt-4 border-top">
                <h5 class="share-title mb-3">Partager cet article</h5>
                <div class="d-flex gap-2">
                    <?php 
                    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                    $currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                    $shareTitle = urlencode($article['title']);
                    $shareUrl = urlencode($currentUrl);
                    ?>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $shareUrl ?>" target="_blank" class="btn btn-share btn-facebook" title="Facebook"><i class="bi bi-facebook"></i></a>
                    <a href="https://twitter.com/intent/tweet?url=<?= $shareUrl ?>&text=<?= $shareTitle ?>" target="_blank" class="btn btn-share btn-twitter" title="X (Twitter)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-twitter-x" viewBox="0 0 16 16">
                            <path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865l8.875 11.633Z"/>
                        </svg>
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= $shareUrl ?>&title=<?= $shareTitle ?>" target="_blank" class="btn btn-share btn-linkedin" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                    <a href="https://wa.me/?text=<?= $shareTitle ?>%20<?= $shareUrl ?>" target="_blank" class="btn btn-share btn-whatsapp" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                    <button class="btn btn-share btn-copy" onclick="copyToClipboard('<?= $currentUrl ?>')" title="Copier le lien"><i class="bi bi-link-45deg"></i></button>
                </div>
            </div><!-- End Share Section -->

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

  <script>
    function copyToClipboard(text) {
      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
          alert("Lien copié dans le presse-papier !");
        });
      } else {
        // Fallback for non-secure context or older browsers
        let textArea = document.createElement("textarea");
        textArea.value = text;
        textArea.style.position = "fixed";
        textArea.style.left = "-9999px";
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
          document.execCommand('copy');
          alert("Lien copié dans le presse-papier !");
        } catch (err) {
          console.error('Unable to copy', err);
        }
        document.body.removeChild(textArea);
      }
    }
  </script>

</body>

</html>
<?php
  require 'hd-ft/ft.php';
?>