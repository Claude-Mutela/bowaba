<?php
/**
 * Users Management — admin/users/index.php
 */
require_once __DIR__ . '/../../kon/conn.php';

$pageTitle  = 'Utilisateurs';
$activePage = 'users';
$adminBase  = '../';

$errors  = [];
$success = '';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'delete' && !empty($_POST['delete_id'])) {
    try {
      $conn->prepare("DELETE FROM users WHERE id=:id")->execute([':id' => (int)$_POST['delete_id']]);
      $success = 'Utilisateur supprimé.';
    } catch (Exception $e) {
      $errors[] = 'Erreur : ' . $e->getMessage();
    }
  }

  if (in_array($action, ['create', 'update'])) {
    $name   = trim($_POST['name'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $role   = in_array($_POST['role'] ?? '', ['admin','editor','author']) ? $_POST['role'] : 'author';
    $bio    = trim($_POST['bio'] ?? '');
    $editId = (int)($_POST['edit_id'] ?? 0);
    $password = trim($_POST['password'] ?? '');

    if (!$name)  $errors[] = 'Le nom est obligatoire.';
    if (!$email) $errors[] = 'L\'email est obligatoire.';
    if ($action === 'create' && !$password) $errors[] = 'Le mot de passe est obligatoire.';

    if (empty($errors)) {
      try {
        if ($action === 'create') {
          $conn->prepare(
            "INSERT INTO users (name, email, password, role, bio) VALUES (:n,:e,:p,:r,:b)"
          )->execute([':n'=>$name, ':e'=>$email, ':p'=>password_hash($password, PASSWORD_DEFAULT), ':r'=>$role, ':b'=>$bio]);
          $success = 'Utilisateur créé avec succès.';
        } else {
          $params = [':n'=>$name, ':e'=>$email, ':r'=>$role, ':b'=>$bio, ':id'=>$editId];
          $pwdSQL = '';
          if ($password) {
            $pwdSQL = ', password=:p';
            $params[':p'] = password_hash($password, PASSWORD_DEFAULT);
          }
          $conn->prepare(
            "UPDATE users SET name=:n, email=:e, role=:r, bio=:b$pwdSQL WHERE id=:id"
          )->execute($params);
          $success = 'Utilisateur mis à jour.';
        }
        header('Location: index.php?saved=1');
        exit;
      } catch (PDOException $e) {
        $errors[] = $e->getCode() === '23000' ? 'Cet email est déjà utilisé.' : $e->getMessage();
      }
    }
  }
}

// Load users with article count
try {
  $users = $conn->query(
    "SELECT u.*, COUNT(a.id) AS article_count
     FROM users u
     LEFT JOIN articles a ON a.user_id = u.id
     GROUP BY u.id
     ORDER BY u.created_at DESC"
  )->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  $users = [];
}

// Load user for editing
$editUser = null;
if (!empty($_GET['edit'])) {
  foreach ($users as $u) {
    if ($u['id'] == (int)$_GET['edit']) { $editUser = $u; break; }
  }
}

include __DIR__ . '/../partials/header.php';
?>

<div class="admin-breadcrumb">
  <a href="../index.php"><i class="bi bi-house-fill"></i> Dashboard</a>
  <span class="sep">/</span>
  <span>Utilisateurs</span>
</div>

<div class="page-header">
  <div class="page-header-left">
    <h1>Utilisateurs</h1>
    <p><?= count($users) ?> utilisateur<?= count($users) > 1 ? 's' : '' ?> enregistré<?= count($users) > 1 ? 's' : '' ?></p>
  </div>
</div>

<?php if (isset($_GET['saved'])): ?>
  <div class="admin-alert admin-alert-success" data-auto-dismiss="3000">
    <i class="bi bi-check-circle-fill"></i> Utilisateur enregistré avec succès.
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

<div class="row g-4">

  <!-- Users Table -->
  <div class="col-lg-7">
    <div class="admin-card">
      <div class="admin-card-header">
        <h2 class="admin-card-title"><i class="bi bi-people-fill"></i> Liste des utilisateurs</h2>
        <div class="search-input-wrapper" style="max-width:220px;">
          <i class="bi bi-search"></i>
          <input type="text" id="userSearch" class="form-control" placeholder="Rechercher…">
        </div>
      </div>
      <div class="admin-table-wrapper">
        <?php if (empty($users)): ?>
          <div class="empty-state">
            <i class="bi bi-person-x"></i>
            <h3>Aucun utilisateur</h3>
            <p>Créez le premier compte administrateur.</p>
          </div>
        <?php else: ?>
          <table class="admin-table" id="usersTable">
            <thead>
              <tr>
                <th>Utilisateur</th>
                <th>Rôle</th>
                <th>Articles</th>
                <th>Inscription</th>
                <th style="width:90px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $u): ?>
              <tr>
                <td>
                  <div style="display:flex; align-items:center; gap:12px;">
                    <div class="sidebar-user-avatar" style="width:38px; height:38px; font-size:15px; flex-shrink:0;">
                      <?= strtoupper(mb_substr($u['name'], 0, 1)) ?>
                    </div>
                    <div>
                      <div style="font-weight:600; font-size:13px;"><?= htmlspecialchars($u['name']) ?></div>
                      <div style="font-size:11px; color:var(--text-secondary);"><?= htmlspecialchars($u['email']) ?></div>
                    </div>
                  </div>
                </td>
                <td>
                  <?php
                    $roleColors = [
                      'admin'  => ['bg'=>'#fee2e2','color'=>'#dc2626'],
                      'editor' => ['bg'=>'#e0f2fe','color'=>'#0369a1'],
                      'author' => ['bg'=>'#d1fae5','color'=>'#059669'],
                    ];
                    $rc = $roleColors[$u['role']] ?? $roleColors['author'];
                    $roleLabels = ['admin'=>'Admin','editor'=>'Éditeur','author'=>'Auteur'];
                  ?>
                  <span style="background:<?= $rc['bg'] ?>; color:<?= $rc['color'] ?>; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px;">
                    <?= $roleLabels[$u['role']] ?? $u['role'] ?>
                  </span>
                </td>
                <td>
                  <span class="order-badge"><?= $u['article_count'] ?></span>
                </td>
                <td style="font-size:12px; color:var(--text-secondary);">
                  <?= date('d/m/Y', strtotime($u['created_at'])) ?>
                </td>
                <td>
                  <div style="display:flex; gap:4px;">
                    <a href="?edit=<?= $u['id'] ?>" class="btn-action edit" title="Modifier">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <form method="POST" onsubmit="return confirmDelete('Supprimer cet utilisateur ?')">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="delete_id" value="<?= $u['id'] ?>">
                      <button type="submit" class="btn-action delete" title="Supprimer">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
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
  <div class="col-lg-5">
    <div class="admin-card">
      <div class="admin-card-header">
        <h2 class="admin-card-title">
          <i class="bi bi-person-<?= $editUser ? 'gear' : 'plus-fill' ?>"></i>
          <?= $editUser ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur' ?>
        </h2>
        <?php if ($editUser): ?>
          <a href="index.php" class="btn-bbc-outline" style="padding:5px 12px; font-size:12px;">
            <i class="bi bi-x-lg"></i> Annuler
          </a>
        <?php endif; ?>
      </div>
      <div class="admin-card-body">
        <form method="POST">
          <input type="hidden" name="action" value="<?= $editUser ? 'update' : 'create' ?>">
          <?php if ($editUser): ?>
            <input type="hidden" name="edit_id" value="<?= $editUser['id'] ?>">
          <?php endif; ?>

          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="userName" name="name"
                   placeholder="Nom complet" required maxlength="255"
                   value="<?= htmlspecialchars($editUser['name'] ?? '') ?>">
            <label for="userName"><i class="bi bi-person me-1"></i> Nom complet *</label>
          </div>

          <div class="form-floating mb-3">
            <input type="email" class="form-control" id="userEmail" name="email"
                   placeholder="Email" required maxlength="255"
                   value="<?= htmlspecialchars($editUser['email'] ?? '') ?>">
            <label for="userEmail"><i class="bi bi-envelope me-1"></i> Adresse email *</label>
          </div>

          <div class="form-floating mb-3">
            <input type="password" class="form-control" id="userPassword" name="password"
                   placeholder="Mot de passe" <?= $editUser ? '' : 'required' ?> minlength="8">
            <label for="userPassword">
              <i class="bi bi-lock me-1"></i>
              <?= $editUser ? 'Nouveau mot de passe (laisser vide = inchangé)' : 'Mot de passe *' ?>
            </label>
          </div>

          <div class="form-floating mb-3">
            <select class="form-select" name="role" id="userRole">
              <option value="author" <?= ($editUser['role'] ?? 'author') === 'author' ? 'selected' : '' ?>>✍️ Auteur</option>
              <option value="editor" <?= ($editUser['role'] ?? '') === 'editor' ? 'selected' : '' ?>>📝 Éditeur</option>
              <option value="admin"  <?= ($editUser['role'] ?? '') === 'admin'  ? 'selected' : '' ?>>🔑 Administrateur</option>
            </select>
            <label for="userRole">Rôle</label>
          </div>

          <div class="form-floating mb-4">
            <textarea class="form-control" id="userBio" name="bio"
                      placeholder="Biographie" style="height:100px;"><?= htmlspecialchars($editUser['bio'] ?? '') ?></textarea>
            <label for="userBio">Biographie courte</label>
          </div>

          <button type="submit" class="btn-bbc-primary w-100" style="justify-content:center;">
            <i class="bi bi-<?= $editUser ? 'save-fill' : 'person-plus-fill' ?>"></i>
            <?= $editUser ? 'Mettre à jour' : 'Créer l\'utilisateur' ?>
          </button>
        </form>
      </div>
    </div>

    <!-- Role Legend -->
    <div class="admin-card mt-4">
      <div class="admin-card-header">
        <h2 class="admin-card-title"><i class="bi bi-info-circle-fill"></i> Rôles</h2>
      </div>
      <div class="admin-card-body" style="padding:16px 20px;">
        <div style="display:flex; flex-direction:column; gap:12px; font-size:13px;">
          <div>
            <span style="background:#fee2e2; color:#dc2626; padding:2px 8px; border-radius:20px; font-size:11px; font-weight:700; margin-right:8px;">ADMIN</span>
            Accès complet à toutes les fonctionnalités
          </div>
          <div>
            <span style="background:#e0f2fe; color:#0369a1; padding:2px 8px; border-radius:20px; font-size:11px; font-weight:700; margin-right:8px;">ÉDITEUR</span>
            Peut publier et modifier tous les articles
          </div>
          <div>
            <span style="background:#d1fae5; color:#059669; padding:2px 8px; border-radius:20px; font-size:11px; font-weight:700; margin-right:8px;">AUTEUR</span>
            Peut créer et gérer ses propres articles
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
