<?php
/**
 * Services Management — admin/services/index.php
 */
require_once __DIR__ . '/../../kon/conn.php';
require_once __DIR__ . '/../partials/auth.php'; // loads session + $adminUser + permissions

$pageTitle  = 'Services';
$activePage = 'services';
$adminBase  = '../';

// RBAC: admin (write) + editor/author (read-only)
requirePermission('services.view');

$errors  = [];
$success = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'delete' && !empty($_POST['delete_id'])) {
    requirePermission('services.delete');
    try {
      $conn->prepare("DELETE FROM services WHERE id=:id")->execute([':id' => (int)$_POST['delete_id']]);
      $success = 'Service supprimé.';
    } catch (Exception $e) {
      $errors[] = 'Erreur : ' . $e->getMessage();
    }
  }

  if (in_array($action, ['create', 'update'])) {
    requirePermission('services.create');
    $title        = trim($_POST['title'] ?? '');
    $slug         = trim($_POST['slug'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $content      = $_POST['content'] ?? '';
    $icon         = trim($_POST['icon'] ?? '');
    $displayOrder = (int)($_POST['display_order'] ?? 0);
    $status       = in_array($_POST['status'] ?? '', ['active','inactive']) ? $_POST['status'] : 'active';
    $editId       = (int)($_POST['edit_id'] ?? 0);

    if (!$title) $errors[] = 'Le titre est obligatoire.';
    if (!$slug)  $errors[] = 'Le slug est obligatoire.';

    // Handle image upload
    $image = $_POST['existing_image'] ?? null;
    if (!empty($_FILES['image']['name'])) {
      $uploadDir = __DIR__ . '/../../assets/img/services/';
      if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
      $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
      $allowed = ['jpg','jpeg','png','webp','gif','svg'];
      if (!in_array($ext, $allowed)) {
        $errors[] = 'Format d\'image non autorisé.';
      } else {
        $filename = uniqid('svc_') . '.' . $ext;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
          $image = 'assets/img/services/' . $filename;
        }
      }
    }

    if (empty($errors)) {
      try {
        if ($action === 'create') {
          $conn->prepare(
            "INSERT INTO services (title, slug, description, content, icon, image, display_order, status)
             VALUES (:t,:s,:d,:c,:i,:img,:ord,:st)"
          )->execute([':t'=>$title,':s'=>$slug,':d'=>$description,':c'=>$content,
                      ':i'=>$icon,':img'=>$image,':ord'=>$displayOrder,':st'=>$status]);
          $success = 'Service créé avec succès.';
        } else {
          $conn->prepare(
            "UPDATE services SET title=:t, slug=:s, description=:d, content=:c,
             icon=:i, image=:img, display_order=:ord, status=:st WHERE id=:id"
          )->execute([':t'=>$title,':s'=>$slug,':d'=>$description,':c'=>$content,
                      ':i'=>$icon,':img'=>$image,':ord'=>$displayOrder,':st'=>$status,':id'=>$editId]);
          $success = 'Service mis à jour.';
        }
        header('Location: index.php?saved=1');
        exit;
      } catch (PDOException $e) {
        $errors[] = $e->getCode() === '23000' ? 'Ce slug est déjà utilisé.' : $e->getMessage();
      }
    }
  }

  // Update display order inline
  if ($action === 'reorder' && !empty($_POST['orders'])) {
    try {
      $orders = json_decode($_POST['orders'], true);
      $upd = $conn->prepare("UPDATE services SET display_order=:ord WHERE id=:id");
      foreach ($orders as $svcId => $ord) {
        $upd->execute([':ord' => (int)$ord, ':id' => (int)$svcId]);
      }
      echo json_encode(['success' => true]);
      exit;
    } catch (Exception $e) {
      echo json_encode(['error' => $e->getMessage()]);
      exit;
    }
  }
}

// Load services
try {
  $services = $conn->query(
    "SELECT * FROM services ORDER BY display_order ASC, title ASC"
  )->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  $services = [];
}

// Load service for editing
$editSvc = null;
if (!empty($_GET['edit'])) {
  foreach ($services as $s) {
    if ($s['id'] == (int)$_GET['edit']) { $editSvc = $s; break; }
  }
}

