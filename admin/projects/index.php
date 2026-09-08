<?php
/**
 * Projects Management — admin/projects/index.php
 */
require_once __DIR__ . '/../../kon/conn.php';
require_once __DIR__ . '/../partials/auth.php'; // loads session + $adminUser + permissions

$pageTitle  = 'Projets';
$activePage = 'projects';
$adminBase  = '../';

// RBAC: admin (write) + editor/author (read-only)
requirePermission('projects.view');

$errors  = [];
$success = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'delete' && !empty($_POST['delete_id'])) {
    requirePermission('projects.delete');
    try {
      $conn->prepare("DELETE FROM projects WHERE id=:id")->execute([':id' => (int)$_POST['delete_id']]);
      $success = 'Projet supprimé.';
    } catch (Exception $e) {
      $errors[] = 'Erreur : ' . $e->getMessage();
    }
  }

  if (in_array($action, ['create', 'update'])) {
    requirePermission('projects.create');
    $title        = trim($_POST['title'] ?? '');
    $slug         = trim($_POST['slug'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $clientName   = trim($_POST['client_name'] ?? '');
    $category     = trim($_POST['category'] ?? '');
    $status       = in_array($_POST['status'] ?? '', ['completed','in_progress']) ? $_POST['status'] : 'completed';
    $completionDate = !empty($_POST['completion_date']) ? $_POST['completion_date'] : null;
    $editId       = (int)($_POST['edit_id'] ?? 0);

    if (!$title) $errors[] = 'Le titre est obligatoire.';
    
    // Auto-generate slug if empty
    if (!$slug && $title) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    }

    // Handle image upload
    $image = $_POST['existing_image'] ?? null;
    if (!empty($_FILES['image']['name'])) {
      $uploadDir = __DIR__ . '/../../assets/img/projects/';
      if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
      $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
      $allowed = ['jpg','jpeg','png','webp','gif'];
      if (!in_array($ext, $allowed)) {
        $errors[] = 'Format d\'image non autorisé.';
      } else {
        $filename = uniqid('proj_') . '.' . $ext;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $filename)) {
          $image = 'assets/img/projects/' . $filename;
        }
      }
    }

    if (empty($errors)) {
      try {
        if ($action === 'create') {
          $conn->prepare(
            "INSERT INTO projects (title, slug, description, image, client_name, category, completion_date, status)
             VALUES (:t,:s,:d,:img,:c,:cat,:date,:st)"
          )->execute([':t'=>$title,':s'=>$slug,':d'=>$description,':img'=>$image,
                      ':c'=>$clientName,':cat'=>$category,':date'=>$completionDate,':st'=>$status]);
          $success = 'Projet créé avec succès.';
        } else {
          $conn->prepare(
            "UPDATE projects SET title=:t, slug=:s, description=:d, image=:img, 
             client_name=:c, category=:cat, completion_date=:date, status=:st WHERE id=:id"
          )->execute([':t'=>$title,':s'=>$slug,':d'=>$description,':img'=>$image,
                      ':c'=>$clientName,':cat'=>$category,':date'=>$completionDate,':st'=>$status,':id'=>$editId]);
          $success = 'Projet mis à jour.';
        }
        header('Location: index.php?saved=1');
        exit;
      } catch (PDOException $e) {
        $errors[] = $e->getCode() === '23000' ? 'Ce slug est déjà utilisé.' : $e->getMessage();
      }
    }
  }
}

// Load projects
try {
  $projects = $conn->query(
    "SELECT * FROM projects ORDER BY created_at DESC"
  )->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  $projects = [];
}

// Load project for editing
$editProj = null;
if (!empty($_GET['edit'])) {
  foreach ($projects as $p) {
    if ($p['id'] == (int)$_GET['edit']) { $editProj = $p; break; }
  }
}

include __DIR__ . '/../partials/header.php';
?>

<div class="admin-breadcrumb">
  <a href="../index.php"><i class="bi bi-house-fill"></i> Dashboard</a>
  <span class="sep">/</span>
  <span>Projets</span>
</div>

