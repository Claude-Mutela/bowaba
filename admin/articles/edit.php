<?php
/**
 * Article Edit Form — admin/articles/edit.php
 */
require_once __DIR__ . '/../../kon/conn.php';
require_once __DIR__ . '/../partials/auth.php'; // Auth & Permissions

$pageTitle  = 'Modifier l\'article';
$activePage = 'articles';
$adminBase  = '../';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: index.php'); exit; }

$errors = [];

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

try {
  $categories = $conn->query("SELECT id, name FROM article_categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
  $users      = $conn->query("SELECT id, name FROM users ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
  $allTags    = $conn->query("SELECT id, name, slug FROM tags ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  $categories = $users = $allTags = [];
}

// Fetch article
$stmt = $conn->prepare("SELECT * FROM articles WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) {
  echo "Article introuvable.";
  exit;
}

// RBAC check: Authors can only edit their own articles
if (!can('articles.edit_all') && !isOwner($article['user_id'])) {
  requirePermission('articles.edit_own', 'Vous ne pouvez modifier que vos propres articles.');
}

// Load current article tags
try {
  $atStmt = $conn->prepare(
    "SELECT t.name FROM tags t
     JOIN article_tags at ON at.tag_id = t.id
     WHERE at.article_id = :id"
  );
  $atStmt->execute([':id' => $id]);
  $articleTagNames = $atStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {
  header('Location: index.php'); exit;
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title      = trim($_POST['title'] ?? '');
  $slug       = trim($_POST['slug'] ?? '');
  $excerpt    = trim($_POST['excerpt'] ?? '');
  $content    = $_POST['content'] ?? '';
  $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
  // RBAC: authors cannot change the author field
  $userId     = can('articles.edit_all') && !empty($_POST['user_id'])
                ? (int)$_POST['user_id']
                : (int)$article['user_id'];
  // RBAC: authors can only save as draft
  $status     = in_array($_POST['status'] ?? '', ['draft','published','archived']) ? $_POST['status'] : 'draft';
  if (!can('articles.publish')) $status = 'draft';
  $isFeatured = (can('articles.feature') && isset($_POST['is_featured'])) ? 1 : 0;
  $publishedAt = ($status === 'published' && !$article['published_at']) ? date('Y-m-d H:i:s') : $article['published_at'];

  if (!$title)   $errors[] = 'Le titre est obligatoire.';
  if (!$slug)    $errors[] = 'Le slug est obligatoire.';
  if (!$content) $errors[] = 'Le contenu est obligatoire.';

  // Handle cover image
  $coverImage = $article['cover_image'];
  if (!empty($_FILES['cover_image']['name'])) {
    $uploadDir = __DIR__ . '/../../assets/img/articles/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $ext     = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp','gif'];
    if (!in_array($ext, $allowed)) {
      $errors[] = 'Format d\'image non autorisé.';
    } else {
      $filename = uniqid('art_') . '.' . $ext;
      if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $uploadDir . $filename)) {
        $coverImage = 'assets/img/articles/' . $filename;
      }
    }
  }

  if (empty($errors)) {
    try {
      $upd = $conn->prepare(
        "UPDATE articles SET user_id=:uid, category_id=:cid, title=:title, slug=:slug,
         excerpt=:excerpt, content=:content, cover_image=:cover, status=:status,
         is_featured=:featured, published_at=:pub
         WHERE id=:id"
      );
      $upd->execute([
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
        ':id'       => $id,
      ]);
      $article = array_merge($article, [
        'title'=>$title,'slug'=>$slug,'excerpt'=>$excerpt,'content'=>$content,
        'category_id'=>$categoryId,'user_id'=>$userId,'status'=>$status,
        'is_featured'=>$isFeatured,'cover_image'=>$coverImage
      ]);

      // Save tags — delete old links then reinsert
      $conn->prepare("DELETE FROM article_tags WHERE article_id = :id")->execute([':id' => $id]);
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
          if ($tagId) $insLink->execute([':a' => $id, ':t' => $tagId]);
        }
        // Refresh articleTagNames for re-display
        $articleTagNames = array_map(fn($t) => $t['value'], $tagData);
      } else {
        $articleTagNames = [];
      }

      header("Location: edit.php?id=$id&saved=1");
      exit;
    } catch (PDOException $e) {
      if ($e->getCode() === '23000') {
        $errors[] = 'Ce slug est déjà utilisé.';
      } else {
        $errors[] = 'Erreur : ' . $e->getMessage();
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
  <span>Modifier</span>
</div>

<!-- Page Header -->
<div class="page-header">
  <div class="page-header-left">
    <h1>Modifier l'article</h1>
    <p style="font-size:12px; color:var(--text-secondary);">
      ID #<?= $id ?> — Créé le <?= date('d/m/Y', strtotime($article['created_at'])) ?>
    </p>
  </div>
  <div class="d-flex gap-2">
    <a href="../../blog/<?= htmlspecialchars($article['slug'] ?? '') ?>" class="btn-bbc-outline" target="_blank">
      <i class="bi bi-eye"></i> Voir
    </a>
    <a href="index.php" class="btn-bbc-outline">
      <i class="bi bi-arrow-left"></i> Retour
    </a>
  </div>
</div>

<!-- Alerts -->
<?php if (isset($_GET['saved'])): ?>
  <div class="admin-alert admin-alert-success" data-auto-dismiss="3000">
    <i class="bi bi-check-circle-fill"></i> Article mis à jour avec succès.
  </div>
<?php endif; ?>
<?php if (!empty($errors)): ?>
  <div class="admin-alert admin-alert-error">
    <i class="bi bi-exclamation-triangle-fill"></i>
    <div><?php foreach ($errors as $err): ?><div><?= htmlspecialchars($err) ?></div><?php endforeach; ?></div>
  </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" id="articleForm">
<div class="row g-4">

  <!-- col-md-8 : Content -->
  <div class="col-md-8">

    <div class="admin-card mb-4">
      <div class="admin-card-header">
        <h2 class="admin-card-title"><i class="bi bi-type-h1"></i> Informations principales</h2>
      </div>
      <div class="admin-card-body">

        <div class="form-floating mb-3">
          <input type="text" class="form-control" id="articleTitle" name="title"
                 placeholder="Titre" value="<?= htmlspecialchars($article['title']) ?>"
                 required maxlength="255"
                 data-max-chars="255" data-counter-id="titleCounter">
          <label for="articleTitle"><i class="bi bi-pencil-square me-1"></i> Titre de l'article *</label>
        </div>
        <div style="text-align:right; font-size:11px; color:var(--text-secondary); margin-top:-10px; margin-bottom:16px;">
          <span id="titleCounter"><?= strlen($article['title']) ?> / 255</span>
        </div>

        <div class="mb-3">
          <label class="form-label" style="font-size:13px; font-weight:600; color:var(--text-secondary);">Slug (URL) *</label>
          <div class="input-group">
            <span class="input-group-text" style="font-size:12px; color:var(--text-secondary); background:#f8fafc;">/blog/</span>
            <input type="text" class="form-control" id="articleSlug" name="slug"
                   value="<?= htmlspecialchars($article['slug']) ?>" required maxlength="255">
          </div>
        </div>

        <div class="form-floating">
          <textarea class="form-control" id="articleExcerpt" name="excerpt"
                    placeholder="Résumé" style="height:90px;"
                    maxlength="500" data-max-chars="500" data-counter-id="excerptCounter"><?= htmlspecialchars($article['excerpt'] ?? '') ?></textarea>
          <label for="articleExcerpt">Résumé (extrait)</label>
        </div>
        <div style="text-align:right; font-size:11px; color:var(--text-secondary); margin-top:4px;">
          <span id="excerptCounter"><?= strlen($article['excerpt'] ?? '') ?> / 500</span>
        </div>

      </div>
    </div>

    <!-- TinyMCE Content -->
    <div class="admin-card mb-4">
      <div class="admin-card-header">
        <h2 class="admin-card-title"><i class="bi bi-body-text"></i> Contenu</h2>
      </div>
      <div class="admin-card-body">
        <div class="tinymce-wrapper">
          <textarea class="tinymce-editor" id="articleContent" name="content"><?= $article['content'] ?></textarea>
        </div>
      </div>
    </div>

    <!-- Stats info -->
    <div class="admin-card">
      <div class="admin-card-body" style="display:flex; gap:24px; flex-wrap:wrap;">
        <div>
          <div style="font-size:11px; color:var(--text-secondary); text-transform:uppercase; letter-spacing:.8px; font-weight:700;">Vues</div>
          <div style="font-size:22px; font-weight:800; color:var(--bbc-blue);"><?= number_format($article['views_count']) ?></div>
        </div>
        <div>
          <div style="font-size:11px; color:var(--text-secondary); text-transform:uppercase; letter-spacing:.8px; font-weight:700;">Publié le</div>
          <div style="font-size:14px; font-weight:600;"><?= $article['published_at'] ? date('d/m/Y H:i', strtotime($article['published_at'])) : '—' ?></div>
        </div>
        <div>
          <div style="font-size:11px; color:var(--text-secondary); text-transform:uppercase; letter-spacing:.8px; font-weight:700;">Modifié le</div>
          <div style="font-size:14px; font-weight:600;"><?= date('d/m/Y H:i', strtotime($article['updated_at'])) ?></div>
        </div>
      </div>
    </div>

  </div>

  <!-- col-md-4 : Settings -->
  <div class="col-md-4">
    <div class="form-sidebar-panel">

      <div class="admin-card mb-4">
        <div class="admin-card-header">
          <h2 class="admin-card-title"><i class="bi bi-send-fill"></i> Publication</h2>
        </div>
        <div class="admin-card-body">
          <div class="mb-3">
            <label class="form-label" style="font-size:13px; font-weight:600;">Statut</label>
            <?php if (can('articles.publish')): ?>
            <select class="form-select" name="status">
              <option value="draft"     <?= $article['status'] === 'draft'     ? 'selected' : '' ?>>📝 Brouillon</option>
              <option value="published" <?= $article['status'] === 'published' ? 'selected' : '' ?>>✅ Publié</option>
              <option value="archived"  <?= $article['status'] === 'archived'  ? 'selected' : '' ?>>📦 Archivé</option>
            </select>
            <?php else: ?>
            <input type="hidden" name="status" value="draft">
            <div class="form-control bg-light" style="font-size:13px; color:#6b7a8d;">
              📝 Brouillon <small>(les auteurs ne peuvent pas publier)</small>
            </div>
            <?php endif; ?>
          </div>
          <?php if (can('articles.edit_all')): ?>
          <div class="mb-3">
            <label class="form-label" style="font-size:13px; font-weight:600;">Auteur</label>
            <select class="form-select" name="user_id">
              <option value="">— Sélectionner —</option>
              <?php foreach ($users as $u): ?>
                <option value="<?= $u['id'] ?>" <?= $article['user_id'] == $u['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($u['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <?php endif; ?>
          <?php if (can('articles.feature')): ?>
          <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" role="switch"
                   id="isFeatured" name="is_featured" value="1"
                   <?= $article['is_featured'] ? 'checked' : '' ?>>
            <label class="form-check-label" for="isFeatured" style="font-size:13px; font-weight:600;">
              <i class="bi bi-star-fill" style="color:var(--bbc-gold-dark);"></i> À la une
            </label>
          </div>
          <?php endif; ?>
          <div class="admin-divider"></div>
          <div class="d-flex flex-column gap-2">
            <button type="submit" class="btn-bbc-primary w-100" style="justify-content:center;">
              <i class="bi bi-save-fill"></i> Enregistrer les modifications
            </button>
            <a href="index.php" class="btn-bbc-outline w-100" style="justify-content:center;">
              Annuler
            </a>
          </div>
        </div>
      </div>

      <div class="admin-card mb-4">
        <div class="admin-card-header">
          <h2 class="admin-card-title"><i class="bi bi-tags-fill"></i> Catégorie</h2>
        </div>
        <div class="admin-card-body">
          <select class="form-select" name="category_id">
            <option value="">— Sans catégorie —</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['id'] ?>" <?= $article['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <!-- Tags -->
      <div class="admin-card mb-4">
        <div class="admin-card-header">
          <h2 class="admin-card-title"><i class="bi bi-hash"></i> Tags</h2>
        </div>
        <div class="admin-card-body">
          <?php
            // Build Tagify initial value JSON from existing tags
            $tagifyInit = json_encode(array_map(fn($n) => ['value' => $n], $articleTagNames ?? []));
          ?>
          <input id="tagsInput" name="tags_input"
                 placeholder="Ajouter des tags…"
                 value="<?= htmlspecialchars($tagifyInit === '[]' ? '' : $tagifyInit) ?>">
          <div style="margin-top:8px; font-size:11px; color:var(--text-secondary);">
            Tapez et appuyez sur <kbd>Entrée</kbd> ou <kbd>,</kbd> pour ajouter.
          </div>
        </div>
      </div>

      <div class="admin-card">
        <div class="admin-card-header">
          <h2 class="admin-card-title"><i class="bi bi-image-fill"></i> Image de couverture</h2>
        </div>
        <div class="admin-card-body">
          <?php if ($article['cover_image']): ?>
            <div class="image-preview-container" id="coverImagePreview" style="display:block;">
              <img src="../../<?= htmlspecialchars($article['cover_image']) ?>" alt="Couverture">
              <button type="button" class="image-preview-remove"
                      onclick="removeImagePreview('coverImageInput','coverImagePreview','coverImageZone')">
                <i class="bi bi-x"></i>
              </button>
            </div>
            <div class="image-upload-zone" id="coverImageZone" style="display:none;"
                 onclick="document.getElementById('coverImageInput').click()">
              <i class="bi bi-cloud-arrow-up"></i>
              <p><strong>Changer l'image</strong></p>
            </div>
          <?php else: ?>
            <div class="image-upload-zone" id="coverImageZone"
                 onclick="document.getElementById('coverImageInput').click()">
              <i class="bi bi-cloud-arrow-up"></i>
              <p><strong>Cliquez pour uploader</strong><br>
                 <span style="font-size:11px;">JPG, PNG, WebP — Max 5 Mo</span>
              </p>
            </div>
            <div class="image-preview-container" id="coverImagePreview">
              <img src="" alt="Aperçu">
              <button type="button" class="image-preview-remove"
                      onclick="removeImagePreview('coverImageInput','coverImagePreview','coverImageZone')">
                <i class="bi bi-x"></i>
              </button>
            </div>
          <?php endif; ?>
          <input type="file" id="coverImageInput" name="cover_image" accept="image/*" style="display:none;">
        </div>
      </div>

    </div>
  </div>

</div>
</form>

<?php include __DIR__ . '/../partials/footer.php'; ?>
