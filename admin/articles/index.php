<?php
/**
 * Articles List — admin/articles/index.php
 */
require_once __DIR__ . '/../../kon/conn.php';

$pageTitle  = 'Articles';
$activePage = 'articles';
$adminBase  = '../';

// Pagination
$perPage     = 10;
$currentPage = max(1, (int)($_GET['page'] ?? 1));
$offset      = ($currentPage - 1) * $perPage;

// Filters
$filterStatus   = $_GET['status'] ?? '';
$filterCategory = $_GET['category'] ?? '';
$search         = trim($_GET['q'] ?? '');

try {
  // Build WHERE clause
  $where  = [];
  $params = [];

  if ($filterStatus) {
    $where[] = 'a.status = :status';
    $params[':status'] = $filterStatus;
  }
  if ($filterCategory) {
    $where[] = 'a.category_id = :cat';
    $params[':cat'] = $filterCategory;
  }
  if ($search) {
    $where[] = '(a.title LIKE :q OR a.excerpt LIKE :q)';
    $params[':q'] = "%$search%";
  }

  $whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

  // Total count
  $countStmt = $conn->prepare("SELECT COUNT(*) FROM articles a $whereSQL");
  $countStmt->execute($params);
  $totalItems = (int)$countStmt->fetchColumn();
  $totalPages = max(1, ceil($totalItems / $perPage));

  // Articles
  $stmt = $conn->prepare(
    "SELECT a.id, a.user_id, a.title, a.slug, a.status, a.is_featured, a.views_count, a.published_at, a.created_at,
            c.name AS category_name, u.name AS author_name
     FROM articles a
     LEFT JOIN article_categories c ON a.category_id = c.id
     LEFT JOIN users u ON a.user_id = u.id
     $whereSQL
     ORDER BY a.created_at DESC
     LIMIT :limit OFFSET :offset"
  );
  foreach ($params as $k => $v) $stmt->bindValue($k, $v);
  $stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
  $stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
  $stmt->execute();
  $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Categories for filter
  $categories = $conn->query("SELECT id, name FROM article_categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
  $articles = $categories = [];
  $totalItems = $totalPages = 0;
}

// Handle delete — only admin/editor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  requirePermission('articles.delete');
  try {
    $del = $conn->prepare("DELETE FROM articles WHERE id = :id");
    $del->execute([':id' => (int)$_POST['delete_id']]);
    header('Location: index.php?deleted=1');
    exit;
  } catch (Exception $e) {}
}

include __DIR__ . '/../partials/header.php';
?>

<!-- Breadcrumb -->
<div class="admin-breadcrumb">
  <a href="../index.php"><i class="bi bi-house-fill"></i> Dashboard</a>
  <span class="sep">/</span>
  <span>Articles</span>
</div>

<!-- Page Header -->
<div class="page-header">
  <div class="page-header-left">
    <h1>Articles</h1>
    <p><?= number_format($totalItems) ?> article<?= $totalItems > 1 ? 's' : '' ?> au total</p>
  </div>
  <a href="create.php" class="btn-bbc-primary">
    <i class="bi bi-plus-lg"></i> Nouvel article
  </a>
</div>

<!-- Alert -->
<?php if (isset($_GET['deleted'])): ?>
  <div class="admin-alert admin-alert-success" data-auto-dismiss="3000">
    <i class="bi bi-check-circle-fill"></i> Article supprimé avec succès.
  </div>
<?php endif; ?>
<?php if (isset($_GET['saved'])): ?>
  <div class="admin-alert admin-alert-success" data-auto-dismiss="3000">
    <i class="bi bi-check-circle-fill"></i> Article enregistré avec succès.
  </div>
<?php endif; ?>

<!-- Filter Bar -->
<div class="admin-card mb-4">
  <div class="admin-card-body" style="padding:16px 20px;">
    <form method="GET" action="index.php" class="filter-bar">
      <div class="search-input-wrapper">
        <i class="bi bi-search"></i>
        <input type="text" id="articleSearch" name="q" class="form-control"
               placeholder="Rechercher un article…" value="<?= htmlspecialchars($search) ?>">
      </div>
      <select name="status" class="form-select" style="width:auto; min-width:150px;">
        <option value="">Tous les statuts</option>
        <option value="published" <?= $filterStatus === 'published' ? 'selected' : '' ?>>Publiés</option>
        <option value="draft"     <?= $filterStatus === 'draft'     ? 'selected' : '' ?>>Brouillons</option>
        <option value="archived"  <?= $filterStatus === 'archived'  ? 'selected' : '' ?>>Archivés</option>
      </select>
      <select name="category" class="form-select" style="width:auto; min-width:180px;">
        <option value="">Toutes les catégories</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= $cat['id'] ?>" <?= $filterCategory == $cat['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn-bbc-primary" style="padding:8px 18px;">
        <i class="bi bi-funnel"></i> Filtrer
      </button>
      <?php if ($filterStatus || $filterCategory || $search): ?>
        <a href="index.php" class="btn-bbc-outline" style="padding:7px 16px;">
          <i class="bi bi-x-lg"></i> Réinitialiser
        </a>
      <?php endif; ?>
    </form>
  </div>
</div>