<div class="page-header">
  <div class="page-header-left">
    <h1>Gestion des Projets</h1>
    <p><?= count($projects) ?> projet<?= count($projects) > 1 ? 's' : '' ?> réalisé<?= count($projects) > 1 ? 's' : '' ?></p>
  </div>
  <?php if ($editProj): ?>
    <a href="index.php" class="btn-bbc-outline"><i class="bi bi-list-ul"></i> Voir la liste</a>
  <?php elseif (can('projects.create')): ?>
    <a href="?new=1" class="btn-bbc-primary"><i class="bi bi-plus-lg"></i> Nouveau projet</a>
  <?php endif; ?>
</div>

<?php if (isset($_GET['saved'])): ?>
  <div class="admin-alert admin-alert-success" data-auto-dismiss="3000">
    <i class="bi bi-check-circle-fill"></i> Projet enregistré avec succès.
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

<?php if (($editProj || isset($_GET['new'])) && can('projects.create')): ?>
<!-- ── Project Form (Edit / Create) ── -->
<div class="row g-4">
  <div class="col-lg-8">

    <div class="admin-card mb-4">
      <div class="admin-card-header">
        <h2 class="admin-card-title">
          <i class="bi bi-collection-play-fill"></i>
          <?= $editProj ? 'Modifier : ' . htmlspecialchars($editProj['title']) : 'Nouveau projet' ?>
        </h2>
      </div>
      <div class="admin-card-body">
        <form method="POST" enctype="multipart/form-data">
          <input type="hidden" name="action" value="<?= $editProj ? 'update' : 'create' ?>">
          <?php if ($editProj): ?>
            <input type="hidden" name="edit_id" value="<?= $editProj['id'] ?>">
            <input type="hidden" name="existing_image" value="<?= htmlspecialchars($editProj['image'] ?? '') ?>">
          <?php endif; ?>

          <div class="row g-3 mb-3">
            <div class="col-md-8">
              <div class="form-floating">
                <input type="text" class="form-control" id="projectTitle" name="title"
                       placeholder="Titre" required maxlength="255"
                       value="<?= htmlspecialchars($editProj['title'] ?? '') ?>">
                <label for="projectTitle">Titre du projet *</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <select class="form-select" name="status" id="projectStatus">
                  <option value="completed"   <?= ($editProj['status'] ?? 'completed') === 'completed'   ? 'selected' : '' ?>>✅ Terminé</option>
                  <option value="in_progress" <?= ($editProj['status'] ?? '') === 'in_progress' ? 'selected' : '' ?>>🚧 En cours</option>
                </select>
                <label for="projectStatus">Statut</label>
              </div>
            </div>
          </div>

          <div class="row g-3 mb-3">
             <div class="col-md-6">
                <div class="form-floating">
                    <input type="text" class="form-control" id="projectSlug" name="slug"
                        placeholder="slug" maxlength="255"
                        value="<?= htmlspecialchars($editProj['slug'] ?? '') ?>">
                    <label for="projectSlug">Slug (URL) *</label>
                </div>
            </div>
            <div class="col-md-6">
                 <div class="form-floating">
                    <input type="date" class="form-control" id="completionDate" name="completion_date"
                        value="<?= htmlspecialchars($editProj['completion_date'] ?? '') ?>">
                    <label for="completionDate">Date de fin</label>
                </div>
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="clientName" name="client_name"
                       placeholder="Nom du client" maxlength="255"
                       value="<?= htmlspecialchars($editProj['client_name'] ?? '') ?>">
                <label for="clientName"><i class="bi bi-person me-1"></i> Client</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="projectCategory" name="category"
                       placeholder="Catégorie" maxlength="100"
                       value="<?= htmlspecialchars($editProj['category'] ?? '') ?>">
                <label for="projectCategory"><i class="bi bi-tag me-1"></i> Catégorie (ex: Web, Design)</label>
              </div>
            </div>
          </div>

          <div class="form-floating mb-3">
            <textarea class="form-control" id="projectDesc" name="description"
                      placeholder="Description" style="height:120px;"><?= htmlspecialchars($editProj['description'] ?? '') ?></textarea>
            <label for="projectDesc">Description du projet</label>
          </div>

          <!-- Image Upload -->
          <div class="mb-4">
            <label class="form-label" style="font-size:13px; font-weight:600; color:var(--text-secondary);">Image du projet</label>
            <?php if (!empty($editProj['image'])): ?>
              <div class="image-preview-container" id="projectImagePreview" style="display:block; margin-bottom:10px;">
                <img src="../../<?= htmlspecialchars($editProj['image']) ?>" alt="Image projet">
                <button type="button" class="image-preview-remove"
                        onclick="removeImagePreview('projectImageInput','projectImagePreview','projectImageZone')">
                  <i class="bi bi-x"></i>
                </button>
              </div>
              <div class="image-upload-zone" id="projectImageZone" style="display:none;"
                   onclick="document.getElementById('projectImageInput').click()">
                <i class="bi bi-cloud-arrow-up"></i>
                <p><strong>Changer l'image</strong></p>
              </div>
            <?php else: ?>
              <div class="image-upload-zone" id="projectImageZone"
                   onclick="document.getElementById('projectImageInput').click()">
                <i class="bi bi-cloud-arrow-up"></i>
                <p><strong>Cliquez pour uploader</strong><br>
                   <span style="font-size:11px;">JPG, PNG, WebP</span>
                </p>
              </div>
              <div class="image-preview-container" id="projectImagePreview">
                <img src="" alt="Aperçu">
                <button type="button" class="image-preview-remove"
                        onclick="removeImagePreview('projectImageInput','projectImagePreview','projectImageZone')">
                  <i class="bi bi-x"></i>
                </button>
              </div>
            <?php endif; ?>
            <input type="file" id="projectImageInput" name="image" accept="image/*" style="display:none;">
          </div>

          <div class="d-flex gap-3">
            <button type="submit" class="btn-bbc-primary">
              <i class="bi bi-save-fill"></i> <?= $editProj ? 'Mettre à jour' : 'Créer le projet' ?>
            </button>
            <a href="index.php" class="btn-bbc-outline">Annuler</a>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

