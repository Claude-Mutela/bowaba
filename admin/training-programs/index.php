<?php
/**
 * Training Programs Management — admin/training-programs/index.php
 */
require_once __DIR__ . '/../../kon/conn.php';
require_once __DIR__ . '/../partials/auth.php'; // loads session + $adminUser + permissions

$pageTitle  = 'Programmes de Formation';
$activePage = 'training-programs';
$adminBase  = '../';

// RBAC: admin (write) + editor/author (read-only)
requirePermission('training_programs.view');

$errors  = [];
$success = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'delete' && !empty($_POST['delete_id'])) {
    requirePermission('training_programs.delete');
    try {
      $conn->prepare("DELETE FROM training_programs WHERE id=:id")->execute([':id' => (int)$_POST['delete_id']]);
      $success = 'Programme supprimé.';
    } catch (Exception $e) {
      $errors[] = 'Erreur : ' . $e->getMessage();
    }
  }

  if (in_array($action, ['create', 'update'])) {
    requirePermission('training_programs.create');
    $title      = trim($_POST['title'] ?? '');
    $slug       = trim($_POST['slug'] ?? '');
    $desc       = trim($_POST['description'] ?? '');
    $duration   = trim($_POST['duration'] ?? '');
    $isActive   = isset($_POST['is_active']) ? 1 : 0;
    $editId     = (int)($_POST['edit_id'] ?? 0);

    if (!$title) $errors[] = 'Le titre est obligatoire.';

    // Auto-generate slug if empty
    if (!$slug && $title) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    }

    // Handle image upload
    $image = $_POST['existing_image'] ?? null;
    if (!empty($_FILES['image']['name'])) {
      $uploadDir = __DIR__ . '/../../assets/img/training/';
      if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
      $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
      $allowed = ['jpg','jpeg','png','webp','gif'];
      if (!in_array($ext, $allowed)) {
        $errors[] = 'Format d\'image non autorisé (JPG, PNG, WebP).';
      } else {
        $filename = uniqid('prog_') . '.' . $ext;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
          $image = 'assets/img/training/' . $filename;
        }
      }
    }

    if (empty($errors)) {
      try {
        if ($action === 'create') {
          $conn->prepare(
            "INSERT INTO training_programs (title, slug, description, duration, image, is_active)
             VALUES (:t,:s,:d,:dur,:img,:act)"
          )->execute([':t'=>$title,':s'=>$slug,':d'=>$desc,':dur'=>$duration,':img'=>$image,':act'=>$isActive]);
          $success = 'Programme créé avec succès.';
        } else {
          $conn->prepare(
            "UPDATE training_programs SET title=:t, slug=:s, description=:d, duration=:dur, image=:img, is_active=:act WHERE id=:id"
          )->execute([':t'=>$title,':s'=>$slug,':d'=>$desc,':dur'=>$duration,':img'=>$image,':act'=>$isActive,':id'=>$editId]);
          $success = 'Programme mis à jour.';
        }
        header('Location: index.php?saved=1');
        exit;
      } catch (PDOException $e) {
        $errors[] = $e->getCode() === '23000' ? 'Ce slug est déjà utilisé.' : $e->getMessage();
      }
    }
  }
}

// Load programs
try {
  $programs = $conn->query(
    "SELECT * FROM training_programs ORDER BY title ASC"
  )->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  $programs = [];
}

// Load program for editing
$editProg = null;
if (!empty($_GET['edit'])) {
  foreach ($programs as $p) {
    if ($p['id'] == (int)$_GET['edit']) { $editProg = $p; break; }
  }
}

include __DIR__ . '/../partials/header.php';
?>

<div class="admin-breadcrumb">
  <a href="../index.php"><i class="bi bi-house-fill"></i> Dashboard</a>
  <span class="sep">/</span>
  <span>Formations</span>
</div>

<div class="page-header">
  <div class="page-header-left">
    <h1>Gestion des Programmes de Formation</h1>
    <p><?= count($programs) ?> programme<?= count($programs) > 1 ? 's' : '' ?> disponible<?= count($programs) > 1 ? 's' : '' ?></p>
  </div>
  <?php if ($editProg): ?>
    <a href="index.php" class="btn-bbc-outline"><i class="bi bi-list-ul"></i> Voir la liste</a>
  <?php elseif (can('training_programs.create')): ?>
    <a href="?new=1" class="btn-bbc-primary"><i class="bi bi-plus-lg"></i> Nouveau programme</a>
  <?php endif; ?>
</div>

<?php if (isset($_GET['saved'])): ?>
  <div class="admin-alert admin-alert-success" data-auto-dismiss="3000">
    <i class="bi bi-check-circle-fill"></i> Programme enregistré.
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