include __DIR__ . '/../partials/header.php';
?>

<div class="admin-breadcrumb">
  <a href="../index.php"><i class="bi bi-house-fill"></i> Dashboard</a>
  <span class="sep">/</span>
  <span>Services</span>
</div>

<div class="page-header">
  <div class="page-header-left">
    <h1>Gestion des services</h1>
    <p><?= count($services) ?> service<?= count($services) > 1 ? 's' : '' ?> configuré<?= count($services) > 1 ? 's' : '' ?></p>
  </div>
  <?php if ($editSvc): ?>
    <a href="index.php" class="btn-bbc-outline"><i class="bi bi-list-ul"></i> Voir la liste</a>
  <?php elseif (can('services.create')): ?>
    <a href="?new=1" class="btn-bbc-primary"><i class="bi bi-plus-lg"></i> Nouveau service</a>
  <?php endif; ?>
</div>

<?php if (isset($_GET['saved'])): ?>
  <div class="admin-alert admin-alert-success" data-auto-dismiss="3000">
    <i class="bi bi-check-circle-fill"></i> Service enregistré avec succès.
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

<?php if (($editSvc || isset($_GET['new'])) && can('services.create')): ?>
<!-- ── Service Form (Edit / Create) ── -->
<div class="row g-4">
  <div class="col-lg-8">

    <div class="admin-card mb-4">
      <div class="admin-card-header">
        <h2 class="admin-card-title">
          <i class="bi bi-briefcase-fill"></i>
          <?= $editSvc ? 'Modifier : ' . htmlspecialchars($editSvc['title']) : 'Nouveau service' ?>
        </h2>
      </div>
      <div class="admin-card-body">
        <form method="POST" enctype="multipart/form-data">
          <input type="hidden" name="action" value="<?= $editSvc ? 'update' : 'create' ?>">
          <?php if ($editSvc): ?>
            <input type="hidden" name="edit_id" value="<?= $editSvc['id'] ?>">
            <input type="hidden" name="existing_image" value="<?= htmlspecialchars($editSvc['image'] ?? '') ?>">
          <?php endif; ?>

          <div class="row g-3 mb-3">
            <div class="col-md-8">
              <div class="form-floating">
                <input type="text" class="form-control" id="serviceTitle" name="title"
                       placeholder="Titre" required maxlength="255"
                       value="<?= htmlspecialchars($editSvc['title'] ?? '') ?>">
                <label for="serviceTitle">Titre du service *</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input type="number" class="form-control" id="displayOrder" name="display_order"
                       placeholder="Ordre" min="0" max="999"
                       value="<?= htmlspecialchars($editSvc['display_order'] ?? '0') ?>">
                <label for="displayOrder"><i class="bi bi-sort-numeric-up"></i> Ordre d'affichage</label>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label" style="font-size:13px; font-weight:600; color:var(--text-secondary);">Slug *</label>
            <div class="input-group">
              <span class="input-group-text" style="font-size:12px; background:#f8fafc; color:var(--text-secondary);">/services/</span>
              <input type="text" class="form-control" id="serviceSlug" name="slug"
                     placeholder="mon-service" required maxlength="255"
                     value="<?= htmlspecialchars($editSvc['slug'] ?? '') ?>">
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="serviceIcon" name="icon"
                       placeholder="Icône" maxlength="100"
                       value="<?= htmlspecialchars($editSvc['icon'] ?? '') ?>">
                <label for="serviceIcon"><i class="bi bi-stars me-1"></i> Classe icône (ex: bi bi-bank)</label>
              </div>
              <div style="margin-top:8px; font-size:12px; color:var(--text-secondary);">
                Aperçu : <i class="<?= htmlspecialchars($editSvc['icon'] ?? 'bi bi-briefcase') ?>" id="iconPreview" style="font-size:20px; color:var(--bbc-blue);"></i>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <select class="form-select" name="status" id="serviceStatus">
                  <option value="active"   <?= ($editSvc['status'] ?? 'active') === 'active'   ? 'selected' : '' ?>>✅ Actif</option>
                  <option value="inactive" <?= ($editSvc['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>⏸ Inactif</option>
                </select>
                <label for="serviceStatus">Statut</label>
              </div>
            </div>
          </div>

          <div class="form-floating mb-3">
            <textarea class="form-control" id="serviceDesc" name="description"
                      placeholder="Description courte" style="height:90px;"><?= htmlspecialchars($editSvc['description'] ?? '') ?></textarea>
            <label for="serviceDesc">Description courte</label>
          </div>

          <!-- TinyMCE Content -->
          <div class="mb-3">
            <label class="form-label" style="font-size:13px; font-weight:600; color:var(--text-secondary);">
              <i class="bi bi-body-text me-1"></i> Contenu détaillé
            </label>
            <div class="tinymce-wrapper">
              <textarea class="tinymce-editor" id="serviceContent" name="content"><?= $editSvc['content'] ?? '' ?></textarea>
            </div>
          </div>

          <!-- Image Upload -->
          <div class="mb-4">
            <label class="form-label" style="font-size:13px; font-weight:600; color:var(--text-secondary);">Image d'illustration</label>
            <?php if (!empty($editSvc['image'])): ?>
              <div class="image-preview-container" id="serviceImagePreview" style="display:block; margin-bottom:10px;">
                <img src="../../<?= htmlspecialchars($editSvc['image']) ?>" alt="Image service">
                <button type="button" class="image-preview-remove"
                        onclick="removeImagePreview('serviceImageInput','serviceImagePreview','serviceImageZone')">
                  <i class="bi bi-x"></i>
                </button>
              </div>
              <div class="image-upload-zone" id="serviceImageZone" style="display:none;"
                   onclick="document.getElementById('serviceImageInput').click()">
                <i class="bi bi-cloud-arrow-up"></i>
                <p><strong>Changer l'image</strong></p>
              </div>
            <?php else: ?>
              <div class="image-upload-zone" id="serviceImageZone"
                   onclick="document.getElementById('serviceImageInput').click()">
                <i class="bi bi-cloud-arrow-up"></i>
                <p><strong>Cliquez pour uploader</strong><br>
                   <span style="font-size:11px;">JPG, PNG, WebP, SVG</span>
                </p>
              </div>
              <div class="image-preview-container" id="serviceImagePreview">
                <img src="" alt="Aperçu">
                <button type="button" class="image-preview-remove"
                        onclick="removeImagePreview('serviceImageInput','serviceImagePreview','serviceImageZone')">
                  <i class="bi bi-x"></i>
                </button>
              </div>
            <?php endif; ?>
            <input type="file" id="serviceImageInput" name="image" accept="image/*" style="display:none;">
          </div>

          <div class="d-flex gap-3">
            <button type="submit" class="btn-bbc-primary">
              <i class="bi bi-save-fill"></i> <?= $editSvc ? 'Mettre à jour' : 'Créer le service' ?>
            </button>
            <a href="index.php" class="btn-bbc-outline">Annuler</a>
          </div>
        </form>
      </div>
    </div>

  </div>

  <!-- Preview Panel -->
  <div class="col-lg-4">
    <div class="admin-card">
      <div class="admin-card-header">
        <h2 class="admin-card-title"><i class="bi bi-eye-fill"></i> Aperçu</h2>
      </div>
      <div class="admin-card-body text-center" style="padding:30px 20px;">
        <div style="width:64px; height:64px; border-radius:12px; background:var(--bbc-blue-light); display:flex; align-items:center; justify-content:center; margin:0 auto 16px; font-size:28px; color:var(--bbc-blue);">
          <i class="<?= htmlspecialchars($editSvc['icon'] ?? 'bi bi-briefcase') ?>"></i>
        </div>
        <h3 style="font-family:'Raleway',sans-serif; font-size:16px; font-weight:700; margin-bottom:8px;">
          <?= htmlspecialchars($editSvc['title'] ?? 'Titre du service') ?>
        </h3>
        <p style="font-size:13px; color:var(--text-secondary);">
          <?= htmlspecialchars($editSvc['description'] ?? 'Description courte du service.') ?>
        </p>
        <div style="display:inline-flex; align-items:center; gap:6px; font-size:12px; color:var(--text-secondary);">
          <i class="bi bi-sort-numeric-up"></i> Ordre : <?= $editSvc['display_order'] ?? 0 ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php else: ?>
<!-- ── Services List ── -->
<div class="admin-card">
  <div class="admin-card-header">
    <h2 class="admin-card-title"><i class="bi bi-briefcase-fill"></i> Liste des services</h2>
    <div class="filter-bar">
      <div class="search-input-wrapper">
        <i class="bi bi-search"></i>
        <input type="text" id="serviceSearch" class="form-control" placeholder="Rechercher…">
      </div>
    </div>
  </div>
  <div class="admin-table-wrapper">
    <?php if (empty($services)): ?>
      <div class="empty-state">
        <i class="bi bi-briefcase"></i>
        <h3>Aucun service</h3>
        <p>Créez votre premier service.</p>
        <a href="?new=1" class="btn-bbc-primary"><i class="bi bi-plus-lg"></i> Nouveau service</a>
      </div>
    <?php else: ?>
      <table class="admin-table" id="servicesTable">
        <thead>
          <tr>
            <th style="width:50px;">Ordre</th>
            <th>Titre</th>
            <th>Slug</th>
            <th>Icône</th>
            <th>Statut</th>
            <th style="width:100px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($services as $svc): ?>
          <tr>
            <td>
              <div style="display:flex; align-items:center; gap:8px;">
                <span class="drag-handle" title="Réordonner"><i class="bi bi-grip-vertical"></i></span>
                <input type="number" class="order-input form-control form-control-sm"
                       value="<?= $svc['display_order'] ?>"
                       style="width:55px; text-align:center; font-weight:700;"
                       data-id="<?= $svc['id'] ?>">
              </div>
            </td>
            <td>
              <div style="font-weight:600; font-size:13px;"><?= htmlspecialchars($svc['title']) ?></div>
              <div style="font-size:11px; color:var(--text-secondary); max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                <?= htmlspecialchars($svc['description'] ?? '') ?>
              </div>
            </td>
            <td>
              <code style="font-size:12px; background:var(--bbc-blue-light); color:var(--bbc-blue); padding:2px 6px; border-radius:4px;">
                <?= htmlspecialchars($svc['slug']) ?>
              </code>
            </td>
            <td>
              <?php if ($svc['icon']): ?>
                <i class="<?= htmlspecialchars($svc['icon']) ?>" style="font-size:20px; color:var(--bbc-blue);" title="<?= htmlspecialchars($svc['icon']) ?>"></i>
              <?php else: ?>
                <span style="color:var(--text-secondary); font-size:12px;">—</span>
              <?php endif; ?>
            </td>
            <td>
              <span class="badge-status <?= $svc['status'] === 'active' ? 'badge-active' : 'badge-inactive' ?>">
                <?= $svc['status'] === 'active' ? 'Actif' : 'Inactif' ?>
              </span>
            </td>
            <td>
              <div style="display:flex; gap:4px;">
                <?php if (can('services.edit')): ?>
                <a href="?edit=<?= $svc['id'] ?>" class="btn-action edit" title="Modifier">
                  <i class="bi bi-pencil"></i>
                </a>
                <?php endif; ?>
                <a href="../../service-details.php?id=<?= $svc['id'] ?>" class="btn-action view" title="Voir" target="_blank">
                  <i class="bi bi-eye"></i>
                </a>
                <?php if (can('services.delete')): ?>
                <form method="POST" onsubmit="return confirmDelete('Supprimer ce service ?')">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="delete_id" value="<?= $svc['id'] ?>">
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

<script>
// Icon preview live update
document.addEventListener('DOMContentLoaded', () => {
  const iconInput   = document.getElementById('serviceIcon');
  const iconPreview = document.getElementById('iconPreview');
  if (iconInput && iconPreview) {
    iconInput.addEventListener('input', () => {
      iconPreview.className = iconInput.value.trim() || 'bi bi-briefcase';
    });
  }
});
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
