<?php
/**
 * Testimonials Management — admin/testimonials/index.php
 */
require_once __DIR__ . '/../../kon/conn.php';
require_once __DIR__ . '/../partials/auth.php'; // loads session + $adminUser + permissions

$pageTitle  = 'Témoignages';
$activePage = 'testimonials';
$adminBase  = '../';

// RBAC: admin (write) + editor/author (read-only)
requirePermission('testimonials.view');

$errors  = [];
$success = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'delete' && !empty($_POST['delete_id'])) {
    requirePermission('testimonials.delete');
    try {
      $conn->prepare("DELETE FROM testimonials WHERE id=:id")->execute([':id' => (int)$_POST['delete_id']]);
      $success = 'Témoignage supprimé.';
    } catch (Exception $e) {
      $errors[] = 'Erreur : ' . $e->getMessage();
    }
  }

  if (in_array($action, ['create', 'update'])) {
    requirePermission('testimonials.create');
    $clientName = trim($_POST['client_name'] ?? '');
    $position   = trim($_POST['position'] ?? '');
    $company    = trim($_POST['company'] ?? '');
    $content    = trim($_POST['content'] ?? '');
    $rating     = (int)($_POST['rating'] ?? 5);
    $isActive   = isset($_POST['is_active']) ? 1 : 0;
    $editId     = (int)($_POST['edit_id'] ?? 0);

    if (!$clientName) $errors[] = 'Le nom du client est obligatoire.';
    if (!$content)    $errors[] = 'Le contenu du témoignage est obligatoire.';
    if ($rating < 1 || $rating > 5) $rating = 5;

    // Handle image upload
    $image = $_POST['existing_image'] ?? null;
    if (!empty($_FILES['image']['name'])) {
      $uploadDir = __DIR__ . '/../../assets/img/testimonials/';
      if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
      $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
      $allowed = ['jpg','jpeg','png','webp','gif'];
      if (!in_array($ext, $allowed)) {
        $errors[] = 'Format d\'image non autorisé.';
      } else {
        $filename = uniqid('testim_') . '.' . $ext;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
          $image = 'assets/img/testimonials/' . $filename;
        }
      }
    }

    if (empty($errors)) {
      try {
        if ($action === 'create') {
          $conn->prepare(
            "INSERT INTO testimonials (client_name, position, company, content, rating, image, is_active)
             VALUES (:n,:p,:c,:txt,:r,:img,:act)"
          )->execute([':n'=>$clientName,':p'=>$position,':c'=>$company,':txt'=>$content,
                      ':r'=>$rating,':img'=>$image,':act'=>$isActive]);
          $success = 'Témoignage ajouté avec succès.';
        } else {
          $conn->prepare(
            "UPDATE testimonials SET client_name=:n, position=:p, company=:c, content=:txt, 
             rating=:r, image=:img, is_active=:act WHERE id=:id"
          )->execute([':n'=>$clientName,':p'=>$position,':c'=>$company,':txt'=>$content,
                      ':r'=>$rating,':img'=>$image,':act'=>$isActive,':id'=>$editId]);
          $success = 'Témoignage mis à jour.';
        }
        header('Location: index.php?saved=1');
        exit;
      } catch (PDOException $e) {
        $errors[] = 'Erreur SGBD : ' . $e->getMessage();
      }
    }
  }
}

// Load testimonials
try {
  $testimonials = $conn->query(
    "SELECT * FROM testimonials ORDER BY created_at DESC"
  )->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  $testimonials = [];
}

// Load testimonial for editing
$editTestim = null;
if (!empty($_GET['edit'])) {
  foreach ($testimonials as $t) {
    if ($t['id'] == (int)$_GET['edit']) { $editTestim = $t; break; }
  }
}

include __DIR__ . '/../partials/header.php';
?>

<div class="admin-breadcrumb">
  <a href="../index.php"><i class="bi bi-house-fill"></i> Dashboard</a>
  <span class="sep">/</span>
  <span>Témoignages</span>
</div>

