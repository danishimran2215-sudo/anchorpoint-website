<?php
/**
 * Anchor Point — Lead Detail & Note Management
 */
require_once __DIR__ . '/../includes/admin-auth-check.php';

$pageTitle = 'Enquiry Details';
$leadId = (int)($_GET['id'] ?? 0);
$db = getDB();

// Handle update notes & status
if (isPost()) {
    $status = sanitize($_POST['status'] ?? 'New');
    $notes = sanitize($_POST['notes'] ?? '');

    $up = $db->prepare('UPDATE leads SET status = ?, notes = ? WHERE id = ?');
    $up->execute([$status, $notes, $leadId]);
    logActivity('Updated Lead', 'leads', $leadId, 'Updated notes and status');
    redirect(SITE_URL . '/admin/leads/view?id=' . $leadId . '&saved=1');
}

$stmt = $db->prepare('
    SELECT l.*, s.title as service_title
    FROM leads l
    LEFT JOIN services s ON l.service_id = s.id
    WHERE l.id = ? LIMIT 1
');
$stmt->execute([$leadId]);
$lead = $stmt->fetch();

if (!$lead) {
    redirect(SITE_URL . '/admin/leads');
}

require_once __DIR__ . '/../includes/admin-header.php';
?>

<div style="margin-bottom:1.5rem;">
  <a href="/admin/leads" style="color:var(--admin-muted);text-decoration:none;font-size:0.85rem;">← Back to All Leads</a>
</div>

<?php if (isset($_GET['saved'])): ?>
  <div class="admin-alert" style="background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.3);color:#34D399;padding:0.75rem 1rem;border-radius:6px;margin-bottom:1.5rem;font-size:0.85rem;">
    Lead updated successfully.
  </div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1.5fr 1fr;gap:2rem;align-items:start;">
  <!-- Details Card -->
  <div class="admin-card" style="padding:2rem;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
      <h2 style="font-size:1.4rem;font-weight:700;"><?= e($lead['name']) ?></h2>
      <span class="badge badge-contacted"><?= e($lead['status']) ?></span>
    </div>

    <table style="width:100%;border-collapse:collapse;font-size:0.9rem;">
      <tr style="border-bottom:1px solid var(--admin-border);">
        <td style="padding:0.75rem 0;color:var(--admin-muted);width:30%;">Phone</td>
        <td style="padding:0.75rem 0;"><a href="tel:<?= e($lead['phone']) ?>" style="color:var(--admin-gold);text-decoration:none;font-weight:600;"><?= e($lead['phone']) ?></a></td>
      </tr>
      <tr style="border-bottom:1px solid var(--admin-border);">
        <td style="padding:0.75rem 0;color:var(--admin-muted);">Email</td>
        <td style="padding:0.75rem 0;"><a href="mailto:<?= e($lead['email']) ?>" style="color:inherit;text-decoration:none;"><?= e($lead['email'] ?: 'Not provided') ?></a></td>
      </tr>
      <tr style="border-bottom:1px solid var(--admin-border);">
        <td style="padding:0.75rem 0;color:var(--admin-muted);">Service Requested</td>
        <td style="padding:0.75rem 0;"><strong><?= e($lead['service_title'] ?: 'General Advisory') ?></strong></td>
      </tr>
      <tr style="border-bottom:1px solid var(--admin-border);">
        <td style="padding:0.75rem 0;color:var(--admin-muted);">Source URL</td>
        <td style="padding:0.75rem 0;"><code><?= e($lead['source_page'] ?: '/') ?></code></td>
      </tr>
      <tr style="border-bottom:1px solid var(--admin-border);">
        <td style="padding:0.75rem 0;color:var(--admin-muted);">IP Address</td>
        <td style="padding:0.75rem 0;"><?= e($lead['ip'] ?: 'Unknown') ?></td>
      </tr>
      <tr style="border-bottom:1px solid var(--admin-border);">
        <td style="padding:0.75rem 0;color:var(--admin-muted);">Submitted Date</td>
        <td style="padding:0.75rem 0;"><?= date('d M Y, h:i A', strtotime($lead['created_at'])) ?></td>
      </tr>
      <tr>
        <td style="padding:1rem 0;color:var(--admin-muted);vertical-align:top;">Message / Request</td>
        <td style="padding:1rem 0;line-height:1.7;"><?= nl2br(e($lead['message'] ?: 'No additional notes entered by client.')) ?></td>
      </tr>
    </table>

    <div style="display:flex;gap:1rem;margin-top:2rem;">
      <a href="<?= buildWhatsAppLink($lead['phone'], 'Hi ' . $lead['name'] . ', this is Anchor Point Business & Travel Services regarding your inquiry.') ?>" target="_blank" class="btn-gold" style="background:#25D366;color:#fff;">
        💬 Chat on WhatsApp
      </a>
      <?php if ($lead['email']): ?>
        <a href="mailto:<?= e($lead['email']) ?>" class="btn-logout">Send Email Reply</a>
      <?php endif; ?>
    </div>
  </div>

  <!-- Status & Internal Notes Card -->
  <div class="admin-card" style="padding:2rem;">
    <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:1.5rem;">Update Lead Status</h3>

    <form action="/admin/leads/view?id=<?= $lead['id'] ?>" method="POST">
      <?= csrfField() ?>

      <div style="margin-bottom:1.5rem;">
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.5rem;">Pipeline Status</label>
        <select name="status" class="form-control">
          <?php foreach (['New', 'Contacted', 'In Progress', 'Converted', 'Lost'] as $stOpt): ?>
            <option value="<?= $stOpt ?>" <?= $lead['status'] === $stOpt ? 'selected' : '' ?>><?= $stOpt ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div style="margin-bottom:1.5rem;">
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.5rem;">Internal Team Notes</label>
        <textarea name="notes" class="form-control" style="min-height:140px;" placeholder="Add notes regarding quotes given, documents collected, or client follow-up date..."><?= e($lead['notes'] ?? '') ?></textarea>
      </div>

      <button type="submit" class="btn-gold" style="width:100%;justify-content:center;">Save Changes</button>
    </form>
  </div>
</div>

</div><!-- .admin-content -->
</div><!-- .admin-main -->

<script src="/assets/js/admin.js"></script>
</body>
</html>
