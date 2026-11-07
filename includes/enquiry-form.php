<?php
/**
 * 14-Field Customer Information & Booking Enquiry Form — PDF Section 10
 * Appears on /book and at the bottom of every /<category>/<activity-name> page.
 * Optional inputs: $selectedTour (array|null), $formHeading (string), $formSubheading (string)
 */
$allCategoriesForm = getTourCategories();
$allToursForm      = getTours();
$dialCodes         = getCountryDialCodes();
$turnstileSiteKey  = trim(getSetting('turnstile_site_key', ''));

$formError = $_SESSION['form_error'] ?? '';
$oldInput  = $_SESSION['form_old'] ?? [];
unset($_SESSION['form_error'], $_SESSION['form_old']);

$preTourId   = isset($oldInput['tour_id']) ? (int)$oldInput['tour_id'] : (int)($selectedTour['id'] ?? ($_GET['tour_id'] ?? 0));
$preCatName  = $oldInput['category_name'] ?? ($selectedTour['category_name'] ?? '');

if ($preTourId > 0 && $preCatName === '') {
    foreach ($allToursForm as $tf) {
        if ((int)$tf['id'] === $preTourId) {
            $preCatName = $tf['category_name'];
            if (empty($selectedTour)) $selectedTour = $tf;
            break;
        }
    }
}
if ($preCatName === '' && !empty($_GET['category'])) {
    $catFromGet = getTourCategoryBySlug(sanitize($_GET['category']));
    if ($catFromGet) $preCatName = $catFromGet['name'];
}
if ($preCatName === '' && !empty($allCategoriesForm)) {
    $preCatName = $allCategoriesForm[0]['name'];
}

$initialHasPickup = 1;
if (!empty($selectedTour)) {
    $initialHasPickup = (int)($selectedTour['has_pickup'] ?? 1);
} elseif ($preTourId > 0) {
    foreach ($allToursForm as $tf) {
        if ((int)$tf['id'] === $preTourId) {
            $initialHasPickup = (int)($tf['has_pickup'] ?? 1);
            break;
        }
    }
}