<div class="page-header">
  <div class="page-header-left">
    <h1>Gestion des Témoignages</h1>
    <p><?= count($testimonials) ?> avis client<?= count($testimonials) > 1 ? 's' : '' ?></p>
  </div>
  <?php if ($editTestim): ?>
    <a href="index.php" class="btn-bbc-outline"><i class="bi bi-list-ul"></i> Voir la liste</a>
  <?php elseif (can('testimonials.create')): ?>
    <a href="?new=1" class="btn-bbc-primary"><i class="bi bi-plus-lg"></i> Nouveau témoignage</a>
  <?php endif; ?>
</div>

<?php if (isset($_GET['saved'])): ?>
  <div class="admin-alert admin-alert-success" data-auto-dismiss="3000">
    <i class="bi bi-check-circle-fill"></i> Témoignage enregistré.
  </div>
<?php endif; ?>
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

<?php if (($editTestim || isset($_GET['new'])) && can('testimonials.create')): ?>
<!-- ── Testimonial Form (Edit / Create) ── -->
<div class="row g-4">
  <div class="col-lg-8">

    <div class="admin-card mb-4">
      <div class="admin-card-header">
        <h2 class="admin-card-title">
          <i class="bi bi-chat-quote-fill"></i>
          <?= $editTestim ? 'Modifier avis de : ' . htmlspecialchars($editTestim['client_name']) : 'Nouveau témoignage' ?>
        </h2>
      </div>
      <div class="admin-card-body">
        <form method="POST" enctype="multipart/form-data">
          <input type="hidden" name="action" value="<?= $editTestim ? 'update' : 'create' ?>">
          <?php if ($editTestim): ?>
            <input type="hidden" name="edit_id" value="<?= $editTestim['id'] ?>">
            <input type="hidden" name="existing_image" value="<?= htmlspecialchars($editTestim['image'] ?? '') ?>">
          <?php endif; ?>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="clientName" name="client_name"
                       placeholder="Nom" required maxlength="255"
                       value="<?= htmlspecialchars($editTestim['client_name'] ?? '') ?>">
                <label for="clientName">Nom du client *</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <select class="form-select" name="rating" id="rating">
                  <?php for($i=5; $i>=1; $i--): ?>
                    <option value="<?= $i ?>" <?= ($editTestim['rating'] ?? 5) == $i ? 'selected' : '' ?>>
                      <?= $i ?> ⭐
                    </option>
                  <?php endfor; ?>
                </select>
                <label for="rating">Note (étoiles)</label>
              </div>
            </div>
          </div>

          <div class="row g-3 mb-3">
             <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" class="form-control" id="clientPos" name="position"
                           placeholder="Poste" maxlength="255"
                           value="<?= htmlspecialchars($editTestim['position'] ?? '') ?>">
                    <label for="clientPos">Poste (ex: CEO)</label>
                </div>
            </div>
            <div class="col-md-6">
                 <div class="form-floating">
                    <input type="text" class="form-control" id="clientCompany" name="company"
                           placeholder="Entreprise" maxlength="255"
                           value="<?= htmlspecialchars($editTestim['company'] ?? '') ?>">
                    <label for="clientCompany">Entreprise (ex: Google)</label>
                </div>
            </div>
          </div>

          <div class="form-floating mb-3">
            <textarea class="form-control" id="content" name="content"
                      placeholder="Contenu" style="height:120px;" required><?= htmlspecialchars($editTestim['content'] ?? '') ?></textarea>
            <label for="content">Témoignage *</label>
          </div>

          <div class="mb-3 form-check form-switch">
            <input class="form-check-input" type="checkbox" id="isActive" name="is_active" value="1" 
                   <?= ($editTestim['is_active'] ?? 1) ? 'checked' : '' ?>>
            <label class="form-check-label" for="isActive">Afficher sur le site (Actif)</label>
          </div>

          <!-- Image Upload -->
          <div class="mb-4">
            <label class="form-label" style="font-size:13px; font-weight:600; color:var(--text-secondary);">Photo du client</label>
            <?php if (!empty($editTestim['image'])): ?>
              <div class="image-preview-container" id="testimImagePreview" style="display:block; margin-bottom:10px;">
                <img src="../../<?= htmlspecialchars($editTestim['image']) ?>" alt="Photo client">
                <button type="button" class="image-preview-remove"
                        onclick="removeImagePreview('testimImageInput','testimImagePreview','testimImageZone')">
                  <i class="bi bi-x"></i>
                </button>
              </div>
              <div class="image-upload-zone" id="testimImageZone" style="display:none;"
                   onclick="document.getElementById('testimImageInput').click()">
                <i class="bi bi-person-bounding-box"></i>
                <p><strong>Changer la photo</strong></p>
              </div>
            <?php else: ?>
              <div class="image-upload-zone" id="testimImageZone"
                   onclick="document.getElementById('testimImageInput').click()">
                <i class="bi bi-person-bounding-box"></i>
                <p><strong>Cliquez pour uploader</strong><br>
                   <span style="font-size:11px;">Carré recommandé (JPG, PNG)</span>
                </p>
              </div>
              <div class="image-preview-container" id="testimImagePreview">
                <img src="" alt="Aperçu">
                <button type="button" class="image-preview-remove"
                        onclick="removeImagePreview('testimImageInput','testimImagePreview','testimImageZone')">
                  <i class="bi bi-x"></i>
                </button>
              </div>
            <?php endif; ?>
            <input type="file" id="testimImageInput" name="image" accept="image/*" style="display:none;">
          </div>

          <div class="d-flex gap-3">
            <button type="submit" class="btn-bbc-primary">
              <i class="bi bi-save-fill"></i> <?= $editTestim ? 'Mettre à jour' : 'Ajouter le témoignage' ?>
            </button>
            <a href="index.php" class="btn-bbc-outline">Annuler</a>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

