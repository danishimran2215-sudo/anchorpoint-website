<?php
/**
 * Anchor Point — Admin Users Manager
 */
require_once __DIR__ . '/../includes/admin-auth-check.php';

$pageTitle = 'Users & Roles Manager';
$db = getDB();
$error = '';
$success = '';

// Handle add user or password reset
if (isPost()) {
    $action = $_POST['action'] ?? '';

    if ($action === 'create_user') {
        $name     = sanitize($_POST['name'] ?? '');
        $email    = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role     = in_array($_POST['role'] ?? '', ['superadmin', 'editor', 'sales']) ? $_POST['role'] : 'editor';

        if (empty($name) || empty($email) || empty($password)) {
            $error = 'All fields are required to create a user.';
        } else {
            try {
                $hash = hashPassword($password);
                $stmt = $db->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)');
                $stmt->execute([$name, $email, $hash, $role]);
                logActivity('Created User', 'users', $db->lastInsertId(), "Created user $email ($role)");
                $success = "User $name successfully created.";
            } catch (Exception $e) {
                $error = 'Failed to create user. Email may already be in use.';
            }
        }
    } elseif ($action === 'change_password') {
        $userId   = (int)($_POST['user_id'] ?? 0);
        $password = $_POST['password'] ?? '';

        if ($userId && !empty($password)) {
            $hash = hashPassword($password);
            $stmt = $db->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
            $stmt->execute([$hash, $userId]);
            logActivity('Changed Password', 'users', $userId, 'Password reset by admin');
            $success = 'Password successfully updated.';
        }
    }
}

$users = [];
try {
    $users = $db->query('SELECT id, name, email, role, is_active, last_login, created_at FROM users ORDER BY id ASC')->fetchAll();
} catch (Exception $e) {}

require_once __DIR__ . '/../includes/admin-header.php';
?>

<?php if ($success): ?>
  <div class="admin-alert" style="background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.3);color:#34D399;padding:0.75rem 1rem;border-radius:6px;margin-bottom:1.5rem;font-size:0.85rem;">
    <?= e($success) ?>
  </div>
<?php endif; ?>

<?php if ($error): ?>
  <div class="admin-alert" style="background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3);color:#F87171;padding:0.75rem 1rem;border-radius:6px;margin-bottom:1.5rem;font-size:0.85rem;">
    <?= e($error) ?>
  </div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1.5fr 1fr;gap:2rem;align-items:start;">
  <!-- Users List -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h2 class="admin-card-title">System Administrators</h2>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>User</th>
            <th>Role</th>
            <th>Last Active</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): ?>
          <tr>
            <td>
              <strong><?= e($u['name']) ?></strong><br>
              <span style="font-size:0.8rem;color:var(--admin-muted);"><?= e($u['email']) ?></span>
            </td>
            <td><span class="badge badge-contacted"><?= e(ucfirst($u['role'])) ?></span></td>
            <td><?= $u['last_login'] ? timeAgo($u['last_login']) : 'Never' ?></td>
            <td><span class="badge badge-converted">Active</span></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Add User Card -->
  <div class="admin-card" style="padding:2rem;">
    <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:1.5rem;">Add New User</h3>

    <form action="/admin/users" method="POST">
      <?= csrfField() ?>
      <input type="hidden" name="action" value="create_user">

      <div style="margin-bottom:1.25rem;">
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.4rem;">Full Name *</label>
        <input type="text" name="name" class="form-control" required placeholder="Ahmed Al Mansoori">
      </div>

      <div style="margin-bottom:1.25rem;">
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.4rem;">Email Address *</label>
        <input type="email" name="email" class="form-control" required placeholder="ahmed@anchorpointbiz.org">
      </div>

      <div style="margin-bottom:1.25rem;">
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.4rem;">Password *</label>
        <input type="password" name="password" class="form-control" required placeholder="••••••••••••">
      </div>

      <div style="margin-bottom:1.5rem;">
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.4rem;">Assigned Role</label>
        <select name="role" class="form-control">
          <option value="editor">Editor (Content &amp; Leads)</option>
          <option value="sales">Sales (Leads Manager Only)</option>
          <option value="superadmin">Super Admin (Full Access)</option>
        </select>
      </div>

      <button type="submit" class="btn-gold" style="width:100%;justify-content:center;">Create Account</button>
    </form>
  </div>
</div>

</div><!-- .admin-content -->
</div><!-- .admin-main -->

<script src="/assets/js/admin.js"></script>
</body>
</html>