<?php if (($editProg || isset($_GET['new'])) && can('training_programs.create')): ?>
<!-- ── Program Form (Edit / Create) ── -->
<div class="row g-4">
  <div class="col-lg-8">

    <div class="admin-card mb-4">
      <div class="admin-card-header">
        <h2 class="admin-card-title">
          <i class="bi bi-book-half"></i>
          <?= $editProg ? 'Modifier : ' . htmlspecialchars($editProg['title']) : 'Nouveau programme' ?>
        </h2>
      </div>
      <div class="admin-card-body">
        <form method="POST" enctype="multipart/form-data">
          <input type="hidden" name="action" value="<?= $editProg ? 'update' : 'create' ?>">
          <?php if ($editProg): ?>
            <input type="hidden" name="edit_id" value="<?= $editProg['id'] ?>">
            <input type="hidden" name="existing_image" value="<?= htmlspecialchars($editProg['image'] ?? '') ?>">
          <?php endif; ?>

          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="progTitle" name="title"
                   placeholder="Titre" required maxlength="255"
                   value="<?= htmlspecialchars($editProg['title'] ?? '') ?>">
            <label for="progTitle">Titre de la formation *</label>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
               <div class="form-floating">
                  <input type="text" class="form-control" id="progSlug" name="slug"
                         placeholder="Slug" maxlength="255"
                         value="<?= htmlspecialchars($editProg['slug'] ?? '') ?>">
                  <label for="progSlug">Slug (URL) *</label>
               </div>
            </div>
            <div class="col-md-6">
               <div class="form-floating">
                  <input type="text" class="form-control" id="progDuration" name="duration"
                         placeholder="Durée" maxlength="100"
                         value="<?= htmlspecialchars($editProg['duration'] ?? '') ?>">
                  <label for="progDuration"><i class="bi bi-clock me-1"></i> Durée (ex: 3 mois)</label>
               </div>
            </div>
          </div>

          <div class="form-floating mb-3">
            <textarea class="form-control" id="progDesc" name="description"
                      placeholder="Description" style="height:120px;"><?= htmlspecialchars($editProg['description'] ?? '') ?></textarea>
            <label for="progDesc">Description du programme</label>
          </div>

          <div class="mb-3 form-check form-switch">
            <input class="form-check-input" type="checkbox" id="isActive" name="is_active" value="1" 
                   <?= ($editProg['is_active'] ?? 1) ? 'checked' : '' ?>>
            <label class="form-check-label" for="isActive">Afficher sur le site (Actif)</label>
          </div>

          <!-- Image Upload -->
          <div class="mb-4">
            <label class="form-label" style="font-size:13px; font-weight:600; color:var(--text-secondary);">Image illustration</label>
            <?php if (!empty($editProg['image'])): ?>
              <div class="image-preview-container" id="progImagePreview" style="display:block; margin-bottom:10px;">
                <img src="../../<?= htmlspecialchars($editProg['image']) ?>" alt="Image">
                <button type="button" class="image-preview-remove"
                        onclick="removeImagePreview('progImageInput','progImagePreview','progImageZone')">
                  <i class="bi bi-x"></i>
                </button>
              </div>
              <div class="image-upload-zone" id="progImageZone" style="display:none;"
                   onclick="document.getElementById('progImageInput').click()">
                <i class="bi bi-cloud-arrow-up"></i>
                <p><strong>Changer l'image</strong></p>
              </div>
            <?php else: ?>
              <div class="image-upload-zone" id="progImageZone"
                   onclick="document.getElementById('progImageInput').click()">
                <i class="bi bi-cloud-arrow-up"></i>
                <p><strong>Cliquez pour uploader</strong><br>
                   <span style="font-size:11px;">JPG, PNG, WebP</span>
                </p>
              </div>
              <div class="image-preview-container" id="progImagePreview">
                <img src="" alt="Aperçu">
                <button type="button" class="image-preview-remove"
                        onclick="removeImagePreview('progImageInput','progImagePreview','progImageZone')">
                  <i class="bi bi-x"></i>
                </button>
              </div>
            <?php endif; ?>
            <input type="file" id="progImageInput" name="image" accept="image/*" style="display:none;">
          </div>

          <div class="d-flex gap-3">
            <button type="submit" class="btn-bbc-primary">
              <i class="bi bi-save-fill"></i> <?= $editProg ? 'Mettre à jour' : 'Créer le programme' ?>
            </button>
            <a href="index.php" class="btn-bbc-outline">Annuler</a>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

<?php else: ?>
<!-- ── Programs List ── -->
<div class="admin-card">
  <div class="admin-card-header">
    <h2 class="admin-card-title"><i class="bi bi-book-half"></i> Liste des programmes</h2>
  </div>
  <div class="admin-table-wrapper">
    <?php if (empty($programs)): ?>
      <div class="empty-state">
        <i class="bi bi-journal-album"></i>
        <h3>Aucun programme</h3>
        <p>Créez votre premier programme de formation.</p>
        <a href="?new=1" class="btn-bbc-primary"><i class="bi bi-plus-lg"></i> Nouveau programme</a>
      </div>
    <?php else: ?>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Image</th>
            <th>Programme</th>
            <th>Durée</th>
            <th>Statut</th>
            <th style="width:100px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($programs as $p): ?>
          <tr>
            <td style="width:60px;">
              <?php if ($p['image']): ?>
                <img src="../../<?= htmlspecialchars($p['image']) ?>" alt="" style="width:40px; height:40px; object-fit:cover; border-radius:6px;">
              <?php else: ?>
                <div style="width:40px; height:40px; background:#eee; border-radius:6px;"></div>
              <?php endif; ?>
            </td>
            <td>
              <div style="font-weight:600; font-size:13px;"><?= htmlspecialchars($p['title']) ?></div>
              <div style="font-size:11px; color:var(--text-secondary); max-width:250px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                <?= htmlspecialchars($p['description'] ?? '') ?>
              </div>
            </td>
            <td>
              <?= htmlspecialchars($p['duration'] ?? '—') ?>
            </td>
            <td>
              <?php if ($p['is_active']): ?>
                <span class="badge-status badge-active">Actif</span>
              <?php else: ?>
                <span class="badge-status badge-inactive">Inactif</span>
              <?php endif; ?>
            </td>
            <td>
              <div style="display:flex; gap:4px;">
                <?php if (can('training_programs.edit')): ?>
                <a href="?edit=<?= $p['id'] ?>" class="btn-action edit" title="Modifier">
                  <i class="bi bi-pencil"></i>
                </a>
                <?php endif; ?>
                <?php if (can('training_programs.delete')): ?>
                <form method="POST" onsubmit="return confirmDelete('Supprimer ce programme ?')">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="delete_id" value="<?= $p['id'] ?>">
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