<?php else: ?>
<!-- ── Projects List ── -->
<div class="admin-card">
  <div class="admin-card-header">
    <h2 class="admin-card-title"><i class="bi bi-collection-play-fill"></i> Liste des projets</h2>
    <div class="filter-bar">
      <div class="search-input-wrapper">
        <i class="bi bi-search"></i>
        <input type="text" id="projectSearch" class="form-control" placeholder="Rechercher…">
      </div>
    </div>
  </div>
  <div class="admin-table-wrapper">
    <?php if (empty($projects)): ?>
      <div class="empty-state">
        <i class="bi bi-collection-play"></i>
        <h3>Aucun projet</h3>
        <p>Ajoutez votre premier projet réalisé.</p>
        <a href="?new=1" class="btn-bbc-primary"><i class="bi bi-plus-lg"></i> Nouveau projet</a>
      </div>
    <?php else: ?>
      <table class="admin-table" id="projectsTable">
        <thead>
          <tr>
            <th>Image</th>
            <th>Projet</th>
            <th>Client</th>
            <th>Catégorie</th>
            <th>Statut</th>
            <th style="width:100px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($projects as $proj): ?>
          <tr>
            <td style="width:60px;">
              <?php if ($proj['image']): ?>
                <img src="../../<?= htmlspecialchars($proj['image']) ?>" alt="" style="width:40px; height:40px; object-fit:cover; border-radius:6px;">
              <?php else: ?>
                <div style="width:40px; height:40px; background:#eee; border-radius:6px;"></div>
              <?php endif; ?>
            </td>
            <td>
              <div style="font-weight:600; font-size:13px;"><?= htmlspecialchars($proj['title']) ?></div>
              <div style="font-size:11px; color:var(--text-secondary);">
                <?= htmlspecialchars($proj['completion_date'] ?? '') ?>
              </div>
            </td>
            <td>
              <?= htmlspecialchars($proj['client_name'] ?? '—') ?>
            </td>
            <td>
              <span class="badge bg-light text-dark border"><?= htmlspecialchars($proj['category'] ?? 'Général') ?></span>
            </td>
            <td>
              <span class="badge-status <?= $proj['status'] === 'completed' ? 'badge-active' : 'badge-inactive' ?>">
                <?= $proj['status'] === 'completed' ? 'Terminé' : 'En cours' ?>
              </span>
            </td>
            <td>
              <div style="display:flex; gap:4px;">
                <?php if (can('projects.edit')): ?>
                <a href="?edit=<?= $proj['id'] ?>" class="btn-action edit" title="Modifier">
                  <i class="bi bi-pencil"></i>
                </a>
                <?php endif; ?>
                <?php if (can('projects.delete')): ?>
                <form method="POST" onsubmit="return confirmDelete('Supprimer ce projet ?')">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="delete_id" value="<?= $proj['id'] ?>">
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
