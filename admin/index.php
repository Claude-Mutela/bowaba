<?php
/**
 * Admin Dashboard — index.php
 * BowaBanCongo CMS
 */
require_once __DIR__ . '/../kon/conn.php';

$pageTitle  = 'Tableau de bord';
$activePage = 'dashboard';
$adminBase  = './';

// ── Stats queries ──
$dbError = null;
$totalArticles = $published = $drafts = $totalViews = $totalServices = $totalUsers = $totalCategories = 0;
$recentArticles = $topArticles = [];

// Check if $conn is available
if (!isset($conn)) {
  $dbError = "Connexion à la base de données échouée. Vérifiez les identifiants dans <code>kon/conn.php</code>.";
} else {
  try {
    $totalArticles  = $conn->query("SELECT COUNT(*) FROM articles")->fetchColumn();
    $published      = $conn->query("SELECT COUNT(*) FROM articles WHERE status='published'")->fetchColumn();
    $drafts         = $conn->query("SELECT COUNT(*) FROM articles WHERE status='draft'")->fetchColumn();
    $totalViews     = $conn->query("SELECT SUM(views_count) FROM articles")->fetchColumn() ?? 0;
    $totalServices  = $conn->query("SELECT COUNT(*) FROM services WHERE status='active'")->fetchColumn();
    $totalUsers     = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $totalCategories= $conn->query("SELECT COUNT(*) FROM article_categories")->fetchColumn();

    // Recent articles
    $recentArticles = $conn->query(
      "SELECT a.id, a.title, a.status, a.is_featured, a.views_count, a.created_at,
              c.name AS category_name, u.name AS author_name
       FROM articles a
       LEFT JOIN article_categories c ON a.category_id = c.id
       LEFT JOIN users u ON a.user_id = u.id
       ORDER BY a.created_at DESC LIMIT 8"
    )->fetchAll(PDO::FETCH_ASSOC);

    // Top articles by views
    $topArticles = $conn->query(
      "SELECT title, views_count, status FROM articles ORDER BY views_count DESC LIMIT 5"
    )->fetchAll(PDO::FETCH_ASSOC);

  } catch (PDOException $e) {
    $dbError = "Erreur SQL : <code>" . htmlspecialchars($e->getMessage()) . "</code><br>
      <strong>Solution :</strong> Importez le fichier <code>bowabanc_db.sql</code> dans phpMyAdmin 
      (base de données : <code>bowabanc_db</code>).";
  }
}

include __DIR__ . '/partials/header.php';
?>

<!-- Breadcrumb -->
<div class="admin-breadcrumb">
  <i class="bi bi-house-fill"></i>
  <span class="sep">/</span>
  <span>Tableau de bord</span>
</div>

<?php if ($dbError): ?>
<div class="admin-alert admin-alert-error" style="margin-bottom:20px;">
  <i class="bi bi-exclamation-triangle-fill" style="font-size:20px;"></i>
  <div>
    <strong>Erreur de base de données</strong><br>
    <?= $dbError ?>
  </div>
</div>
<?php endif; ?>

<!-- Page Header -->
<div class="page-header">
  <div class="page-header-left">
    <h1>Tableau de bord</h1>
    <p>Bienvenue dans l'interface d'administration de BowaBanCongo.</p>
  </div>
  <div class="d-flex gap-2">
    <a href="articles/create.php" class="btn-bbc-primary">
      <i class="bi bi-plus-lg"></i> Nouvel article
    </a>
  </div>
</div>

