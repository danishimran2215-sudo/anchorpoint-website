<?php
/**
 * Anchor Point Travel & Tourism — Site, WhatsApp, Email (SMTP) & Analytics Settings
 * Aligned 100% with includes/functions.php & includes/mailer.php setting keys
 */
require_once __DIR__ . '/../includes/admin-auth-check.php';

$pageTitle = 'Site, WhatsApp & Email Settings';
$db        = getDB();
$saved     = false;

$settingKeys = [
    'show_prices',
    'show_service_prices',
    'site_name',
    'phone_display',
    'whatsapp_number',
    'company_email',
    'office_address',
    'office_hours',
    'trade_license_no',
    'tourism_license_no',
    'instagram_url',
    'facebook_url',
    'tiktok_url',
    'youtube_url',
    'ga_id',
    'gtm_id',
    'meta_pixel_id',
    'turnstile_site_key',
    'turnstile_secret_key',
    'smtp_host',
    'smtp_port',
    'smtp_user',
    'smtp_pass',
    'smtp_encryption',
    'smtp_from_email',
    'smtp_from_name',
];

if (isPost() && verifyCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
    foreach ($settingKeys as $key) {
        if ($key === 'smtp_pass' && ($_POST['smtp_pass'] ?? '') === '' && getSetting('smtp_pass') !== '') {
            continue; // keep existing password if left blank
        }
        setSetting($key, sanitize($_POST[$key] ?? ''));
    }
    logActivity('Updated Site & Email Settings', 'settings', 0);
    $saved = true;
}

require_once __DIR__ . '/../includes/admin-header.php';
?>

<?php if ($saved): ?>
  <div class="admin-alert" style="background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.35);color:#34D399;padding:0.75rem 1rem;border-radius:8px;margin-bottom:1.5rem;font-size:0.88rem;font-weight:600;">
    ✓ All settings saved and applied across the website immediately.
  </div>
<?php endif; ?>

