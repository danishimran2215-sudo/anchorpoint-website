/**
 * Anchor Point Business Setup & Travel — Navigation & UI Interactions
 * Handles Business Setup Header Dropdown, Mobile Drawer, and Mobile Accordion
 */
document.addEventListener('DOMContentLoaded', function () {
  // ── Desktop Business Setup Dropdown ─────────────────────────
  var dropdown = document.getElementById('businessSetupDropdown');
  var trigger = document.getElementById('businessSetupTrigger');

  if (dropdown && trigger) {
    var megaMenu = dropdown.querySelector('.nav-mega-menu');
    var isHover = false;
    var isOpen = false;

    function openDropdown() {
      isOpen = true;
      trigger.setAttribute('aria-expanded', 'true');
      dropdown.classList.add('is-open');
    }

    function closeDropdown() {
      isOpen = false;
      trigger.setAttribute('aria-expanded', 'false');
      dropdown.classList.remove('is-open');
    }

    // Hover on desktop
    dropdown.addEventListener('mouseenter', function () {
      if (window.innerWidth >= 1024) openDropdown();
    });
    dropdown.addEventListener('mouseleave', function () {
      if (window.innerWidth >= 1024) closeDropdown();
    });

    // Click toggle
    trigger.addEventListener('click', function (e) {
      if (window.innerWidth < 1024) {
        // On small screen, default link or accordion handles it
        return;
      }
      e.preventDefault();
      if (isOpen) {
        closeDropdown();
      } else {
        openDropdown();
      }
    });

    // Close on outside click or Escape
    document.addEventListener('click', function (e) {
      if (!dropdown.contains(e.target)) {
        closeDropdown();
      }
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') closeDropdown();
    });
  }

  // ── Mobile Drawer Toggle ────────────────────────────────────
  var mobileToggle   = document.getElementById('mobileMenuToggle');
  var mobileDrawer   = document.getElementById('mobileNavDrawer');
  var mobileClose    = document.getElementById('mobileMenuClose');
  var mobileBackdrop = document.getElementById('mobileNavBackdrop');

  function openDrawer() {
    if (!mobileDrawer) return;
    mobileDrawer.classList.add('is-open');
    if (mobileBackdrop) mobileBackdrop.classList.add('is-open');
    document.body.classList.add('nav-open');
    document.body.style.overflow = 'hidden';
    if (mobileToggle) mobileToggle.setAttribute('aria-expanded', 'true');
  }

  function closeDrawer() {
    if (!mobileDrawer) return;
    mobileDrawer.classList.remove('is-open');
    if (mobileBackdrop) mobileBackdrop.classList.remove('is-open');
    document.body.classList.remove('nav-open');
    document.body.style.overflow = '';
    if (mobileToggle) mobileToggle.setAttribute('aria-expanded', 'false');
  }

  if (mobileToggle) {
    mobileToggle.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      if (mobileDrawer && mobileDrawer.classList.contains('is-open')) {
        closeDrawer();
      } else {
        openDrawer();
      }
    });
  }

  if (mobileClose) {
    mobileClose.addEventListener('click', function (e) {
      e.preventDefault();
      closeDrawer();
    });
  }

  if (mobileBackdrop) {
    mobileBackdrop.addEventListener('click', function (e) {
      e.preventDefault();
      closeDrawer();
    });
  }

  // Close drawer if clicking outside
  document.addEventListener('click', function (e) {
    if (mobileDrawer && mobileDrawer.classList.contains('is-open')) {
      if (!mobileDrawer.contains(e.target) && (!mobileToggle || !mobileToggle.contains(e.target))) {
        closeDrawer();
      }
    }
  });

  // Close drawer when any navigation link is clicked
  if (mobileDrawer) {
    mobileDrawer.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        closeDrawer();
      });
    });
  }

  // Close on Escape
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && mobileDrawer && mobileDrawer.classList.contains('is-open')) {
      closeDrawer();
    }
  });

  // ── Mobile Business Setup Accordion ─────────────────────────
  var accBtn   = document.getElementById('mobileBusinessAccordionBtn');
  var accPanel = document.getElementById('mobileBusinessAccordionPanel');

  if (accBtn && accPanel) {
    accBtn.addEventListener('click', function () {
      var isExpanded = accBtn.getAttribute('aria-expanded') === 'true';
      if (isExpanded) {
        accBtn.setAttribute('aria-expanded', 'false');
        accBtn.classList.remove('is-active');
        accPanel.hidden = true;
      } else {
        accBtn.setAttribute('aria-expanded', 'true');
        accBtn.classList.add('is-active');
        accPanel.hidden = false;
      }
    });
  }

  // ── Attraction Category Filter Tabs (on /tickets-attractions) ──
  var filterPills = document.querySelectorAll('.ticket-filter-pill');
  var ticketCards = document.querySelectorAll('.ticket-product-card');

  if (filterPills.length > 0 && ticketCards.length > 0) {
    filterPills.forEach(function (pill) {
      pill.addEventListener('click', function () {
        var group = this.getAttribute('data-group');
        filterPills.forEach(function (p) { p.classList.remove('is-active'); });
        this.classList.add('is-active');

        ticketCards.forEach(function (card) {
          if (group === 'all' || card.getAttribute('data-ticket-group') === group) {
            card.style.display = '';
          } else {
            card.style.display = 'none';
          }
        });
      });
    });
  }
});
