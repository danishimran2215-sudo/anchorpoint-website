<?php
/**
 * Anchor Point — Admin Login
 */
require_once __DIR__ . '/../config.php';

if (isAdminLoggedIn()) {
    redirect(SITE_URL . '/admin/dashboard');
}

$error = '';
$clientIp = getClientIP();

if (isPost()) {
    // 1. Brute force check
    if (checkBruteForce($clientIp)) {
        $error = 'Too many failed login attempts. Please wait 15 minutes before trying again.';
    } else {
        // 2. CSRF verify
        $token = $_POST[CSRF_TOKEN_NAME] ?? '';
        if (!verifyCsrfToken($token)) {
            $error = 'Security session expired. Please refresh the page and try again.';
        } else {
            $email = sanitize($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $error = 'Please enter both email and password.';
            } else {
                $db = getDB();
                try {
                    $stmt = $db->prepare('SELECT id, name, email, password_hash, role, is_active FROM users WHERE email = ? LIMIT 1');
                    $stmt->execute([$email]);
                    $user = $stmt->fetch();

                    if ($user && $user['is_active'] && verifyPassword($password, $user['password_hash'])) {
                        clearFailedAttempts($clientIp);
                        loginAdmin($user['id'], $user['role'], $user['name']);
                        logActivity('Logged In', 'users', $user['id'], 'User logged into dashboard');
                        
                        $ref = $_GET['ref'] ?? '/admin/dashboard';
                        redirect($ref);
                    } else {
                        recordFailedAttempt($clientIp);
                        $error = 'Invalid email address or password.';
                    }
                } catch (Exception $e) {
                    $error = 'Authentication error. Please verify database connection.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Sign In — Anchor Point</title>
<link rel="icon" type="image/svg+xml" href="/assets/images/logo-icon.svg">
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body style="display:flex;align-items:center;justify-content:center;background:var(--admin-bg);">

  <div style="width:100%;max-width:420px;padding:2rem;">
    <div style="text-align:center;margin-bottom:2rem;">
      <img src="/assets/images/logo.svg" alt="Anchor Point" style="height:44px;margin:0 auto 1rem;display:block;">
      <h2 style="font-size:1.5rem;font-weight:700;color:var(--admin-text);">Portal Sign In</h2>
      <p style="font-size:0.85rem;color:var(--admin-muted);margin-top:4px;">Anchor Point Administrative Dashboard</p>
    </div>

    <div class="admin-card" style="padding:2rem;margin-bottom:1rem;">
      <?php if ($error): ?>
        <div style="background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3);color:#F87171;padding:0.75rem 1rem;border-radius:6px;font-size:0.85rem;margin-bottom:1.5rem;">
          <?= e($error) ?>
        </div>
      <?php endif; ?>

      <form action="/admin/login" method="POST">
        <?= csrfField() ?>

        <div style="margin-bottom:1.25rem;">
          <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.4rem;color:var(--admin-text);">Email Address</label>
          <input type="email" name="email" class="form-control" required placeholder="admin@anchorpointbiz.org" value="<?= e($_POST['email'] ?? 'admin@anchorpointbiz.org') ?>">
        </div>

        <div style="margin-bottom:1.5rem;">
          <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.4rem;color:var(--admin-text);">Password</label>
          <input type="password" name="password" class="form-control" required placeholder="••••••••••••" value="Admin@2024!">
        </div>

        <button type="submit" class="btn-gold" style="width:100%;justify-content:center;padding:0.85rem;">Sign In to Dashboard</button>
      </form>
    </div>

    <p style="text-align:center;font-size:0.8rem;color:var(--admin-muted);">
      Default credentials pre-filled for convenience.<br>
      Change your password in Settings after initial sign in.
    </p>
  </div>

</body>
</html>
