<?php
/**
 * Article Create Form — admin/articles/create.php
 */
require_once __DIR__ . '/../../kon/conn.php';
require_once __DIR__ . '/../partials/auth.php'; // Auth & Permissions

$pageTitle  = 'Nouvel article';
$activePage = 'articles';
$adminBase  = '../';

$errors = [];
$success = false;

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

// Load categories, users and tags for selects
try {
  $categories = $conn->query("SELECT id, name FROM article_categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
  $users      = $conn->query("SELECT id, name FROM users ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
  $allTags    = $conn->query("SELECT id, name, slug FROM tags ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  $categories = $users = $allTags = [];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title       = trim($_POST['title'] ?? '');
  $slug        = trim($_POST['slug'] ?? '');
  
  // Auto-generate slug if empty
  if (!$slug && $title) {
      $slug = makeSlug($title);
  }
  // Sanitize slug
  $slug = makeSlug($slug);
  
  $excerpt     = trim($_POST['excerpt'] ?? '');
  $content     = $_POST['content'] ?? '';
  $categoryId  = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
  // RBAC: authors are always set as the article's author
  $userId      = can('articles.edit_all') && !empty($_POST['user_id'])
                 ? (int)$_POST['user_id']
                 : (int)$adminUser['id'];
  // RBAC: authors can only save as draft
  $status      = in_array($_POST['status'] ?? '', ['draft','published','archived']) ? $_POST['status'] : 'draft';
  if (!can('articles.publish')) $status = 'draft';
  $isFeatured  = (can('articles.feature') && isset($_POST['is_featured'])) ? 1 : 0;
  $publishedAt = ($status === 'published') ? date('Y-m-d H:i:s') : null;

  // Validation
  if (!$title)   $errors[] = 'Le titre est obligatoire.';
  // Slug is now guaranteed to have a value if title exists
  if (!$content) $errors[] = 'Le contenu est obligatoire.';

  // Handle cover image upload
  $coverImage = null;
  if (!empty($_FILES['cover_image']['name'])) {
    $uploadDir  = __DIR__ . '/../../assets/img/articles/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $ext        = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
    $allowed    = ['jpg','jpeg','png','webp','gif'];
    if (!in_array($ext, $allowed)) {
      $errors[] = 'Format d\'image non autorisé.';
    } else {
      $filename   = uniqid('art_') . '.' . $ext;
      if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $uploadDir . $filename)) {
        $coverImage = 'assets/img/articles/' . $filename;
      }
    }
  }

  if (empty($errors)) {
    try {
      $stmt = $conn->prepare(
        "INSERT INTO articles (user_id, category_id, title, slug, excerpt, content, cover_image, status, is_featured, published_at)
         VALUES (:uid, :cid, :title, :slug, :excerpt, :content, :cover, :status, :featured, :pub)"
      );
      $stmt->execute([
        ':uid'      => $userId,
        ':cid'      => $categoryId,
        ':title'    => $title,
        ':slug'     => $slug,
        ':excerpt'  => $excerpt,
        ':content'  => $content,
        ':cover'    => $coverImage,
        ':status'   => $status,
        ':featured' => $isFeatured,
        ':pub'      => $publishedAt,
      ]);
      $newId = $conn->lastInsertId();

      // Save tags
      $tagsInput = trim($_POST['tags_input'] ?? '');
      if ($tagsInput) {
        $tagData = json_decode($tagsInput, true) ?: [];
        $insTag  = $conn->prepare("INSERT IGNORE INTO tags (name, slug) VALUES (:n, :s)");
        $insLink = $conn->prepare("INSERT IGNORE INTO article_tags (article_id, tag_id) VALUES (:a, :t)");
        $selTag  = $conn->prepare("SELECT id FROM tags WHERE slug = :s");
        foreach ($tagData as $t) {
          $tname = trim($t['value'] ?? '');
          if (!$tname) continue;
          $tslug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $tname));
          $insTag->execute([':n' => $tname, ':s' => $tslug]);
          $selTag->execute([':s' => $tslug]);
          $tagId = $selTag->fetchColumn();
          if ($tagId) $insLink->execute([':a' => $newId, ':t' => $tagId]);
        }
      }

      header("Location: index.php?saved=1");
      exit;
    } catch (PDOException $e) {
      if ($e->getCode() === '23000') {
        $errors[] = 'Ce slug est déjà utilisé. Veuillez en choisir un autre.';
      } else {
        $errors[] = 'Erreur lors de l\'enregistrement : ' . $e->getMessage();
      }
    }
  }
}

include __DIR__ . '/../partials/header.php';
?>

<!-- Breadcrumb -->
<div class="admin-breadcrumb">
  <a href="../index.php"><i class="bi bi-house-fill"></i> Dashboard</a>
  <span class="sep">/</span>
  <a href="index.php">Articles</a>
  <span class="sep">/</span>
  <span>Nouvel article</span>
