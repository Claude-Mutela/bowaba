<?php
/**
 * Admin Setup — admin/setup.php
 *
 * ONE-TIME SCRIPT to create the first admin user.
 * DELETE THIS FILE after use for security.
 */

// Basic protection: only accessible from localhost
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
if (!in_array($ip, ['127.0.0.1', '::1', '::ffff:127.0.0.1'])) {
    http_response_code(403);
    die('Accès refusé.');
}

require_once __DIR__ . '/../kon/conn.php';

$message = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role     = in_array($_POST['role'] ?? '', ['admin','editor','author']) ? $_POST['role'] : 'admin';

    if (!$name || !$email || !$password) {
        $error = 'Tous les champs sont obligatoires.';
    } elseif (strlen($password) < 8) {
        $error = 'Le mot de passe doit contenir au moins 8 caractères.';
    } else {
        try {
            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, password, role) VALUES (:n, :e, :p, :r)"
            );
            $stmt->execute([
                ':n' => $name,
                ':e' => $email,
                ':p' => password_hash($password, PASSWORD_DEFAULT),
                ':r' => $role,
            ]);
            $message = "✅ Utilisateur <strong>" . htmlspecialchars($name) . "</strong> créé avec succès !<br>
                        <a href='login.php'>→ Aller à la page de connexion</a><br><br>
                        <strong style='color:#dc2626;'>⚠️ Supprimez ce fichier (setup.php) maintenant !</strong>";
        } catch (PDOException $e) {
            $error = $e->getCode() === '23000'
                ? 'Cet email est déjà utilisé.'
                : 'Erreur : ' . $e->getMessage();
        }
    }
}

// Count existing users
$userCount = 0;
try {
    $userCount = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
} catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Setup Admin — BowaBanCongo</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <style>
    body { background: #f1f5f9; display: flex; align-items: center; justify-content: center; min-height: 100vh; font-family: 'Inter', sans-serif; }
    .setup-card { background: #fff; border-radius: 12px; padding: 40px; max-width: 460px; width: 100%; box-shadow: 0 8px 32px rgba(0,0,0,.1); }
    h1 { font-size: 20px; font-weight: 800; color: #0a1628; margin-bottom: 4px; }
    .warning { background: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px; padding: 12px 16px; font-size: 13px; color: #92400e; margin-bottom: 24px; }
  </style>
</head>
<body>
<div class="setup-card">
  <h1>🔧 Setup — Premier admin</h1>
  <p style="font-size:13px; color:#6b7a8d; margin-bottom:20px;">
    <?= $userCount ?> utilisateur<?= $userCount > 1 ? 's' : '' ?> existant<?= $userCount > 1 ? 's' : '' ?> dans la base.
  </p>

  <div class="warning">
    ⚠️ <strong>Sécurité :</strong> Supprimez ce fichier après utilisation.
  </div>

  <?php if ($message): ?>
    <div class="alert alert-success"><?= $message ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label fw-semibold">Nom complet</label>
      <input type="text" class="form-control" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label class="form-label fw-semibold">Email</label>
      <input type="email" class="form-control" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>
    <div class="mb-3">
      <label class="form-label fw-semibold">Mot de passe (min. 8 caractères)</label>
      <input type="password" class="form-control" name="password" required minlength="8">
    </div>
    <div class="mb-4">
      <label class="form-label fw-semibold">Rôle</label>
      <select class="form-select" name="role">
        <option value="admin">Administrateur</option>
        <option value="editor">Éditeur</option>
        <option value="author">Auteur</option>
      </select>
    </div>
    <button type="submit" class="btn btn-primary w-100 fw-bold">Créer l'utilisateur admin</button>
  </form>
</div>
</body>
</html>
