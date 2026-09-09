<?php
    require_once __DIR__ . '/kon/conn.php';

    // Get Slug
    $slug = filter_input(INPUT_GET, 'slug', FILTER_SANITIZE_SPECIAL_CHARS);
    if (!$slug && isset($_GET['slug'])) {
        $slug = trim($_GET['slug']);
    }
    
    // Fetch Service
    $service = null;
    if ($slug) {
        $stmt = $conn->prepare("SELECT * FROM services WHERE slug = :slug AND status = 'active' LIMIT 1");
        $stmt->execute([':slug' => $slug]);
        $service = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 404 if not found
    if (!$service) {
        // Fallback: try ID if slug fails (for backward compatibility)
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if($id){
             $stmt = $conn->prepare("SELECT slug FROM services WHERE id=:id AND status='active'");
             $stmt->execute([':id'=>$id]);
             $found = $stmt->fetchColumn();
             if($found){
                 header("Location: service/" . $found, true, 301);
                 exit;
             }
        }
        
        header("HTTP/1.0 404 Not Found");
        require_once __DIR__ . '/hd-ft/hd.php'; // Load header to show navbar even on 404
        echo '<div class="container my-5 text-center"><h1>Service introuvable</h1><p>Ce service n\'existe pas ou a été retiré.</p><a href="services" class="btn btn-primary">Retour aux services</a></div>';
        require_once __DIR__ . '/hd-ft/ft.php';
        exit;
    }

    // Meta Tags & Open Graph
    $pageTitle = $service['title'];
    $pageDesc  = !empty($service['description']) ? $service['description'] : 'Découvrez nos prestations et accompagnements personnalisés pour ' . $service['title'] . ' chez Bowaba n Congo.';
    $pageImage = !empty($service['image']) ? $service['image'] : null; // For Open Graph (falls back to Bowaba logo if empty)
    $pageUrl   = 'service/' . $slug;
    $pageCss   = 'assets/css/detail-service.css';
    $nav       = 'details-service';
    
    require_once __DIR__ . '/hd-ft/hd.php';
?>

    
    <!-- ======= Breadcrumbs Strip ======= -->
    <div class="top-bloc small-header">
      <div class="content">
        <!-- Desktop: Breadcrumb complet avec titre -->
        <ol class="d-none d-md-flex">
          <li><a href="index">Accueil</a> /</li>
          <li><a href="services">Services</a> /</li>
          <li class="active"><?= htmlspecialchars($service['title']) ?></li>
        </ol>
        
        <!-- Mobile: Lien retour simple sans titre -->
        <div class="d-md-none">
            <a href="services" style="color: #fff; font-weight: 600; text-decoration: none; display: flex; align-items: center;">
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
                        <!-- Icone ou visuel décoratif -->
                        <div class="service-icon mb-4 text-center">
                           <?php if($service['icon']): ?>
                               <i class="<?= htmlspecialchars($service['icon']) ?> fs-1 text-primary"></i>
                           <?php else: ?>
                               <i class="bx bx-layer fs-1 text-primary"></i> 
                           <?php endif; ?>
                        </div>

                        <?php if($service['image']): ?>
                        <div class="service-image mb-4 text-center">
                            <img src="<?= htmlspecialchars($service['image']) ?>" alt="<?= htmlspecialchars($service['title']) ?>" class="img-fluid rounded shadow-sm">
                        </div>
                        <?php endif; ?>
                        
                        <div class="content-body">
                            <h1 class="text-center mb-4"><?= htmlspecialchars($service['title']) ?></h1>

                            <?php if($service['description']): ?>
                                <div class="lead mb-4 service-intro text-center">
                                    <?= nl2br(htmlspecialchars($service['description'])) ?>
                                </div>
                            <?php endif; ?>

                            <?php if($service['content']): ?>
                                <div class="service-text-content">
                                    <?= $service['content'] // Raw HTML from TinyMCE ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- CTA Section -->
                        <div class="service-cta mt-5 p-4 bg-light rounded-3 text-center">
                            <h3>Intéressé par ce service ?</h3>
                            <p>Contactez-nous pour en discuter ou obtenir un devis personnalisé.</p>
                            <a href="contact.php" class="btn btn-primary btn-lg mt-2">Nous contacter</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
    require_once __DIR__ . '/hd-ft/ft.php';
?>