$defaultDate   = $oldInput['booking_date'] ?? date('Y-m-d', strtotime('+1 day'));
$todayDate     = date('Y-m-d');
$headingText   = $formHeading ?? 'Book or Send an Enquiry';
$subheadText   = $formSubheading ?? 'Fill in your details below. We save your request immediately and reply within 30 minutes on WhatsApp.';
$utms          = getUTM();
$currentSource = parse_url($_SERVER['REQUEST_URI'] ?? '/book', PHP_URL_PATH) ?: '/book';
?>
<div id="enquiryFormSection" class="enquiry-form-card">
  <div style="margin-bottom:1.5rem">
    <h2 style="font-size:clamp(1.4rem,2.4vw,1.8rem);color:var(--navy-900);margin-bottom:0.35rem"><?= e($headingText) ?></h2>
    <p style="color:var(--slate-600);margin:0;font-size:0.97rem"><?= e($subheadText) ?></p>
  </div>

  <?php if ($formError !== ''): ?>
    <div class="alert-box alert-error" role="alert"><?= e($formError) ?></div>
  <?php endif; ?>

  <form action="/api/book-tour.php" method="POST" data-enquiry-form novalidate>
    <?= csrfField() ?>
    <input type="hidden" name="source_page" value="<?= e($currentSource) ?>">
    <input type="hidden" name="utm_source" value="<?= e($utms['utm_source'] ?? '') ?>">
    <input type="hidden" name="utm_medium" value="<?= e($utms['utm_medium'] ?? '') ?>">
    <input type="hidden" name="utm_campaign" value="<?= e($utms['utm_campaign'] ?? '') ?>">
    <input type="hidden" name="utm_term" value="<?= e($utms['utm_term'] ?? '') ?>">
    <input type="hidden" name="utm_content" value="<?= e($utms['utm_content'] ?? '') ?>">

    <!-- Field 14: Spam Honeypot -->
    <div class="hp-trap" aria-hidden="true">
      <label for="hp_website_url">Leave this field empty</label>
      <input type="text" id="hp_website_url" name="website_url" tabindex="-1" autocomplete="off" value="">
    </div>

    <div class="form-grid">
      <!-- 1. Full name -->
      <div class="form-group">
        <label for="f_customer_name">Full name <span class="req-star">*</span></label>
        <input type="text"
               id="f_customer_name"
               name="customer_name"
               class="form-control"
               placeholder="e.g. Ahmed Al Mansoori"
               required
               value="<?= e($oldInput['customer_name'] ?? '') ?>">
      </div>

      <!-- 2. Email address -->
      <div class="form-group">
        <label for="f_customer_email">Email address <span class="req-star">*</span></label>
        <input type="email"
               id="f_customer_email"
               name="customer_email"
               class="form-control"
               placeholder="you@example.com"
               required
               value="<?= e($oldInput['customer_email'] ?? '') ?>">
      </div>

      <!-- 3. Phone / WhatsApp number with country code picker (Default UAE +971) -->
      <div class="form-group">
        <label for="f_customer_phone">Phone / WhatsApp number <span class="req-star">*</span></label>
        <div class="phone-input-group">
          <select name="country_code" class="form-control" aria-label="Country dial code">
            <?php
              $selCode = $oldInput['country_code'] ?? '+971';
              foreach ($dialCodes as $dc):
            ?>
              <option value="<?= e($dc['code']) ?>"<?= $selCode === $dc['code'] ? ' selected' : '' ?>><?= e($dc['label']) ?></option>
            <?php endforeach; ?>
          </select>
          <input type="tel"
                 id="f_customer_phone"
                 name="customer_phone"
                 class="form-control"
                 placeholder="50 123 4567"
                 required
                 value="<?= e($oldInput['customer_phone'] ?? '') ?>">
        </div>
      </div>

      <!-- 11. Nationality -->
      <div class="form-group">
        <label for="f_nationality">Nationality <span style="font-weight:400;color:var(--slate-500)">(Optional)</span></label>
        <input type="text"
               id="f_nationality"
               name="nationality"
               class="form-control"
               list="nationalityList"
               placeholder="Select or type nationality"
               value="<?= e($oldInput['nationality'] ?? '') ?>">
        <datalist id="nationalityList">
          <option value="United Arab Emirates">
          <option value="United Kingdom">
          <option value="United States">
          <option value="Saudi Arabia">
          <option value="India">
          <option value="Pakistan">
          <option value="Germany">
          <option value="France">
          <option value="Australia">
          <option value="Canada">
          <option value="Philippines">
          <option value="Qatar">
          <option value="Kuwait">
          <option value="Oman">
          <option value="Bahrain">
        </datalist>
      </div>

      <!-- 4. Tour category -->
      <div class="form-group">
        <label for="f_category_name">Tour category <span class="req-star">*</span></label>
        <select id="f_category_name" name="category_name" class="form-control" required>
          <?php foreach ($allCategoriesForm as $catRow): ?>
            <option value="<?= e($catRow['name']) ?>"<?= $preCatName === $catRow['name'] ? ' selected' : '' ?>>
              <?= e($catRow['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- 5. Specific activity (synced to category, pre-selected from activity page) -->
      <div class="form-group">
        <label for="f_tour_id">Specific activity <span class="req-star">*</span></label>
        <select id="f_tour_id" name="tour_id" class="form-control" required>
          <?php foreach ($allToursForm as $tRow): ?>
            <option value="<?= (int)$tRow['id'] ?>"
                    data-category-name="<?= e($tRow['category_name']) ?>"
                    data-has-pickup="<?= (int)($tRow['has_pickup'] ?? 1) ?>"
                    <?= ((int)$tRow['id'] === $preTourId) ? 'selected' : '' ?>>
              <?= e($tRow['title']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- 6. Preferred date (blocks past dates) -->
      <div class="form-group">
        <label for="f_booking_date">Preferred date <span class="req-star">*</span></label>
        <input type="date"
               id="f_booking_date"
               name="booking_date"
               class="form-control"
               min="<?= e($todayDate) ?>"
               required
               value="<?= e($defaultDate) ?>">
      </div>

      <!-- 7. Preferred time / pick-up time -->
      <div class="form-group">
        <label for="f_preferred_time">Preferred time / pick-up time <span style="font-weight:400;color:var(--slate-500)">(Optional)</span></label>
        <?php $selTime = $oldInput['preferred_time'] ?? 'Flexible'; ?>
        <select id="f_preferred_time" name="preferred_time" class="form-control">
          <?php foreach (['Flexible', 'Morning (08:00 AM – 12:00 PM)', 'Afternoon (12:00 PM – 04:00 PM)', 'Sunset (04:00 PM – 06:30 PM)', 'Evening (06:30 PM – 10:30 PM)'] as $tSlot): ?>
            <option value="<?= e($tSlot) ?>"<?= $selTime === $tSlot ? ' selected' : '' ?>><?= e($tSlot) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- 8. Number of adults (min 1) -->
      <div class="form-group">
        <label for="f_adults_count">Number of adults <span class="req-star">*</span></label>
        <input type="number"
               id="f_adults_count"
               name="adults_count"
               class="form-control"
               min="1"
               max="200"
               required
               value="<?= (int)($oldInput['adults_count'] ?? 2) ?>">
      </div>

      <!-- 9. Number of children + ages -->
      <div class="form-group">
        <label for="f_children_count">Number of children &amp; ages <span style="font-weight:400;color:var(--slate-500)">(Optional)</span></label>
        <div style="display:grid;grid-template-columns:105px minmax(0,1fr);gap:0.5rem">
          <input type="number"
                 id="f_children_count"
                 name="children_count"
                 class="form-control"
                 min="0"
                 max="100"
                 value="<?= (int)($oldInput['children_count'] ?? 0) ?>"
                 aria-label="Number of children">
          <div data-children-ages-wrap>
            <input type="text"
                   name="children_ages"
                   class="form-control"
                   placeholder="Ages e.g. 5, 9 yrs"
                   value="<?= e($oldInput['children_ages'] ?? '') ?>"
                   aria-label="Children ages">
          </div>
        </div>
      </div>

      <!-- 10. Pick-up location (shown ONLY when activity includes pick-up) -->
      <div class="form-group full-width" data-pickup-wrapper<?= $initialHasPickup === 0 ? ' style="display:none"' : '' ?>>
        <label for="f_pickup_location">Pick-up location (hotel name &amp; area) <span style="font-weight:400;color:var(--slate-500)">(Optional)</span></label>
        <input type="text"
               id="f_pickup_location"
               name="pickup_location"
               class="form-control"
               placeholder="e.g. Rove Downtown Hotel, Downtown Dubai"
               value="<?= e($oldInput['pickup_location'] ?? '') ?>">
      </div>

      <!-- 12. Special requests -->
      <div class="form-group full-width">
        <label for="f_special_requests">Special requests <span style="font-weight:400;color:var(--slate-500)">(Optional)</span></label>
        <textarea id="f_special_requests"
                  name="special_requests"
                  class="form-control"
                  placeholder="Dietary needs, wheelchair assistance, child/baby seat, birthday or anniversary setup..."><?= e($oldInput['special_requests'] ?? '') ?></textarea>
      </div>

      <!-- 13. UAE PDPL Consent Checkbox -->
      <div class="form-group full-width">
        <label class="consent-label">
          <input type="checkbox" name="consent_given" value="1" required<?= !empty($oldInput['consent_given']) ? ' checked' : '' ?>>
          <span>I agree to the <a href="/privacy" target="_blank">privacy policy</a> and to be contacted by WhatsApp, phone or email. <span class="req-star">*</span></span>
        </label>
      </div>

      <?php if ($turnstileSiteKey !== ''): ?>
      <div class="form-group full-width">
        <div class="cf-turnstile" data-sitekey="<?= e($turnstileSiteKey) ?>"></div>
      </div>
      <?php endif; ?>

      <div class="form-group full-width" style="margin-top:0.35rem">
        <button type="submit" class="btn btn-primary" style="width:100%;min-height:54px;font-size:1.05rem">
          Submit Booking Enquiry
        </button>
      </div>
    </div>
  </form>
</div>