<!-- ── Stats Cards ── -->
<div class="row g-3 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card">
      <div class="stat-icon"><i class="bi bi-file-earmark-richtext"></i></div>
      <div class="stat-info">
        <div class="stat-value"><?= number_format($totalArticles) ?></div>
        <div class="stat-label">Total Articles</div>
        <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i> <?= $published ?> publiés</div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card gold">
      <div class="stat-icon"><i class="bi bi-eye-fill"></i></div>
      <div class="stat-info">
        <div class="stat-value"><?= number_format($totalViews) ?></div>
        <div class="stat-label">Vues totales</div>
        <div class="stat-trend up"><i class="bi bi-graph-up"></i> Cumulées</div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card green">
      <div class="stat-icon"><i class="bi bi-briefcase-fill"></i></div>
      <div class="stat-info">
        <div class="stat-value"><?= number_format($totalServices) ?></div>
        <div class="stat-label">Services actifs</div>
        <div class="stat-trend up"><i class="bi bi-check-circle"></i> En ligne</div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="stat-card purple">
      <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
      <div class="stat-info">
        <div class="stat-value"><?= number_format($totalUsers) ?></div>
        <div class="stat-label">Utilisateurs</div>
        <div class="stat-trend"><i class="bi bi-person-check"></i> <?= $totalCategories ?> catégories</div>
      </div>
    </div>
  </div>
</div>

<!-- ── Secondary Stats ── -->
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="admin-card h-100">
      <div class="admin-card-body text-center py-4">
        <div style="font-size:36px; font-weight:800; color:var(--bbc-blue);"><?= $published ?></div>
        <div style="font-size:13px; color:var(--text-secondary); margin-top:4px;">Articles publiés</div>
        <div class="badge-status badge-published mt-2">Publiés</div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="admin-card h-100">
      <div class="admin-card-body text-center py-4">
        <div style="font-size:36px; font-weight:800; color:#d97706;"><?= $drafts ?></div>
        <div style="font-size:13px; color:var(--text-secondary); margin-top:4px;">Brouillons</div>
        <div class="badge-status badge-draft mt-2">Brouillons</div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="admin-card h-100">
      <div class="admin-card-body text-center py-4">
        <div style="font-size:36px; font-weight:800; color:#059669;"><?= $totalCategories ?></div>
        <div style="font-size:13px; color:var(--text-secondary); margin-top:4px;">Catégories</div>
        <div class="badge-status badge-active mt-2">Actives</div>
      </div>
    </div>
  </div>
</div>

