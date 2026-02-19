<?php
/**
 * Partners Management — admin/partners/index.php
 */
require_once __DIR__ . '/../../kon/conn.php';
require_once __DIR__ . '/../partials/auth.php'; // loads session + $adminUser + permissions

$pageTitle  = 'Partenaires';
$activePage = 'partners';
$adminBase  = '../';

// RBAC: admin (write) + editor/author (read-only)
requirePermission('partners.view');

$errors  = [];
$success = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'delete' && !empty($_POST['delete_id'])) {
    requirePermission('partners.delete');
    try {
      $conn->prepare("DELETE FROM partners WHERE id=:id")->execute([':id' => (int)$_POST['delete_id']]);
      $success = 'Partenaire supprimé.';
    } catch (Exception $e) {
      $errors[] = 'Erreur : ' . $e->getMessage();
    }
  }

  if (in_array($action, ['create', 'update'])) {
    requirePermission('partners.create');
    $name     = trim($_POST['name'] ?? '');
    $website  = trim($_POST['website'] ?? '');
    $desc     = trim($_POST['description'] ?? '');
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    $editId   = (int)($_POST['edit_id'] ?? 0);

    if (!$name) $errors[] = 'Le nom est obligatoire.';

    // Handle logo upload
    $logo = $_POST['existing_logo'] ?? null;
    if (!empty($_FILES['logo']['name'])) {
      $uploadDir = __DIR__ . '/../../assets/img/partners/';
      if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
      $ext     = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
      $allowed = ['jpg','jpeg','png','webp','gif','svg'];
      if (!in_array($ext, $allowed)) {
        $errors[] = 'Format d\'image non autorisé (JPG, PNG, WebP, SVG).';
      } else {
        $filename = uniqid('part_') . '.' . $ext;
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir . $filename)) {
          $logo = 'assets/img/partners/' . $filename;
        }
      }
    }

    if (empty($errors)) {
      try {
        if ($action === 'create') {
          $conn->prepare(
            "INSERT INTO partners (name, logo, website, description, is_active)
             VALUES (:n,:l,:w,:d,:act)"
          )->execute([':n'=>$name,':l'=>$logo,':w'=>$website,':d'=>$desc,':act'=>$isActive]);
          $success = 'Partenaire ajouté avec succès.';
        } else {
          $conn->prepare(
            "UPDATE partners SET name=:n, logo=:l, website=:w, description=:d, is_active=:act WHERE id=:id"
          )->execute([':n'=>$name,':l'=>$logo,':w'=>$website,':d'=>$desc,':act'=>$isActive,':id'=>$editId]);
          $success = 'Partenaire mis à jour.';
        }
        header('Location: index.php?saved=1');
        exit;
      } catch (PDOException $e) {
        $errors[] = 'Erreur SGBD : ' . $e->getMessage();
      }
    }
  }
}

// Load partners
try {
  $partners = $conn->query(
    "SELECT * FROM partners ORDER BY created_at DESC"
  )->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  $partners = [];
}

// Load partner for editing
$editPart = null;
if (!empty($_GET['edit'])) {
  foreach ($partners as $p) {
    if ($p['id'] == (int)$_GET['edit']) { $editPart = $p; break; }
  }
}

include __DIR__ . '/../partials/header.php';
?>

<div class="admin-breadcrumb">
  <a href="../index.php"><i class="bi bi-house-fill"></i> Dashboard</a>
  <span class="sep">/</span>
  <span>Partenaires</span>
</div>

<div class="page-header">
  <div class="page-header-left">
    <h1>Gestion des Partenaires</h1>
    <p><?= count($partners) ?> entreprise<?= count($partners) > 1 ? 's' : '' ?> accompagnée<?= count($partners) > 1 ? 's' : '' ?></p>
  </div>
  <?php if ($editPart): ?>
    <a href="index.php" class="btn-bbc-outline"><i class="bi bi-list-ul"></i> Voir la liste</a>
  <?php elseif (can('partners.create')): ?>
    <a href="?new=1" class="btn-bbc-primary"><i class="bi bi-plus-lg"></i> Nouveau partenaire</a>
  <?php endif; ?>
</div>

<?php if (isset($_GET['saved'])): ?>
  <div class="admin-alert admin-alert-success" data-auto-dismiss="3000">
    <i class="bi bi-check-circle-fill"></i> Partenaire enregistré.
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

