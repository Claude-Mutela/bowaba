<?php
    $title='BLOG';
    $nav='blog';
    require'hd-ft/hd.php';
    require'kon/conn.php';
    
    // ── Pagination & Filters ──
    $limit = 5;
    $page  = isset($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
    $start = ($page - 1) * $limit;

    $search   = trim($_GET['search'] ?? '');
    $category = trim($_GET['category'] ?? ''); // slug
    $tag      = trim($_GET['tag'] ?? '');      // slug

    // Build Query
    $where  = ["a.status = 'published'"];
    $params = [];

    if ($search) {
        $where[] = "(a.title LIKE :s OR a.content LIKE :s)";
        $params[':s'] = "%$search%";
    }
    if ($category) {
        $where[] = "c.slug = :cat";
        $params[':cat'] = $category;
    }
    if ($tag) {
        // Subquery for tags
        $where[] = "a.id IN (SELECT at.article_id FROM article_tags at JOIN tags t ON at.tag_id = t.id WHERE t.slug = :tag)";
        $params[':tag'] = $tag;
    }

    $whereSQL = implode(' AND ', $where);

    // Count Total
    $countStmt = $conn->prepare("
        SELECT COUNT(DISTINCT a.id) 
        FROM articles a
        LEFT JOIN article_categories c ON a.category_id = c.id
        WHERE $whereSQL
    ");
    $countStmt->execute($params);
    $totalArticles = $countStmt->fetchColumn();
    $totalPages    = ceil($totalArticles / $limit);

    // Fetch Articles
    $sql = "
        SELECT a.id, a.title, a.slug, a.excerpt, a.content, a.cover_image, a.published_at, a.views_count,
               u.name as author_name, c.name as category_name, c.slug as category_slug
        FROM articles a
        LEFT JOIN users u ON a.user_id = u.id
        LEFT JOIN article_categories c ON a.category_id = c.id
        WHERE $whereSQL
        ORDER BY a.published_at DESC
        LIMIT $start, $limit
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Sidebar Data: Categories with count
    $cats = $conn->query("
        SELECT c.name, c.slug, COUNT(a.id) as count
        FROM article_categories c
        LEFT JOIN articles a ON a.category_id = c.id AND a.status = 'published'
        GROUP BY c.id
        ORDER BY c.name
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Sidebar Data: Recent Posts
    $recents = $conn->query("
        SELECT id, title, cover_image, published_at 
        FROM articles 
        WHERE status = 'published' 
        ORDER BY published_at DESC 
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Sidebar Data: All Tags
    $allTags = $conn->query("SELECT name, slug FROM tags ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - Bowaba</title>

    <meta content="Découvrez nos derniers articles" name="description">
    
    <!-- Favicons -->
    <link href="assets/img/icone-bw.png" rel="icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Main CSS Files -->
    <link href="assets/css/global.css" rel="stylesheet">
    <link href="assets/css/blog.css" rel="stylesheet">
</head>

<body>

    <!-- ======= Hero Section ======= -->
    <section id="hero">
        <div class="hero-container">
            <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
                <ol class="carousel-indicators" id="hero-carousel-indicators"></ol>
                <div class="carousel-inner" role="listbox">
                    <div class="carousel-item active" style="background-image: url('assets/img/carte.jpeg');">
                        <div class="carousel-container">
                            <div class="carousel-content container">
                                <h2 class="animate__animated animate__fadeInDown"><span>Nos Articles</span></h2>
                                <p class="animate__animated animate__fadeInUp">Découvrez nos dernières actualités et conseils.</p>
                            </div>
                        </div>
                    </div>
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

                        <?php if (empty($articles)): ?>
                            <div class="alert alert-info">Aucun article trouvé.</div>
                        <?php else: ?>
                            <?php foreach($articles as $art): 
                                $date = strtotime($art['published_at']);
                                // Truncate content for excerpt if excerpt is empty
                                $desc = $art['excerpt'] ?: strip_tags($art['content']);
                                $desc = substr($desc, 0, 200) . '...';
                            ?>
                                <article class="entry">

                                    <?php if($art['cover_image']): ?>
                                    <div class="entry-img">
                                        <img src="<?= htmlspecialchars($art['cover_image']) ?>" alt="" class="img-fluid">
                                    </div>
                                    <?php endif; ?>

                                    <h2 class="entry-title">
                                        <a href="blog-single.php?id=<?= $art['id'] ?>">
                                            <?= htmlspecialchars($art['title']) ?>
                                        </a>
                                    </h2>

                                    <div class="entry-meta">
                                        <ul>
                                            <li class="d-flex align-items-center"><i class="bi bi-person"></i> <a href="#"><?= htmlspecialchars($art['author_name'] ?? 'Admin') ?></a></li>
                                            <li class="d-flex align-items-center"><i class="bi bi-clock"></i> <a href="#"><time datetime="<?= $art['published_at'] ?>"><?= date('d M, Y', $date) ?></time></a></li>
                                            <?php if($art['category_name']): ?>
                                            <li class="d-flex align-items-center"><i class="bi bi-folder"></i> <a href="?category=<?= $art['category_slug'] ?>"><?= htmlspecialchars($art['category_name']) ?></a></li>
                                            <?php endif; ?>
                                            <li class="d-flex align-items-center"><i class="bi bi-eye"></i> <?= $art['views_count'] ?> Vues</li>
                                        </ul>
                                    </div>

                                    <div class="entry-content">
                                        <p><?= $desc ?></p>
                                        <div class="read-more">
                                            <a href="blog-single.php?id=<?= $art['id'] ?>">Lire la suite</a>
                                        </div>
                                    </div>

                                </article><!-- End blog entry -->
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                        <div class="blog-pagination">
                            <ul class="justify-content-center">
                                <!-- Prev -->
                                <li class="<?= ($page <= 1) ? 'disabled' : '' ?>">
                                    <a href="<?= ($page <= 1) ? '#' : '?page='.($page-1) ?>"><i class="bi bi-chevron-left"></i></a>
                                </li>
                                
                                <!-- Pages -->
                                <?php for($p=1; $p<=$totalPages; $p++): ?>
                                <li class="<?= ($page == $p) ? 'active' : '' ?>">
                                    <a href="?page=<?= $p ?>"><?= $p ?></a>
                                </li>
                                <?php endfor; ?>

                                <!-- Next -->
                                <li class="<?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                    <a href="<?= ($page >= $totalPages) ? '#' : '?page='.($page+1) ?>"><i class="bi bi-chevron-right"></i></a>
                                </li>
                            </ul>
                        </div>
                        <?php endif; ?>

                    </div><!-- End blog entries list -->

                    <div class="col-lg-4">

                        <div class="sidebar">

                            <h3 class="sidebar-title">Recherche</h3>
                            <div class="sidebar-item search-form">
                                <form action="" method="GET">
                                    <input type="text" name="search" placeholder="Rechercher..." value="<?= htmlspecialchars($search) ?>">
                                    <button type="submit"><i class="bi bi-search"></i></button>
                                </form>
                            </div><!-- End sidebar search formn-->

                            <h3 class="sidebar-title">Catégories</h3>
                            <div class="sidebar-item categories">
                                <ul>
                                    <?php foreach($cats as $c): ?>
                                    <li><a href="?category=<?= $c['slug'] ?>"><?= htmlspecialchars($c['name']) ?> <span>(<?= $c['count'] ?>)</span></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div><!-- End sidebar categories-->

                            <h3 class="sidebar-title">Articles Récents</h3>
                            <div class="sidebar-item recent-posts">
                                <?php foreach($recents as $r): ?>
                                    <div class="post-item clearfix">
                                        <?php if($r['cover_image']): ?>
                                        <img src="<?= htmlspecialchars($r['cover_image']) ?>" alt="">
                                        <?php endif; ?>
                                        <h4><a href="blog-single.php?id=<?= $r['id'] ?>"><?= htmlspecialchars($r['title']) ?></a></h4>
                                        <time datetime="<?= $r['published_at'] ?>"><?= date('d M, Y', strtotime($r['published_at'])) ?></time>
                                    </div>
                                <?php endforeach; ?>
                            </div><!-- End sidebar recent posts-->

                            <h3 class="sidebar-title">Tags</h3>
                            <div class="sidebar-item tags">
                                <ul>
                                    <?php foreach($allTags as $t): ?>
                                    <li><a href="?tag=<?= $t['slug'] ?>"><?= htmlspecialchars($t['name']) ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div><!-- End sidebar tags-->

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

    <!-- Main JS File -->
    <script src="assets/js/main.js"></script>

</body>
<?php
    require'hd-ft/ft.php';
?>
</html>
    $limit = 5;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $start = ($page - 1) * $limit;

    // Fetch Articles (MOCK DATA)
    // $sql_select_blog = $conn->prepare("SELECT * FROM article ORDER BY id_art DESC LIMIT $start, $limit");
    // $sql_select_blog->execute();

    // Fetch Recent Posts for Sidebar (MOCK DATA)
    // $recent_post = $conn->prepare("SELECT * FROM article ORDER BY id_art DESC LIMIT 5");
    // $recent_post->execute();

    $mock_articles = [
        (object)[
            'id_art' => 1,
            'titre_art' => 'Lancement de la nouvelle campagne Bowaba',
            'auteur' => 'Admin',
            'date_art' => '2023-10-01 10:00:00',
            'photo_art' => 'pexels-brett-sayles-2850290.jpg', 
            'p_art1' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.'
        ],
        (object)[
            'id_art' => 2,
            'titre_art' => 'Les bienfaits de la technologie moderne',
            'auteur' => 'Jean Dupont',
            'date_art' => '2023-09-25 14:30:00',
            'photo_art' => 'example.jpg',
            'p_art1' => 'Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
        ],
        (object)[
            'id_art' => 3,
            'titre_art' => 'Comment améliorer votre productivité',
            'auteur' => 'Marie Curie',
            'date_art' => '2023-09-20 09:15:00',
            'photo_art' => 'example.jpg',
            'p_art1' => 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.'
        ],
         (object)[
            'id_art' => 4,
            'titre_art' => 'Événement caritatif annuel',
            'auteur' => 'Admin',
            'date_art' => '2023-09-15 18:00:00',
            'photo_art' => 'example.jpg',
            'p_art1' => 'Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet.'
        ],
         (object)[
            'id_art' => 5,
            'titre_art' => 'Nouveau partenariat annoncé',
            'auteur' => 'Paul Martin',
            'date_art' => '2023-09-10 11:20:00',
            'photo_art' => 'pexels-brett-sayles-2850290.jpg',
            'p_art1' => 'At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident.'
        ]
    ];

    $mock_recent = array_slice($mock_articles, 0, 3);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - Bowaba</title>

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
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

    <!-- Main CSS Files -->
    <link href="assets/css/global.css" rel="stylesheet">
    <link href="assets/css/blog.css" rel="stylesheet">
</head>

<body>

    <!-- ======= Hero Section ======= -->
    <section id="hero">
        <div class="hero-container">
            <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
                <ol class="carousel-indicators" id="hero-carousel-indicators"></ol>
                <div class="carousel-inner" role="listbox">
                    <div class="carousel-item active" style="background-image: url('assets/img/carte.jpeg');">
                        <div class="carousel-container">
                            <div class="carousel-content container">
                                <h2 class="animate__animated animate__fadeInDown"><span>Nos Articles</span></h2>
                                <p class="animate__animated animate__fadeInUp">Découvrez nos dernières actualités et conseils.</p>
                            </div>
                        </div>
                    </div>
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

                        <?php foreach($mock_articles as $articles){ 
                            $d_date = strtotime($articles->date_art);
                        ?>
                            <article class="entry">

                                <div class="entry-img">
                                    <!-- Using placeholder image if file doesn't exist, logic handled in src attribute or keep as is if testing locallly -->
                                    <img src="adm/actions/image/<?php echo $articles->photo_art; ?>" alt="" class="img-fluid">
                                </div>

                                <h2 class="entry-title">
                                    <a href="blog-single.php?titre_art=<?php echo urlencode($articles->titre_art); ?>&id_art=<?php echo $articles->id_art; ?>">
                                        <?php echo nl2br(htmlentities($articles->titre_art)); ?>
                                    </a>
                                </h2>

                                <div class="entry-meta">
                                    <ul>
                                        <li class="d-flex align-items-center"><i class="bi bi-person"></i> <a href="#"><?php echo $articles->auteur; ?></a></li>
                                        <li class="d-flex align-items-center"><i class="bi bi-clock"></i> <a href="#"><time datetime="<?php echo date('Y-m-d', $d_date); ?>"><?php echo date('d M, Y', $d_date); ?></time></a></li>
                                        <li class="d-flex align-items-center"><i class="bi bi-folder"></i> <a href="#">Actualités</a></li> <!-- Static Category for now -->
                                        <li class="d-flex align-items-center"><i class="bi bi-chat-dots"></i> <a href="#">0 Commentaires</a></li>
                                    </ul>
                                </div>

                                <div class="entry-content">
                                    <p>
                                        <?php echo nl2br(htmlentities(substr($articles->p_art1, 0, 200))); ?>...
                                    </p>
                                    <div class="read-more">
                                        <a href="blog-single.php?titre_art=<?php echo urlencode($articles->titre_art); ?>&id_art=<?php echo $articles->id_art; ?>">Lire la suite</a>
                                    </div>
                                </div>

                            </article><!-- End blog entry -->
                        <?php } ?>

                        <div class="blog-pagination">
                            <ul class="justify-content-center">
                                <li class="<?php if($page <= 1){ echo 'disabled'; } ?>">
                                    <a href="<?php if($page <= 1){ echo '#'; } else { echo "?page=".($page - 1); } ?>"><i class="bi bi-chevron-left"></i></a>
                                </li>
                                <li class="active"><a href="#"><?php echo $page; ?></a></li>
                                <li><a href="?page=<?php echo $page + 1; ?>"><?php echo $page + 1; ?></a></li>
                                <li><a href="?page=<?php echo $page + 2; ?>"><?php echo $page + 2; ?></a></li>
                                <li>
                                    <a href="?page=<?php echo $page + 1; ?>"><i class="bi bi-chevron-right"></i></a>
                                </li>
                            </ul>
                        </div>

                    </div><!-- End blog entries list -->

                    <div class="col-lg-4">

                        <div class="sidebar">

                            <h3 class="sidebar-title">Recherche</h3>
                            <div class="sidebar-item search-form">
                                <form action="" method="GET">
                                    <input type="text" name="search" placeholder="Rechercher un article...">
                                    <button type="submit"><i class="bi bi-search"></i></button>
                                </form>
                            </div><!-- End sidebar search formn-->

                            <h3 class="sidebar-title">Catégories</h3>
                            <div class="sidebar-item categories">
                                <ul>
                                    <li><a href="#">Actualités <span>(25)</span></a></li>
                                    <li><a href="#">Technologie <span>(12)</span></a></li>
                                    <li><a href="#">Éducation <span>(5)</span></a></li>
                                    <li><a href="#">Santé <span>(22)</span></a></li>
                                    <li><a href="#">Divers <span>(8)</span></a></li>
                                </ul>
                            </div><!-- End sidebar categories-->

                            <h3 class="sidebar-title">Articles Récents</h3>
                            <div class="sidebar-item recent-posts">
                                <?php foreach($mock_recent as $article){ 
                                    $d_dat = strtotime($article->date_art);
                                ?>
                                    <div class="post-item clearfix">
                                        <img src="adm/actions/image/<?php echo $article->photo_art; ?>" alt="">
                                        <h4><a href="blog-single.php?titre_art=<?php echo urlencode($article->titre_art); ?>&id_art=<?php echo $article->id_art; ?>"><?php echo substr($article->titre_art, 0, 50); ?>...</a></h4>
                                        <time datetime="<?php echo date('Y-m-d', $d_dat); ?>"><?php echo date('d M, Y', $d_dat); ?></time>
                                    </div>
                                <?php } ?>
                            </div><!-- End sidebar recent posts-->

                            <h3 class="sidebar-title">Tags</h3>
                            <div class="sidebar-item tags">
                                <ul>
                                    <li><a href="#">App</a></li>
                                    <li><a href="#">IT</a></li>
                                    <li><a href="#">Business</a></li>
                                    <li><a href="#">Mac</a></li>
                                    <li><a href="#">Design</a></li>
                                    <li><a href="#">Office</a></li>
                                    <li><a href="#">Creative</a></li>
                                    <li><a href="#">Studio</a></li>
                                    <li><a href="#">Smart</a></li>
                                    <li><a href="#">Tips</a></li>
                                    <li><a href="#">Marketing</a></li>
                                </ul>
                            </div><!-- End sidebar tags-->

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

    <!-- Main JS File -->
    <script src="assets/js/main.js"></script>

</body>
<?php
    require'hd-ft/ft.php';
?>
</html>