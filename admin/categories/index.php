<?php
/**
 * Categories — admin/categories/index.php
 */
require_once __DIR__ . '/../../kon/conn.php';
require_once __DIR__ . '/../partials/auth.php'; // Auth & Permissions

$pageTitle  = 'Catégories';
$activePage = 'categories';
$adminBase  = '../';

$errors  = [];
$success = '';

// Helper slugify
function makeSlug($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a-' . time() : $text;
}

// Handle create / update / delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'delete' && !empty($_POST['delete_id'])) {
    requirePermission('categories.delete');
    try {
      $conn->prepare("DELETE FROM article_categories WHERE id=:id")->execute([':id' => (int)$_POST['delete_id']]);
      // Redirect to avoid resubmission
      $_SESSION['flash_success'] = 'Catégorie supprimée.';
      header("Location: index.php");
      exit;
    } catch (Exception $e) {
      $errors[] = 'Impossible de supprimer : ' . $e->getMessage();
    }
  }

  if (in_array($action, ['create', 'update'])) {
    requirePermission('categories.create');
    $name        = trim($_POST['name'] ?? '');
    $slug        = trim($_POST['slug'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $editId      = (int)($_POST['edit_id'] ?? 0);

    if (!$name) $errors[] = 'Le nom est obligatoire.';
    
    // Auto-generate slug if empty
    if (!$slug && $name) {
      $slug = makeSlug($name);
    }
    // Sanitize slug
    $slug = makeSlug($slug);

    if (empty($errors)) {
      try {
        if ($action === 'create') {
          $conn->prepare(
            "INSERT INTO article_categories (name, slug, description) VALUES (:n,:s,:d)"
          )->execute([':n'=>$name, ':s'=>$slug, ':d'=>$description]);
          $_SESSION['flash_success'] = 'Catégorie créée avec succès.';
        } else {
          $conn->prepare(
            "UPDATE article_categories SET name=:n, slug=:s, description=:d WHERE id=:id"
          )->execute([':n'=>$name, ':s'=>$slug, ':d'=>$description, ':id'=>$editId]);
          $_SESSION['flash_success'] = 'Catégorie mise à jour.';
        }
        header("Location: index.php");
        exit;
      } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
           $errors[] = 'Ce slug est déjà utilisé. Veuillez en choisir un autre.';
        } else {
           $errors[] = $e->getMessage();
        }
      }
    }
  }
}

// Flash messages
if (isset($_SESSION['flash_success'])) {
    $success = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}

// Load categories with article count
try {
  $categories = $conn->query(
    "SELECT c.*, COUNT(a.id) AS article_count
     FROM article_categories c
     LEFT JOIN articles a ON a.category_id = c.id
     GROUP BY c.id
     ORDER BY c.name"
  )->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  $categories = [];
}

// Load category for editing
$editCat = null;
if (!empty($_GET['edit'])) {
  foreach ($categories as $c) {
    if ($c['id'] == (int)$_GET['edit']) { $editCat = $c; break; }
  }
}

include __DIR__ . '/../partials/header.php';
?>

<div class="admin-breadcrumb">
  <a href="../index.php"><i class="bi bi-house-fill"></i> Dashboard</a>
  <span class="sep">/</span>
  <span>Catégories</span>
</div>

<div class="page-header">
  <div class="page-header-left">
    <h1>Catégories d'articles</h1>
    <p><?= count($categories) ?> catégorie<?= count($categories) > 1 ? 's' : '' ?></p>
  </div>
</div>

<?php if ($success): ?>
  <div class="admin-alert admin-alert-success" data-auto-dismiss="3000">
    <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success) ?>
  </div>
<?php endif; ?>
<?php if (!empty($errors)): ?>
  <div class="admin-alert admin-alert-error">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <div><?php foreach ($errors as $e): ?><div><?= htmlspecialchars($e) ?></div><?php endforeach; ?></div>
  </div>
<?php endif; ?>