<?php else: ?>
<!-- ── Testimonials List ── -->
<div class="admin-card">
  <div class="admin-card-header">
    <h2 class="admin-card-title"><i class="bi bi-chat-quote-fill"></i> Liste des témoignages</h2>
  </div>
  <div class="admin-table-wrapper">
    <?php if (empty($testimonials)): ?>
      <div class="empty-state">
        <i class="bi bi-chat-quote"></i>
        <h3>Aucun avis</h3>
        <p>Ajoutez votre premier témoignage client.</p>
        <a href="?new=1" class="btn-bbc-primary"><i class="bi bi-plus-lg"></i> Nouvel avis</a>
      </div>
    <?php else: ?>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Photo</th>
            <th>Client</th>
            <th>Avis</th>
            <th>Note</th>
            <th>Statut</th>
            <th style="width:100px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($testimonials as $t): ?>
          <tr>
            <td style="width:50px;">
              <?php if ($t['image']): ?>
                <img src="../../<?= htmlspecialchars($t['image']) ?>" alt="" style="width:40px; height:40px; object-fit:cover; border-radius:50%;">
              <?php else: ?>
                <div style="width:40px; height:40px; background:#eee; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#999;">
                  <i class="bi bi-person-fill"></i>
                </div>
              <?php endif; ?>
            </td>
            <td>
              <div style="font-weight:600; font-size:13px;"><?= htmlspecialchars($t['client_name']) ?></div>
              <div style="font-size:11px; color:var(--text-secondary);">
                <?= htmlspecialchars($t['position'] ?? '') ?> 
                <?= ($t['position'] && $t['company']) ? '@' : '' ?> 
                <?= htmlspecialchars($t['company'] ?? '') ?>
              </div>
            </td>
            <td style="max-width:300px;">
              <div style="font-size:12px; color:#555; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                "<?= htmlspecialchars($t['content']) ?>"
              </div>
            </td>
            <td>
              <span class="badge bg-warning text-dark"><i class="bi bi-star-fill"></i> <?= $t['rating'] ?></span>
            </td>
            <td>
              <?php if ($t['is_active']): ?>
                <span class="badge-status badge-active">Actif</span>
              <?php else: ?>
                <span class="badge-status badge-inactive">Inactif</span>
              <?php endif; ?>
            </td>
            <td>
              <div style="display:flex; gap:4px;">
                <?php if (can('testimonials.edit')): ?>
                <a href="?edit=<?= $t['id'] ?>" class="btn-action edit" title="Modifier">
                  <i class="bi bi-pencil"></i>
                </a>
                <?php endif; ?>
                <?php if (can('testimonials.delete')): ?>
                <form method="POST" onsubmit="return confirmDelete('Supprimer cet avis ?')">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="delete_id" value="<?= $t['id'] ?>">
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
<?php endif; ?>

<?php include __DIR__ . '/../partials/footer.php'; ?>
