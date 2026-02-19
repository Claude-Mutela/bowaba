<?php
/**
 * Students Management — admin/students/index.php
 */
require_once __DIR__ . '/../../kon/conn.php';
require_once __DIR__ . '/../partials/auth.php'; // loads session + $adminUser + permissions

$pageTitle  = 'Apprenants';
$activePage = 'students';
$adminBase  = '../';

// RBAC: admin (write) + editor/author (read-only)
requirePermission('students.view');

$errors  = [];
$success = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'delete' && !empty($_POST['delete_id'])) {
    requirePermission('students.delete');
    try {
      $conn->prepare("DELETE FROM students WHERE id=:id")->execute([':id' => (int)$_POST['delete_id']]);
      $success = 'Apprenant supprimé.';
    } catch (Exception $e) {
      $errors[] = 'Erreur : ' . $e->getMessage();
    }
  }

  if (in_array($action, ['create', 'update'])) {
    requirePermission('students.create');
    $fname     = trim($_POST['first_name'] ?? '');
    $lname     = trim($_POST['last_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $programId = !empty($_POST['program_id']) ? (int)$_POST['program_id'] : null;
    $date      = !empty($_POST['completion_date']) ? $_POST['completion_date'] : null;
    $certId    = trim($_POST['certificate_id'] ?? '');
    $editId    = (int)($_POST['edit_id'] ?? 0);

    if (!$fname || !$lname) $errors[] = 'Nom et Prénom sont obligatoires.';
    if (!$programId)        $errors[] = 'Le programme de formation est obligatoire.';

    if (empty($errors)) {
      try {
        if ($action === 'create') {
          $conn->prepare(
            "INSERT INTO students (first_name, last_name, email, program_id, completion_date, certificate_id)
             VALUES (:f,:l,:e,:p,:d,:c)"
          )->execute([':f'=>$fname,':l'=>$lname,':e'=>$email,':p'=>$programId,':d'=>$date,':c'=>$certId]);
          $success = 'Apprenant ajouté avec succès.';
        } else {
          $conn->prepare(
            "UPDATE students SET first_name=:f, last_name=:l, email=:e, program_id=:p, 
             completion_date=:d, certificate_id=:c WHERE id=:id"
          )->execute([':f'=>$fname,':l'=>$lname,':e'=>$email,':p'=>$programId,':d'=>$date,':c'=>$certId,':id'=>$editId]);
          $success = 'Apprenant mis à jour.';
        }
        header('Location: index.php?saved=1');
        exit;
      } catch (PDOException $e) {
        $errors[] = 'Erreur SGBD : ' . $e->getMessage();
      }
    }
  }
}

// Load students with program info
try {
  $students = $conn->query(
    "SELECT s.*, p.title as program_title 
     FROM students s
     LEFT JOIN training_programs p ON s.program_id = p.id
     ORDER BY s.created_at DESC"
  )->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  $students = [];
}

// Load programs for select
try {
    $programs = $conn->query("SELECT id, title FROM training_programs ORDER BY title ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $programs = [];
}

// Load student for editing
$editStudent = null;
if (!empty($_GET['edit'])) {
  foreach ($students as $s) {
    if ($s['id'] == (int)$_GET['edit']) { $editStudent = $s; break; }
  }
}

include __DIR__ . '/../partials/header.php';
?>

<div class="admin-breadcrumb">
  <a href="../index.php"><i class="bi bi-house-fill"></i> Dashboard</a>
  <span class="sep">/</span>
  <span>Apprenants</span>
</div>

<div class="page-header">
  <div class="page-header-left">
    <h1>Gestion des Apprenants</h1>
    <p><?= count($students) ?> personne<?= count($students) > 1 ? 's' : '' ?> formée<?= count($students) > 1 ? 's' : '' ?></p>
  </div>
  <?php if ($editStudent): ?>
    <a href="index.php" class="btn-bbc-outline"><i class="bi bi-list-ul"></i> Voir la liste</a>
  <?php elseif (can('students.create')): ?>
    <a href="?new=1" class="btn-bbc-primary"><i class="bi bi-plus-lg"></i> Nouvel apprenant</a>
  <?php endif; ?>
</div>

<?php if (isset($_GET['saved'])): ?>
  <div class="admin-alert admin-alert-success" data-auto-dismiss="3000">
    <i class="bi bi-check-circle-fill"></i> Apprenant enregistré.
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

<?php if (($editStudent || isset($_GET['new'])) && can('students.create')): ?>
<!-- ── Student Form (Edit / Create) ── -->
<div class="row g-4">
  <div class="col-lg-8">

    <div class="admin-card mb-4">
      <div class="admin-card-header">
        <h2 class="admin-card-title">
          <i class="bi bi-mortarboard-fill"></i>
          <?= $editStudent ? 'Modifier : ' . htmlspecialchars($editStudent['first_name'] . ' ' . $editStudent['last_name']) : 'Nouvel apprenant' ?>
        </h2>
      </div>
      <div class="admin-card-body">
        <form method="POST">
          <input type="hidden" name="action" value="<?= $editStudent ? 'update' : 'create' ?>">
          <?php if ($editStudent): ?>
            <input type="hidden" name="edit_id" value="<?= $editStudent['id'] ?>">
          <?php endif; ?>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="firstName" name="first_name"
                       placeholder="Prénom" required maxlength="255"
                       value="<?= htmlspecialchars($editStudent['first_name'] ?? '') ?>">
                <label for="firstName">Prénom *</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="lastName" name="last_name"
                       placeholder="Nom" required maxlength="255"
                       value="<?= htmlspecialchars($editStudent['last_name'] ?? '') ?>">
                <label for="lastName">Nom *</label>
              </div>
            </div>
          </div>

          <div class="form-floating mb-3">
            <input type="email" class="form-control" id="email" name="email"
                   placeholder="Email" maxlength="255"
                   value="<?= htmlspecialchars($editStudent['email'] ?? '') ?>">
            <label for="email"><i class="bi bi-envelope me-1"></i> Email (optionnel)</label>
          </div>

          <div class="form-floating mb-3">
             <select class="form-select" id="program" name="program_id" required>
                 <option value="">— Sélectionner un programme —</option>
                 <?php foreach($programs as $prog): ?>
                    <option value="<?= $prog['id'] ?>" <?= ($editStudent['program_id'] ?? '') == $prog['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($prog['title']) ?>
                    </option>
                 <?php endforeach; ?>
             </select>
             <label for="program"><i class="bi bi-journal-check me-1"></i> Programme de formation *</label>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
               <div class="form-floating">
                  <input type="date" class="form-control" id="compDate" name="completion_date"
                         value="<?= htmlspecialchars($editStudent['completion_date'] ?? '') ?>">
                  <label for="compDate">Date de fin</label>
              </div>
            </div>
            <div class="col-md-6">
               <div class="form-floating">
                  <input type="text" class="form-control" id="certId" name="certificate_id"
                         placeholder="ID Certif" maxlength="100"
                         value="<?= htmlspecialchars($editStudent['certificate_id'] ?? '') ?>">
                  <label for="certId"><i class="bi bi-award me-1"></i> ID Certificat</label>
              </div>
            </div>
          </div>

          <div class="d-flex gap-3 mt-4">
            <button type="submit" class="btn-bbc-primary">
              <i class="bi bi-save-fill"></i> <?= $editStudent ? 'Mettre à jour' : 'Ajouter l\'apprenant' ?>
            </button>
            <a href="index.php" class="btn-bbc-outline">Annuler</a>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>

<?php else: ?>
<!-- ── Students List ── -->
<div class="admin-card">
  <div class="admin-card-header">
    <h2 class="admin-card-title"><i class="bi bi-mortarboard-fill"></i> Liste des apprenants</h2>
    <div class="filter-bar">
      <div class="search-input-wrapper">
        <i class="bi bi-search"></i>
        <input type="text" id="studentSearch" class="form-control" placeholder="Rechercher…">
      </div>
    </div>
  </div>
  <div class="admin-table-wrapper">
    <?php if (empty($students)): ?>
      <div class="empty-state">
        <i class="bi bi-mortarboard"></i>
        <h3>Aucun apprenant</h3>
        <p>Ajoutez les personnes que vous avez formées.</p>
        <a href="?new=1" class="btn-bbc-primary"><i class="bi bi-plus-lg"></i> Nouvel apprenant</a>
      </div>
    <?php else: ?>
      <table class="admin-table" id="studentsTable">
        <thead>
          <tr>
            <th>Nom complet</th>
            <th>Programme</th>
            <th>Date fin</th>
            <th>Certificat</th>
            <th style="width:100px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($students as $s): ?>
          <tr>
            <td>
              <div style="font-weight:600; font-size:13px;">
                <?= htmlspecialchars($s['first_name'] . ' ' . $s['last_name']) ?>
              </div>
              <?php if ($s['email']): ?>
                <div style="font-size:11px; color:var(--text-secondary);">
                  <a href="mailto:<?= htmlspecialchars($s['email']) ?>" style="color:inherit;"><?= htmlspecialchars($s['email']) ?></a>
                </div>
              <?php endif; ?>
            </td>
            <td>
              <?php if($s['program_title']): ?>
                <span class="badge bg-light text-dark border"><?= htmlspecialchars($s['program_title']) ?></span>
              <?php else: ?>
                <span class="badge bg-light text-muted border">Non assigné</span>
              <?php endif; ?>
            </td>
            <td style="font-family:monospace; font-size:12px;">
              <?= htmlspecialchars($s['completion_date'] ?? '—') ?>
            </td>
            <td>
              <?php if ($s['certificate_id']): ?>
                <code style="font-size:11px;"><?= htmlspecialchars($s['certificate_id']) ?></code>
              <?php else: ?>
                <span style="color:#ccc;">—</span>
              <?php endif; ?>
            </td>
            <td>
              <div style="display:flex; gap:4px;">
                <?php if (can('students.edit')): ?>
                <a href="?edit=<?= $s['id'] ?>" class="btn-action edit" title="Modifier">
                  <i class="bi bi-pencil"></i>
                </a>
                <?php endif; ?>
                <?php if (can('students.delete')): ?>
                <form method="POST" onsubmit="return confirmDelete('Supprimer cet aprenant ?')">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="delete_id" value="<?= $s['id'] ?>">
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