<div class="admin-card" style="padding:2rem;max-width:920px;">
  <form action="" method="POST">
    <?= csrfField() ?>

    <h2 style="font-size:1.08rem;font-weight:700;color:var(--admin-gold);margin-bottom:0.85rem;">1. Website Price Visibility</h2>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:2rem;">
      <div style="padding:1rem 1.25rem;background:rgba(59,130,246,0.08);border:1px solid var(--admin-border);border-radius:8px;">
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.4rem;">Tour &amp; Attraction Prices (AED)</label>
        <?php $curShow = arePricesVisible() ? '1' : '0'; ?>
        <select name="show_prices" class="form-control">
          <option value="1" <?= $curShow === '1' ? 'selected' : '' ?>>Show Tour Prices ("From AED ___ per person")</option>
          <option value="0" <?= $curShow === '0' ? 'selected' : '' ?>>Hide Tour Prices (Enquiry / Quote Mode)</option>
        </select>
      </div>

      <div style="padding:1rem 1.25rem;background:rgba(245,158,11,0.08);border:1px solid var(--admin-border);border-radius:8px;">
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.4rem;">Business Setup Prices (AED)</label>
        <?php $curServShow = areServicePricesVisible() ? '1' : '0'; ?>
        <select name="show_service_prices" class="form-control">
          <option value="1" <?= $curServShow === '1' ? 'selected' : '' ?>>Show Business Setup Prices ("From AED ___")</option>
          <option value="0" <?= $curServShow === '0' ? 'selected' : '' ?>>Hide Business Setup Prices (Free Consultation Mode)</option>
        </select>
      </div>
    </div>

    <h2 style="font-size:1.08rem;font-weight:700;color:var(--admin-gold);margin-bottom:0.85rem;">2. Priority WhatsApp, Phone, Email &amp; UAE Licences</h2>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">Website / Brand Name</label>
        <input type="text" name="site_name" class="form-control" value="<?= e(getSetting('site_name', SITE_NAME)) ?>">
      </div>
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">Main Company Email (Receives all form enquiries)</label>
        <input type="email" name="company_email" class="form-control" value="<?= e(getCompanyEmail()) ?>">
      </div>
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">Displayed Priority Phone</label>
        <input type="text" name="phone_display" class="form-control" value="<?= e(getPrimaryPhone()) ?>">
      </div>
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">WhatsApp Number (International digits only, e.g. 971525376838)</label>
        <input type="text" name="whatsapp_number" class="form-control" value="<?= e(getWhatsAppNumber()) ?>">
      </div>
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">UAE Trade Licence Number</label>
        <input type="text" name="trade_license_no" class="form-control" value="<?= e(getSetting('trade_license_no', '')) ?>">
      </div>
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">DET Tourism Licence Number</label>
        <input type="text" name="tourism_license_no" class="form-control" value="<?= e(getSetting('tourism_license_no', '')) ?>">
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1.3fr 1fr;gap:1.25rem;margin-bottom:2rem;">
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">Dubai Office Address</label>
        <input type="text" name="office_address" class="form-control" value="<?= e(getSetting('office_address', OFFICE_ADDRESS)) ?>">
      </div>
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">Operating Hours</label>
        <input type="text" name="office_hours" class="form-control" value="<?= e(getSetting('office_hours', OFFICE_HOURS)) ?>">
      </div>
    </div>

    <h2 style="font-size:1.08rem;font-weight:700;color:var(--admin-gold);margin-bottom:0.85rem;padding-top:1.25rem;border-top:1px solid var(--admin-border);">3. Reliable SMTP Email Delivery (Google Workspace / Zoho / SendGrid / cPanel)</h2>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">SMTP Host</label>
        <input type="text" name="smtp_host" class="form-control" placeholder="smtp.zoho.com / smtp.gmail.com" value="<?= e(getSetting('smtp_host', SMTP_HOST)) ?>">
      </div>
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">SMTP Port</label>
        <input type="text" name="smtp_port" class="form-control" value="<?= e(getSetting('smtp_port', (string)SMTP_PORT)) ?>">
      </div>
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">Encryption</label>
        <?php $enc = getSetting('smtp_encryption', SMTP_ENCRYPTION); ?>
        <select name="smtp_encryption" class="form-control">
          <option value="tls" <?= $enc === 'tls' ? 'selected' : '' ?>>TLS (Port 587)</option>
          <option value="ssl" <?= $enc === 'ssl' ? 'selected' : '' ?>>SSL (Port 465)</option>
          <option value="none" <?= $enc === 'none' ? 'selected' : '' ?>>None</option>
        </select>
      </div>
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">SMTP Username</label>
        <input type="text" name="smtp_user" class="form-control" value="<?= e(getSetting('smtp_user', SMTP_USER)) ?>">
      </div>
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">SMTP Password (leave blank to keep)</label>
        <input type="password" name="smtp_pass" class="form-control" placeholder="••••••••">
      </div>
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">From Email</label>
        <input type="email" name="smtp_from_email" class="form-control" value="<?= e(getSetting('smtp_from_email', getCompanyEmail())) ?>">
      </div>
    </div>
    <div style="margin-bottom:2rem;">
      <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">From Sender Name</label>
      <input type="text" name="smtp_from_name" class="form-control" style="max-width:420px;" value="<?= e(getSetting('smtp_from_name', SITE_NAME)) ?>">
    </div>

    <h2 style="font-size:1.08rem;font-weight:700;color:var(--admin-gold);margin-bottom:0.85rem;padding-top:1.25rem;border-top:1px solid var(--admin-border);">4. Analytics, Conversion Pixels &amp; Cloudflare Turnstile</h2>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;">
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">GA4 Measurement ID</label>
        <input type="text" name="ga_id" class="form-control" placeholder="G-XXXXXXXXXX" value="<?= e(getSetting('ga_id', GA_ID)) ?>">
      </div>
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">Google Tag Manager ID</label>
        <input type="text" name="gtm_id" class="form-control" placeholder="GTM-XXXXXXX" value="<?= e(getSetting('gtm_id', GTM_ID)) ?>">
      </div>
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">Meta Pixel ID</label>
        <input type="text" name="meta_pixel_id" class="form-control" placeholder="Pixel ID" value="<?= e(getSetting('meta_pixel_id', META_PIXEL)) ?>">
      </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:2rem;">
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">Cloudflare Turnstile Site Key (Optional)</label>
        <input type="text" name="turnstile_site_key" class="form-control" value="<?= e(getSetting('turnstile_site_key', '')) ?>">
      </div>
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">Cloudflare Turnstile Secret Key (Optional)</label>
        <input type="password" name="turnstile_secret_key" class="form-control" value="<?= e(getSetting('turnstile_secret_key', '')) ?>">
      </div>
    </div>

    <h2 style="font-size:1.08rem;font-weight:700;color:var(--admin-gold);margin-bottom:0.85rem;padding-top:1.25rem;border-top:1px solid var(--admin-border);">5. Social Profiles</h2>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:2rem;">
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">Instagram URL</label>
        <input type="text" name="instagram_url" class="form-control" value="<?= e(getSetting('instagram_url', '')) ?>">
      </div>
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">Facebook URL</label>
        <input type="text" name="facebook_url" class="form-control" value="<?= e(getSetting('facebook_url', '')) ?>">
      </div>
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">TikTok URL</label>
        <input type="text" name="tiktok_url" class="form-control" value="<?= e(getSetting('tiktok_url', '')) ?>">
      </div>
      <div>
        <label style="display:block;font-size:0.85rem;font-weight:600;margin-bottom:0.35rem;">YouTube URL</label>
        <input type="text" name="youtube_url" class="form-control" value="<?= e(getSetting('youtube_url', '')) ?>">
      </div>
    </div>

    <button type="submit" class="btn-gold" style="padding:0.75rem 2rem;border:none;cursor:pointer;font-weight:700;">Save All Settings</button>
  </form>
</div>

</div><!-- .admin-content -->
</div><!-- .admin-main -->
<script src="/assets/js/admin.js"></script>
</body>
</html>