<!-- ── Main Content Grid ── -->
<div class="row g-4">

  <!-- Recent Articles Table -->
  <div class="col-xl-8">
    <div class="admin-card">
      <div class="admin-card-header">
        <h2 class="admin-card-title"><i class="bi bi-clock-history"></i> Articles récents</h2>
        <a href="articles/index.php" class="btn-bbc-outline" style="padding:6px 14px; font-size:12.5px;">
          Voir tout <i class="bi bi-arrow-right"></i>
        </a>
      </div>
      <div class="admin-table-wrapper">
        <?php if (empty($recentArticles)): ?>
          <div class="empty-state">
            <i class="bi bi-file-earmark-x"></i>
            <h3>Aucun article</h3>
            <p>Commencez par créer votre premier article.</p>
            <a href="articles/create.php" class="btn-bbc-primary">
              <i class="bi bi-plus-lg"></i> Créer un article
            </a>
          </div>
        <?php else: ?>
          <table class="admin-table" id="articlesTableDash">
            <thead>
              <tr>
                <th>Titre</th>
                <th>Catégorie</th>
                <th>Statut</th>
                <th>Vues</th>
                <th>Date</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentArticles as $art): ?>
              <tr>
                <td>
                  <div style="display:flex; align-items:center; gap:10px;">
                    <div class="article-thumb-placeholder"><i class="bi bi-image"></i></div>
                    <div>
                      <div style="font-weight:600; font-size:13px; max-width:220px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        <?= htmlspecialchars($art['title']) ?>
                      </div>
                      <div style="font-size:11px; color:var(--text-secondary);">
                        <?= htmlspecialchars($art['author_name'] ?? 'N/A') ?>
                        <?php if ($art['is_featured']): ?>
                          <i class="bi bi-star-fill featured-star ms-1" title="À la une"></i>
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                </td>
                <td>
                  <span style="font-size:12px; background:var(--bbc-blue-light); color:var(--bbc-blue); padding:3px 8px; border-radius:20px; font-weight:600;">
                    <?= htmlspecialchars($art['category_name'] ?? 'Sans catégorie') ?>
                  </span>
                </td>
                <td>
                  <?php
                    $statusClass = match($art['status']) {
                      'published' => 'badge-published',
                      'draft'     => 'badge-draft',
                      'archived'  => 'badge-archived',
                      default     => 'badge-draft'
                    };
                    $statusLabel = match($art['status']) {
                      'published' => 'Publié',
                      'draft'     => 'Brouillon',
                      'archived'  => 'Archivé',
                      default     => $art['status']
                    };
                  ?>
                  <span class="badge-status <?= $statusClass ?>"><?= $statusLabel ?></span>
                </td>
                <td>
                  <span style="font-weight:600; color:var(--bbc-blue);">
                    <i class="bi bi-eye" style="font-size:12px;"></i>
                    <?= number_format($art['views_count']) ?>
                  </span>
                </td>
                <td style="font-size:12px; color:var(--text-secondary);">
                  <?= date('d/m/Y', strtotime($art['created_at'])) ?>
                </td>
                <td>
                  <div style="display:flex; gap:4px;">
                    <a href="articles/edit.php?id=<?= $art['id'] ?>" class="btn-action edit" title="Modifier">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <a href="../blog-single.php?id=<?= $art['id'] ?>" class="btn-action view" title="Voir" target="_blank">
                      <i class="bi bi-eye"></i>
                    </a>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Top Articles by Views -->
  <div class="col-xl-4">
    <div class="admin-card mb-4">
      <div class="admin-card-header">
        <h2 class="admin-card-title"><i class="bi bi-trophy-fill"></i> Top articles</h2>
      </div>
      <div class="admin-card-body" style="padding:0;">
        <?php if (empty($topArticles)): ?>
          <div class="empty-state" style="padding:30px;">
            <i class="bi bi-bar-chart" style="font-size:32px;"></i>
            <p>Aucune donnée disponible</p>
          </div>
        <?php else: ?>
          <?php foreach ($topArticles as $i => $art): ?>
          <div style="display:flex; align-items:center; gap:12px; padding:14px 20px; border-bottom:1px solid var(--border-color);">
            <div style="width:28px; height:28px; border-radius:50%; background:<?= $i === 0 ? 'var(--bbc-gold)' : 'var(--bbc-blue-light)' ?>; color:<?= $i === 0 ? '#1a2332' : 'var(--bbc-blue)' ?>; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; flex-shrink:0;">
              <?= $i + 1 ?>
            </div>
            <div style="flex:1; min-width:0;">
              <div style="font-size:13px; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                <?= htmlspecialchars($art['title']) ?>
              </div>
              <div style="font-size:11px; color:var(--text-secondary);">
                <i class="bi bi-eye"></i> <?= number_format($art['views_count']) ?> vues
              </div>
            </div>
            <span class="badge-status <?= $art['status'] === 'published' ? 'badge-published' : 'badge-draft' ?>" style="font-size:10px;">
              <?= $art['status'] === 'published' ? 'Publié' : 'Brouillon' ?>
            </span>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="admin-card">
      <div class="admin-card-header">
        <h2 class="admin-card-title"><i class="bi bi-lightning-fill"></i> Actions rapides</h2>
      </div>
      <div class="admin-card-body" style="display:flex; flex-direction:column; gap:10px;">
        <a href="articles/create.php" class="btn-bbc-primary w-100" style="justify-content:center;">
          <i class="bi bi-file-earmark-plus"></i> Nouvel article
        </a>
        <a href="categories/index.php" class="btn-bbc-outline w-100" style="justify-content:center;">
          <i class="bi bi-tag-fill"></i> Gérer les catégories
        </a>
        <a href="services/index.php" class="btn-bbc-outline w-100" style="justify-content:center;">
          <i class="bi bi-briefcase"></i> Gérer les services
        </a>
        <a href="users/index.php" class="btn-bbc-outline w-100" style="justify-content:center;">
          <i class="bi bi-person-plus"></i> Gérer les utilisateurs
        </a>
      </div>
    </div>
  </div>

</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