</div>

<!-- Page Header -->
<div class="page-header">
  <div class="page-header-left">
    <h1>Nouvel article</h1>
    <p>Remplissez les champs ci-dessous pour créer un nouvel article.</p>
  </div>
  <div class="d-flex gap-2">
    <a href="index.php" class="btn-bbc-outline">
      <i class="bi bi-arrow-left"></i> Retour
    </a>
  </div>
</div>

<!-- Errors -->
<?php if (!empty($errors)): ?>
  <div class="admin-alert admin-alert-error mb-4">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <div>
      <?php foreach ($errors as $err): ?>
        <div><?= htmlspecialchars($err) ?></div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" id="articleForm">
<div class="row g-4">

  <!-- ── col-md-8 : Main Content ── -->
  <div class="col-md-8">

    <!-- Title & Slug -->
    <div class="admin-card mb-4">
      <div class="admin-card-header">
        <h2 class="admin-card-title"><i class="bi bi-type-h1"></i> Informations principales</h2>
      </div>
      <div class="admin-card-body">

        <!-- Title (floating label) -->
        <div class="form-floating mb-3">
          <input type="text" class="form-control" id="articleTitle" name="title"
                 placeholder="Titre de l'article"
                 value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                 required maxlength="255"
                 data-max-chars="255" data-counter-id="titleCounter">
          <label for="articleTitle"><i class="bi bi-pencil-square me-1"></i> Titre de l'article *</label>
        </div>
        <div style="text-align:right; font-size:11px; color:var(--text-secondary); margin-top:-10px; margin-bottom:16px;">
          <span id="titleCounter">0 / 255</span>
        </div>

        <!-- Slug -->
        <div class="mb-3">
          <label class="form-label" style="font-size:13px; font-weight:600; color:var(--text-secondary);">
            Slug (URL) *
          </label>
          <div class="input-group">
            <span class="input-group-text" style="font-size:12px; color:var(--text-secondary); background:#f8fafc;">
              /blog/
            </span>
            <input type="text" class="form-control" id="articleSlug" name="slug"
                   placeholder="mon-article-en-francais"
                   value="<?= htmlspecialchars($_POST['slug'] ?? '') ?>"
                   required maxlength="255">
          </div>
          <div style="font-size:11px; color:var(--text-secondary); margin-top:4px;">
            <i class="bi bi-info-circle"></i> Généré automatiquement depuis le titre. Modifiable manuellement.
          </div>
        </div>

        <!-- Excerpt -->
        <div class="form-floating">
          <textarea class="form-control" id="articleExcerpt" name="excerpt"
                    placeholder="Résumé"
                    style="height:90px;"
                    maxlength="500"
                    data-max-chars="500" data-counter-id="excerptCounter"><?= htmlspecialchars($_POST['excerpt'] ?? '') ?></textarea>
          <label for="articleExcerpt"><i class="bi bi-text-paragraph me-1"></i> Résumé (extrait)</label>
        </div>
        <div style="text-align:right; font-size:11px; color:var(--text-secondary); margin-top:4px;">
          <span id="excerptCounter">0 / 500</span>
        </div>

      </div>
    </div>

    <!-- Content (TinyMCE) -->
    <div class="admin-card mb-4">
      <div class="admin-card-header">
        <h2 class="admin-card-title"><i class="bi bi-body-text"></i> Contenu de l'article</h2>
      </div>
      <div class="admin-card-body">
        <div class="tinymce-wrapper">
          <textarea class="tinymce-editor" id="articleContent" name="content"><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>
        </div>
      </div>
    </div>

  </div><!-- end col-md-8 -->

  <!-- ── col-md-4 : Settings Sidebar ── -->
  <div class="col-md-4">
    <div class="form-sidebar-panel">

      <!-- Publish Settings -->
      <div class="admin-card mb-4">
        <div class="admin-card-header">
          <h2 class="admin-card-title"><i class="bi bi-send-fill"></i> Publication</h2>
        </div>
        <div class="admin-card-body">

          <!-- Status -->
          <div class="mb-3">
            <label class="form-label" style="font-size:13px; font-weight:600;">Statut</label>
            <?php if (can('articles.publish')): ?>
            <select class="form-select" name="status" id="articleStatus">
              <option value="draft"     <?= ($_POST['status'] ?? 'draft') === 'draft'     ? 'selected' : '' ?>>
                📝 Brouillon
              </option>
              <option value="published" <?= ($_POST['status'] ?? '') === 'published' ? 'selected' : '' ?>>
                ✅ Publié
              </option>
              <option value="archived"  <?= ($_POST['status'] ?? '') === 'archived'  ? 'selected' : '' ?>>
                📦 Archivé
              </option>
            </select>
            <?php else: ?>
            <input type="hidden" name="status" value="draft">
            <div class="form-control bg-light" style="font-size:13px; color:#6b7a8d;">
              📝 Brouillon <small>(les auteurs ne peuvent pas publier)</small>
            </div>
            <?php endif; ?>
          </div>

          <!-- Author -->
          <?php if (can('articles.edit_all')): ?>
          <div class="mb-3">
            <label class="form-label" style="font-size:13px; font-weight:600;">Auteur</label>
            <select class="form-select" name="user_id">
              <option value="">— Sélectionner un auteur —</option>
              <?php foreach ($users as $u): ?>
                <option value="<?= $u['id'] ?>" <?= ($_POST['user_id'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($u['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <?php else: ?>
          <input type="hidden" name="user_id" value="<?= $adminUser['id'] ?>">
          <?php endif; ?>

          <!-- Featured -->
          <?php if (can('articles.feature')): ?>
            <input class="form-check-input" type="checkbox" role="switch"
                   id="isFeatured" name="is_featured" value="1"
                   <?= isset($_POST['is_featured']) ? 'checked' : '' ?>>
            <label class="form-check-label" for="isFeatured" style="font-size:13px; font-weight:600;">
              <i class="bi bi-star-fill" style="color:var(--bbc-gold-dark);"></i> Mettre à la une
            </label>
          </div>
          <?php endif; ?>

          <div class="admin-divider"></div>

          <!-- Submit Buttons -->
          <div class="d-flex flex-column gap-2">
            <?php if (can('articles.publish')): ?>
            <button type="submit" name="status_submit" value="published" class="btn-bbc-primary w-100" style="justify-content:center;">
              <i class="bi bi-send-fill"></i> Publier maintenant
            </button>
            <?php endif; ?>
            <button type="submit" name="status_submit" value="draft" class="btn-bbc-outline w-100" style="justify-content:center;">
              <i class="bi bi-save"></i> Enregistrer brouillon
            </button>
          </div>

        </div>
      </div>

      <!-- Category -->
      <div class="admin-card mb-4">
        <div class="admin-card-header">
          <h2 class="admin-card-title"><i class="bi bi-tags-fill"></i> Catégorie</h2>
        </div>
        <div class="admin-card-body">
          <select class="form-select" name="category_id" id="articleCategory">
            <option value="">— Sans catégorie —</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>" <?= ($_POST['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div style="margin-top:10px;">
            <a href="../categories/index.php" style="font-size:12px; color:var(--bbc-blue);">
              <i class="bi bi-plus-circle"></i> Gérer les catégories
            </a>
          </div>
        </div>
      </div>

      <!-- Tags -->
      <div class="admin-card mb-4">
        <div class="admin-card-header">
          <h2 class="admin-card-title"><i class="bi bi-hash"></i> Tags</h2>
        </div>
        <div class="admin-card-body">
          <input id="tagsInput" name="tags_input" placeholder="Ajouter des tags…"
                 value="<?= htmlspecialchars($_POST['tags_input'] ?? '') ?>">
          <div style="margin-top:8px; font-size:11px; color:var(--text-secondary);">
            Tapez et appuyez sur <kbd>Entrée</kbd> ou <kbd>,</kbd> pour ajouter.
          </div>
        </div>
      </div>

      <!-- Cover Image -->
      <div class="admin-card">
        <div class="admin-card-header">
          <h2 class="admin-card-title"><i class="bi bi-image-fill"></i> Image de couverture</h2>
        </div>
        <div class="admin-card-body">

          <!-- Upload Zone -->
          <div class="image-upload-zone" id="coverImageZone"
               onclick="document.getElementById('coverImageInput').click()">
            <i class="bi bi-cloud-arrow-up"></i>
            <p><strong>Cliquez pour uploader</strong><br>
               <span style="font-size:11px;">JPG, PNG, WebP — Max 5 Mo</span>
            </p>
          </div>

          <!-- Preview -->
          <div class="image-preview-container" id="coverImagePreview">
            <img src="" alt="Aperçu">
            <button type="button" class="image-preview-remove"
                    onclick="removeImagePreview('coverImageInput','coverImagePreview','coverImageZone')">
              <i class="bi bi-x"></i>
            </button>
          </div>

          <input type="file" id="coverImageInput" name="cover_image"
                 accept="image/*" style="display:none;">
          <div style="font-size:11px; color:var(--text-secondary); margin-top:8px;">
            Dimensions recommandées : 1200 × 630 px
          </div>
        </div>
      </div>

    </div>
  </div><!-- end col-md-4 -->

</div><!-- end row -->
</form>

<?php include __DIR__ . '/../partials/footer.php'; ?>