<!-- Articles Table -->
<div class="admin-card">
  <div class="admin-table-wrapper">
    <?php if (empty($articles)): ?>
      <div class="empty-state">
        <i class="bi bi-file-earmark-x"></i>
        <h3>Aucun article trouvé</h3>
        <p>Modifiez vos filtres ou créez un nouvel article.</p>
        <a href="create.php" class="btn-bbc-primary"><i class="bi bi-plus-lg"></i> Créer un article</a>
      </div>
    <?php else: ?>
      <table class="admin-table" id="articlesTable">
        <thead>
          <tr>
            <th style="width:40px;">#</th>
            <th>Titre</th>
            <th>Catégorie</th>
            <th>Auteur</th>
            <th>Statut</th>
            <th>Vues</th>
            <th>Date</th>
            <th style="width:110px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($articles as $i => $art): ?>
          <tr>
            <td style="color:var(--text-secondary); font-size:12px;"><?= $offset + $i + 1 ?></td>
            <td>
              <div style="display:flex; align-items:center; gap:10px;">
                <div class="article-thumb-placeholder"><i class="bi bi-image"></i></div>
                <div>
                  <div style="font-weight:600; font-size:13px; max-width:240px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    <?= htmlspecialchars($art['title']) ?>
                  </div>
                  <div style="font-size:11px; color:var(--text-secondary);">
                    /<?= htmlspecialchars($art['slug']) ?>
                    <?php if ($art['is_featured']): ?>
                      <i class="bi bi-star-fill featured-star ms-1" title="À la une"></i>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </td>
            <td>
              <?php if ($art['category_name']): ?>
                <span style="font-size:12px; background:var(--bbc-blue-light); color:var(--bbc-blue); padding:3px 8px; border-radius:20px; font-weight:600;">
                  <?= htmlspecialchars($art['category_name']) ?>
                </span>
              <?php else: ?>
                <span style="color:var(--text-secondary); font-size:12px;">—</span>
              <?php endif; ?>
            </td>
            <td style="font-size:13px;"><?= htmlspecialchars($art['author_name'] ?? '—') ?></td>
            <td>
              <?php
                $sc = match($art['status']) {
                  'published' => 'badge-published',
                  'draft'     => 'badge-draft',
                  'archived'  => 'badge-archived',
                  default     => 'badge-draft'
                };
                $sl = match($art['status']) {
                  'published' => 'Publié',
                  'draft'     => 'Brouillon',
                  'archived'  => 'Archivé',
                  default     => $art['status']
                };
              ?>
              <span class="badge-status <?= $sc ?>"><?= $sl ?></span>
            </td>
            <td>
              <span style="font-weight:600; color:var(--bbc-blue); font-size:13px;">
                <i class="bi bi-eye" style="font-size:11px;"></i> <?= number_format($art['views_count']) ?>
              </span>
            </td>
            <td style="font-size:12px; color:var(--text-secondary);">
              <?= date('d/m/Y', strtotime($art['created_at'])) ?>
            </td>
            <td>
              <div style="display:flex; gap:4px;">
                <?php if (canEditArticle($art['user_id'] ?? 0)): ?>
                <a href="edit.php?id=<?= $art['id'] ?>" class="btn-action edit" title="Modifier">
                  <i class="bi bi-pencil"></i>
                </a>
                <?php endif; ?>
                <a href="../../blog/<?= htmlspecialchars($art['slug'] ?? '') ?>" class="btn-action view" title="Voir" target="_blank">
                  <i class="bi bi-eye"></i>
                </a>
                <?php if (can('articles.delete')): ?>
                <form method="POST" style="display:inline;" onsubmit="return confirmDelete('Supprimer cet article définitivement ?')">
                  <input type="hidden" name="delete_id" value="<?= $art['id'] ?>">
                  <button type="submit" class="btn-action delete" title="Supprimer">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
                <?php endif; ?>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

  <!-- Pagination -->
  <?php if ($totalPages > 1): ?>
  <div class="admin-pagination">
    <div class="pagination-info">
      Affichage <strong><?= $offset + 1 ?>–<?= min($offset + $perPage, $totalItems) ?></strong>
      sur <strong><?= $totalItems ?></strong> articles
    </div>
    <nav>
      <ul class="pagination mb-0">
        <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
          <a class="page-link" href="?page=<?= $currentPage - 1 ?>&status=<?= urlencode($filterStatus) ?>&category=<?= urlencode($filterCategory) ?>&q=<?= urlencode($search) ?>">
            <i class="bi bi-chevron-left"></i>
          </a>
        </li>
        <?php for ($p = max(1, $currentPage - 2); $p <= min($totalPages, $currentPage + 2); $p++): ?>
          <li class="page-item <?= $p === $currentPage ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $p ?>&status=<?= urlencode($filterStatus) ?>&category=<?= urlencode($filterCategory) ?>&q=<?= urlencode($search) ?>">
              <?= $p ?>
            </a>
          </li>
        <?php endfor; ?>
        <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
          <a class="page-link" href="?page=<?= $currentPage + 1 ?>&status=<?= urlencode($filterStatus) ?>&category=<?= urlencode($filterCategory) ?>&q=<?= urlencode($search) ?>">
            <i class="bi bi-chevron-right"></i>
          </a>
        </li>
      </ul>
    </nav>
  </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