<?php if (($editPart || isset($_GET['new'])) && can('partners.create')): ?>
<!-- ── Partner Form (Edit / Create) ── -->
<div class="row g-4">
  <div class="col-lg-8">

    <div class="admin-card mb-4">
      <div class="admin-card-header">
        <h2 class="admin-card-title">
          <i class="bi bi-building"></i>
          <?= $editPart ? 'Modifier : ' . htmlspecialchars($editPart['name']) : 'Nouveau partenaire' ?>
        </h2>
      </div>
      <div class="admin-card-body">
        <form method="POST" enctype="multipart/form-data">
          <input type="hidden" name="action" value="<?= $editPart ? 'update' : 'create' ?>">
          <?php if ($editPart): ?>
            <input type="hidden" name="edit_id" value="<?= $editPart['id'] ?>">
            <input type="hidden" name="existing_logo" value="<?= htmlspecialchars($editPart['logo'] ?? '') ?>">
          <?php endif; ?>

          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="partnerName" name="name"
                   placeholder="Nom" required maxlength="255"
                   value="<?= htmlspecialchars($editPart['name'] ?? '') ?>">
            <label for="partnerName">Nom de l'entreprise *</label>
          </div>

          <div class="form-floating mb-3">
            <input type="url" class="form-control" id="partnerWeb" name="website"
                   placeholder="Site web" maxlength="255"
                   value="<?= htmlspecialchars($editPart['website'] ?? '') ?>">
            <label for="partnerWeb"><i class="bi bi-link-45deg me-1"></i> Site web (https://...)</label>
          </div>

          <div class="form-floating mb-3">
            <textarea class="form-control" id="partnerDesc" name="description"
                      placeholder="Description" style="height:90px;"><?= htmlspecialchars($editPart['description'] ?? '') ?></textarea>
            <label for="partnerDesc">Courte description (optionnel)</label>
          </div>

          <div class="mb-3 form-check form-switch">
            <input class="form-check-input" type="checkbox" id="isActive" name="is_active" value="1" 
                   <?= ($editPart['is_active'] ?? 1) ? 'checked' : '' ?>>
            <label class="form-check-label" for="isActive">Afficher sur le site</label>
          </div>

          <!-- Logo Upload -->
          <div class="mb-4">
            <label class="form-label" style="font-size:13px; font-weight:600; color:var(--text-secondary);">Logo de l'entreprise</label>
            <?php if (!empty($editPart['logo'])): ?>
              <div class="image-preview-container" id="logoPreview" style="display:block; margin-bottom:10px;">
                <img src="../../<?= htmlspecialchars($editPart['logo']) ?>" alt="Logo">
                <button type="button" class="image-preview-remove"
                        onclick="removeImagePreview('logoInput','logoPreview','logoZone')">
                  <i class="bi bi-x"></i>
                </button>
              </div>
              <div class="image-upload-zone" id="logoZone" style="display:none;"
                   onclick="document.getElementById('logoInput').click()">
                <i class="bi bi-cloud-arrow-up"></i>
                <p><strong>Changer le logo</strong></p>
              </div>
            <?php else: ?>
              <div class="image-upload-zone" id="logoZone"
                   onclick="document.getElementById('logoInput').click()">
                <i class="bi bi-cloud-arrow-up"></i>
                <p><strong>Cliquez pour uploader</strong><br>
                   <span style="font-size:11px;">Fond transparent recommandé (PNG, SVG)</span>
                </p>
              </div>
              <div class="image-preview-container" id="logoPreview">
                <img src="" alt="Aperçu">
                <button type="button" class="image-preview-remove"
                        onclick="removeImagePreview('logoInput','logoPreview','logoZone')">
                  <i class="bi bi-x"></i>
                </button>
              </div>
            <?php endif; ?>
            <input type="file" id="logoInput" name="logo" accept="image/*" style="display:none;">
          </div>

          <div class="d-flex gap-3">
            <button type="submit" class="btn-bbc-primary">
              <i class="bi bi-save-fill"></i> <?= $editPart ? 'Mettre à jour' : 'Ajouter partenaire' ?>
            </button>
            <a href="index.php" class="btn-bbc-outline">Annuler</a>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

<?php else: ?>
<!-- ── Partners List ── -->
<div class="admin-card">
  <div class="admin-card-header">
    <h2 class="admin-card-title"><i class="bi bi-building"></i> Liste des partenaires</h2>
  </div>
  <div class="admin-table-wrapper">
    <?php if (empty($partners)): ?>
      <div class="empty-state">
        <i class="bi bi-building"></i>
        <h3>Aucun partenaire</h3>
        <p>Ajoutez les entreprises que vous avez accompagnées.</p>
        <a href="?new=1" class="btn-bbc-primary"><i class="bi bi-plus-lg"></i> Nouveau partenaire</a>
      </div>
    <?php else: ?>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Logo</th>
            <th>Entreprise</th>
            <th>Site Web</th>
            <th>Statut</th>
            <th style="width:100px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($partners as $p): ?>
          <tr>
            <td style="width:80px;">
              <?php if ($p['logo']): ?>
                <img src="../../<?= htmlspecialchars($p['logo']) ?>" alt="" style="max-width:60px; max-height:40px; object-fit:contain;">
              <?php else: ?>
                <span style="color:#ccc;">—</span>
              <?php endif; ?>
            </td>
            <td>
              <div style="font-weight:600; font-size:13px;"><?= htmlspecialchars($p['name']) ?></div>
              <div style="font-size:11px; color:var(--text-secondary); max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                <?= htmlspecialchars($p['description'] ?? '') ?>
              </div>
            </td>
            <td>
              <?php if ($p['website']): ?>
                <a href="<?= htmlspecialchars($p['website']) ?>" target="_blank" style="font-size:12px; color:var(--bbc-blue);">
                  <i class="bi bi-box-arrow-up-right me-1"></i> Visiter
                </a>
              <?php else: ?>
                <span style="color:#ccc; font-size:12px;">—</span>
              <?php endif; ?>
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
                <?php if (can('partners.edit')): ?>
                <a href="?edit=<?= $p['id'] ?>" class="btn-action edit" title="Modifier">
                  <i class="bi bi-pencil"></i>
                </a>
                <?php endif; ?>
                <?php if (can('partners.delete')): ?>
                <form method="POST" onsubmit="return confirmDelete('Supprimer ce partenaire ?')">
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