<div class="row g-4">

  <!-- Categories Table -->
  <div class="col-lg-7">
    <div class="admin-card">
      <div class="admin-card-header">
        <h2 class="admin-card-title"><i class="bi bi-tags-fill"></i> Liste des catégories</h2>
      </div>
      <div class="admin-table-wrapper">
        <?php if (empty($categories)): ?>
          <div class="empty-state">
            <i class="bi bi-tags"></i>
            <h3>Aucune catégorie</h3>
            <p>Créez votre première catégorie.</p>
          </div>
        <?php else: ?>
          <table class="admin-table" id="categoriesTable">
            <thead>
              <tr>
                <th>Nom</th>
                <th>Slug</th>
                <th>Articles</th>
                <th>Description</th>
                <th style="width:90px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($categories as $cat): ?>
              <tr>
                <td style="font-weight:600;"><?= htmlspecialchars($cat['name']) ?></td>
                <td>
                  <code style="font-size:12px; background:var(--bbc-blue-light); color:var(--bbc-blue); padding:2px 6px; border-radius:4px;">
                    <?= htmlspecialchars($cat['slug']) ?>
                  </code>
                </td>
                <td>
                  <span class="order-badge"><?= $cat['article_count'] ?></span>
                </td>
                <td style="font-size:12px; color:var(--text-secondary); max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                  <?= htmlspecialchars($cat['description'] ?? '—') ?>
                </td>
                <td>
                  <div style="display:flex; gap:4px;">
                    <?php if (can('categories.edit')): ?>
                    <a href="?edit=<?= $cat['id'] ?>" class="btn-action edit" title="Modifier">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <?php endif; ?>
                    <?php if (can('categories.delete')): ?>
                    <form method="POST" onsubmit="return confirmDelete('Supprimer cette catégorie ?')">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="delete_id" value="<?= $cat['id'] ?>">
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
    </div>
  </div>

  <!-- Create / Edit Form -->
  <?php if (can('categories.create')): ?>
  <div class="col-lg-5">
    <div class="admin-card">
      <div class="admin-card-header">
        <h2 class="admin-card-title">
          <i class="bi bi-<?= $editCat ? 'pencil-square' : 'plus-circle-fill' ?>"></i>
          <?= $editCat ? 'Modifier la catégorie' : 'Nouvelle catégorie' ?>
        </h2>
        <?php if ($editCat): ?>
          <a href="index.php" class="btn-bbc-outline" style="padding:5px 12px; font-size:12px;">
            <i class="bi bi-x-lg"></i> Annuler
          </a>
        <?php endif; ?>
      </div>
      <div class="admin-card-body">
        <form method="POST">
          <input type="hidden" name="action" value="<?= $editCat ? 'update' : 'create' ?>">
          <?php if ($editCat): ?>
            <input type="hidden" name="edit_id" value="<?= $editCat['id'] ?>">
          <?php endif; ?>

          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="categoryName" name="name"
                   placeholder="Nom" required maxlength="255"
                   value="<?= htmlspecialchars($editCat['name'] ?? '') ?>">
            <label for="categoryName">Nom de la catégorie *</label>
          </div>

          <div class="mb-3">
            <label class="form-label" style="font-size:13px; font-weight:600; color:var(--text-secondary);">Slug *</label>
            <div class="input-group">
              <span class="input-group-text" style="font-size:12px; background:#f8fafc; color:var(--text-secondary);">/cat/</span>
              <input type="text" class="form-control" id="categorySlug" name="slug"
                     placeholder="ma-categorie" required maxlength="255"
                     value="<?= htmlspecialchars($editCat['slug'] ?? '') ?>">
            </div>
          </div>

          <div class="form-floating mb-4">
            <textarea class="form-control" id="categoryDesc" name="description"
                      placeholder="Description" style="height:100px;"><?= htmlspecialchars($editCat['description'] ?? '') ?></textarea>
            <label for="categoryDesc">Description (optionnel)</label>
          </div>

          <button type="submit" class="btn-bbc-primary w-100" style="justify-content:center;">
            <i class="bi bi-<?= $editCat ? 'save-fill' : 'plus-lg' ?>"></i>
            <?= $editCat ? 'Mettre à jour' : 'Créer la catégorie' ?>
          </button>
        </form>
      </div>
    </div>
  </div>
  <?php endif; ?>

</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
