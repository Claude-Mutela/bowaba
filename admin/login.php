<?php
/**
 * Admin Login — admin/login.php
 */

// Simple session-based auth guard
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
  header('Location: index.php');
  exit;
}

require_once __DIR__ . '/../kon/conn.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email    = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($email && $password) {
    try {
      $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
      $stmt->execute([':email' => $email]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user_id']   = $user['id'];
        $_SESSION['admin_user_name'] = $user['name'];
        $_SESSION['admin_user_role'] = $user['role'];
        header('Location: index.php');
        exit;
      } else {
        $error = 'Email ou mot de passe incorrect.';
      }
    } catch (Exception $e) {
      $error = 'Erreur de connexion à la base de données.';
    }
  } else {
    $error = 'Veuillez remplir tous les champs.';
  }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion — BowaBanCongo Admin</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Raleway:wght@700;800&display=swap" rel="stylesheet">
  <style>
    :root {
      --bbc-blue: #008ff2;
      --bbc-blue-dark: #006bbf;
      --bbc-gold: #ffd02a;
    }

    * { box-sizing: border-box; }

    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #0a1628 0%, #162240 50%, #0a1628 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      position: relative;
      overflow: hidden;
    }

    /* Background decoration */
    body::before {
      content: '';
      position: absolute;
      top: -200px;
      right: -200px;
      width: 600px;
      height: 600px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(0,143,242,.15) 0%, transparent 70%);
      pointer-events: none;
    }

    body::after {
      content: '';
      position: absolute;
      bottom: -200px;
      left: -200px;
      width: 500px;
      height: 500px;
      border-radius: 50%;
      background: radial-gradient(circle, rgba(255,208,42,.08) 0%, transparent 70%);
      pointer-events: none;
    }

    .login-card {
      background: rgba(255,255,255,.97);
      border-radius: 16px;
      padding: 48px 44px;
      width: 100%;
      max-width: 420px;
      box-shadow: 0 24px 80px rgba(0,0,0,.4);
      position: relative;
      z-index: 1;
    }

    .login-logo {
      text-align: center;
      margin-bottom: 32px;
    }

    .login-logo img {
      height: 48px;
      width: auto;
      margin-bottom: 12px;
    }

    .login-logo h1 {
      font-family: 'Raleway', sans-serif;
      font-size: 20px;
      font-weight: 800;
      color: #0a1628;
      margin: 0 0 4px;
    }

    .login-logo p {
      font-size: 12px;
      color: #6b7a8d;
      margin: 0;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      font-weight: 600;
    }

    .login-divider {
      height: 3px;
      background: linear-gradient(90deg, var(--bbc-blue), var(--bbc-gold));
      border-radius: 2px;
      margin: 0 0 28px;
    }

    .form-floating > label {
      font-size: 13.5px;
      color: #6b7a8d;
    }

    .form-control:focus {
      border-color: var(--bbc-blue);
      box-shadow: 0 0 0 3px rgba(0,143,242,.12);
    }

    .btn-login {
      width: 100%;
      padding: 13px;
      background: linear-gradient(135deg, var(--bbc-blue), var(--bbc-blue-dark));
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 700;
      cursor: pointer;
      transition: all .25s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      box-shadow: 0 4px 16px rgba(0,143,242,.35);
      letter-spacing: .3px;
    }

    .btn-login:hover {
      background: linear-gradient(135deg, var(--bbc-blue-dark), #004f99);
      box-shadow: 0 6px 24px rgba(0,143,242,.45);
      transform: translateY(-1px);
    }

    .error-alert {
      background: #fee2e2;
      color: #991b1b;
      border-radius: 8px;
      padding: 12px 16px;
      font-size: 13.5px;
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 20px;
    }

    .login-footer {
      text-align: center;
      margin-top: 24px;
      font-size: 12px;
      color: #6b7a8d;
    }

    .login-footer a {
      color: var(--bbc-blue);
      text-decoration: none;
    }

    .login-footer a:hover { text-decoration: underline; }

    .password-toggle {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #6b7a8d;
      cursor: pointer;
      font-size: 16px;
      z-index: 10;
      padding: 4px;
    }

    .password-wrapper {
      position: relative;
    }

    .password-wrapper .form-control {
      padding-right: 44px;
    }
  </style>
</head>
<body>

<div class="login-card">

  <!-- Logo -->
  <div class="login-logo">
    <img src="../assets/img/logo/logo-bw.png" alt="BowaBanCongo"
         onerror="this.style.display='none'">
    <h1>BowaBanCongo</h1>
    <p>Interface d'administration</p>
  </div>

  <div class="login-divider"></div>

  <!-- Error -->
  <?php if ($error): ?>
    <div class="error-alert">
      <i class="bi bi-exclamation-triangle-fill"></i>
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <!-- Form -->
  <form method="POST" autocomplete="on">

    <div class="form-floating mb-3">
      <input type="email" class="form-control" id="loginEmail" name="email"
             placeholder="Email" required autocomplete="email"
             value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      <label for="loginEmail"><i class="bi bi-envelope me-1"></i> Adresse email</label>
    </div>

    <div class="mb-4 password-wrapper">
      <div class="form-floating">
        <input type="password" class="form-control" id="loginPassword" name="password"
               placeholder="Mot de passe" required autocomplete="current-password">
        <label for="loginPassword"><i class="bi bi-lock me-1"></i> Mot de passe</label>
      </div>
      <button type="button" class="password-toggle" id="togglePassword" title="Afficher/masquer">
        <i class="bi bi-eye" id="toggleIcon"></i>
      </button>
    </div>

    <button type="submit" class="btn-login">
      <i class="bi bi-box-arrow-in-right"></i>
      Se connecter
    </button>

  </form>

  <div class="login-footer">
    <a href="../index.php"><i class="bi bi-arrow-left"></i> Retour au site</a>
  </div>

</div>

<script>
  // Password toggle
  const toggle   = document.getElementById('togglePassword');
  const pwdInput = document.getElementById('loginPassword');
  const icon     = document.getElementById('toggleIcon');

  toggle.addEventListener('click', () => {
    const isText = pwdInput.type === 'text';
    pwdInput.type = isText ? 'password' : 'text';
    icon.className = isText ? 'bi bi-eye' : 'bi bi-eye-slash';
  });
</script>

</body>
</html>
