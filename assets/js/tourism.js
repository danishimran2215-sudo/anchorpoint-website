/**
 * Anchor Point Travel & Tourism — Frontend Interactivity & Analytics (tourism.js)
 */
(function () {
  'use strict';

  /* ── 1. WhatsApp Click Analytics (GA4 + Meta Pixel, PDF Section 9) ── */
  window.trackWhatsAppClick = function (locationName, activityName) {
    var payload = {
      button_location: locationName || 'unknown',
      activity_name: activityName || 'general',
      page_path: window.location.pathname
    };
    if (typeof window.gtag === 'function') {
      window.gtag('event', 'whatsapp_click', payload);
    }
    if (typeof window.fbq === 'function') {
      window.fbq('trackCustom', 'WhatsAppClick', payload);
    }
  };

  document.addEventListener('click', function (e) {
    var waEl = e.target.closest('[data-wa-location]');
    if (waEl) {
      window.trackWhatsAppClick(
        waEl.getAttribute('data-wa-location'),
        waEl.getAttribute('data-wa-activity') || ''
      );
    }
  });

  /* ── 2. Mobile Navigation Drawer handled centrally in nav-interactions.js ── */

  /* ── 3. Homepage Scroll-Spy & Smart Category Links (PDF Section 2) ── */
  var isHome = window.location.pathname === '/' || window.location.pathname === '/index.php';
  var navLinks = document.querySelectorAll('[data-cat-nav]');
  if (isHome && navLinks.length > 0) {
    var activeSlug = '';
    var sections = document.querySelectorAll('section.category-section[id]');

    var setActiveNav = function (slug) {
      activeSlug = slug;
      navLinks.forEach(function (lnk) {
        if (lnk.getAttribute('data-cat-nav') === slug) {
          lnk.classList.add('is-active');
        } else {
          lnk.classList.remove('is-active');
        }
      });
    };

    if ('IntersectionObserver' in window && sections.length > 0) {
      var observer = new IntersectionObserver(
        function (entries) {
          entries.forEach(function (entry) {
            if (entry.isIntersecting) {
              setActiveNav(entry.target.id);
            }
          });
        },
        { rootMargin: '-22% 0px -60% 0px', threshold: 0.05 }
      );
      sections.forEach(function (sec) {
        observer.observe(sec);
      });
    }

    // Smooth scroll on 1st click; open full /<category> page if already active on 2nd click
    navLinks.forEach(function (lnk) {
      lnk.addEventListener('click', function (ev) {
        var slug = lnk.getAttribute('data-cat-nav');
        var target = document.getElementById(slug);
        if (!target) return;
        if (activeSlug === slug && lnk.getAttribute('data-clicked-once') === '1') {
          window.location.href = '/' + slug;
          ev.preventDefault();
          return;
        }
        ev.preventDefault();
        navLinks.forEach(function (other) {
          other.removeAttribute('data-clicked-once');
        });
        lnk.setAttribute('data-clicked-once', '1');
        setActiveNav(slug);
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        if (history.replaceState) {
          history.replaceState(null, '', '#' + slug);
        }
      });
    });
  }

  /* ── 4. Activity Detail Sidebar Dynamic WhatsApp Link ── */
  var detailWaBtn = document.getElementById('detailBookWhatsAppBtn');
  var quickDate = document.getElementById('quickBookingDate');
  var quickGuests = document.getElementById('quickBookingGuests');
  if (detailWaBtn && (quickDate || quickGuests)) {
    var updateDetailWaHref = function () {
      var num = detailWaBtn.getAttribute('data-wa-num') || '971525376838';
      var act = detailWaBtn.getAttribute('data-wa-activity') || 'UAE Tour';
      var dt = quickDate && quickDate.value ? quickDate.value : 'tomorrow';
      var gs = quickGuests && quickGuests.value ? quickGuests.value : '2';
      var msg = 'Hi, I want to book ' + act + ' on ' + dt + ' for ' + gs + ' guests.';
      detailWaBtn.setAttribute('href', 'https://wa.me/' + num + '?text=' + encodeURIComponent(msg));
    };
    if (quickDate) quickDate.addEventListener('change', updateDetailWaHref);
    if (quickGuests) quickGuests.addEventListener('change', updateDetailWaHref);
    updateDetailWaHref();
  }

  /* ── 5. Photo Gallery Lightbox ── */
  var galleryTriggers = document.querySelectorAll('[data-lightbox-src]');
  var lightbox = document.getElementById('galleryLightbox');
  if (galleryTriggers.length > 0 && lightbox) {
    var lbImg = document.getElementById('lightboxImage');
    var lbClose = document.getElementById('lightboxClose');
    var lbPrev = document.getElementById('lightboxPrev');
    var lbNext = document.getElementById('lightboxNext');
    var sources = Array.from(galleryTriggers).map(function (btn) {
      return btn.getAttribute('data-lightbox-src');
    });
    // Deduplicate ordered list
    var uniqueSources = [];
    sources.forEach(function (s) {
      if (s && uniqueSources.indexOf(s) === -1) uniqueSources.push(s);
    });
    var currentIdx = 0;

    var openAt = function (src) {
      var idx = uniqueSources.indexOf(src);
      currentIdx = idx >= 0 ? idx : 0;
      lbImg.src = uniqueSources[currentIdx];
      lightbox.classList.add('is-open');
    };

    galleryTriggers.forEach(function (btn) {
      btn.addEventListener('click', function () {
        openAt(btn.getAttribute('data-lightbox-src'));
      });
    });

    if (lbClose) {
      lbClose.addEventListener('click', function () {
        lightbox.classList.remove('is-open');
      });
    }
    if (lbPrev) {
      lbPrev.addEventListener('click', function () {
        currentIdx = (currentIdx - 1 + uniqueSources.length) % uniqueSources.length;
        lbImg.src = uniqueSources[currentIdx];
      });
    }
    if (lbNext) {
      lbNext.addEventListener('click', function () {
        currentIdx = (currentIdx + 1) % uniqueSources.length;
        lbImg.src = uniqueSources[currentIdx];
      });
    }
    lightbox.addEventListener('click', function (ev) {
      if (ev.target === lightbox) lightbox.classList.remove('is-open');
    });
    document.addEventListener('keydown', function (ev) {
      if (!lightbox.classList.contains('is-open')) return;
      if (ev.key === 'Escape') lightbox.classList.remove('is-open');
      if (ev.key === 'ArrowLeft' && lbPrev) lbPrev.click();
      if (ev.key === 'ArrowRight' && lbNext) lbNext.click();
    });
  }

  /* ── 6. 14-Field Customer Information Form Sync (PDF Section 10) ── */
  document.querySelectorAll('[data-enquiry-form]').forEach(function (form) {
    var catSelect = form.querySelector('[name="category_name"]');
    var actSelect = form.querySelector('[name="tour_id"]');
    var pickupWrap = form.querySelector('[data-pickup-wrapper]');
    var pickupInput = form.querySelector('[name="pickup_location"]');
    var childrenCount = form.querySelector('[name="children_count"]');
    var childrenAgesWrap = form.querySelector('[data-children-ages-wrap]');

    var allActOptions = actSelect
      ? Array.from(actSelect.querySelectorAll('option[data-category-name]'))
      : [];

    var syncPickupVisibility = function () {
      if (!actSelect || !pickupWrap) return;
      var selectedOpt = actSelect.options[actSelect.selectedIndex];
      var hasPickup = selectedOpt ? selectedOpt.getAttribute('data-has-pickup') : '1';
      if (hasPickup === '0') {
        pickupWrap.style.display = 'none';
        if (pickupInput) pickupInput.removeAttribute('required');
      } else {
        pickupWrap.style.display = '';
      }
    };

    var filterActivitiesByCategory = function (keepSelected) {
      if (!catSelect || !actSelect) return;
      var chosenCat = catSelect.value;
      var prevVal = actSelect.value;
      var firstMatch = '';

      allActOptions.forEach(function (opt) {
        var match = !chosenCat || opt.getAttribute('data-category-name') === chosenCat;
        opt.hidden = !match;
        opt.disabled = !match;
        if (match && !firstMatch) firstMatch = opt.value;
      });

      if (!keepSelected || (actSelect.selectedOptions[0] && actSelect.selectedOptions[0].disabled)) {
        actSelect.value = firstMatch || '';
      } else {
        actSelect.value = prevVal;
      }
      syncPickupVisibility();
    };

    if (catSelect) {
      catSelect.addEventListener('change', function () {
        filterActivitiesByCategory(false);
      });
    }
    if (actSelect) {
      actSelect.addEventListener('change', function () {
        var opt = actSelect.options[actSelect.selectedIndex];
        if (opt && catSelect && opt.getAttribute('data-category-name')) {
          catSelect.value = opt.getAttribute('data-category-name');
          allActOptions.forEach(function (o) {
            var m = o.getAttribute('data-category-name') === catSelect.value;
            o.hidden = !m;
            o.disabled = !m;
          });
        }
        syncPickupVisibility();
      });
    }

    if (childrenCount && childrenAgesWrap) {
      var syncChildrenAges = function () {
        var c = parseInt(childrenCount.value || '0', 10);
        childrenAgesWrap.style.opacity = c > 0 ? '1' : '0.75';
      };
      childrenCount.addEventListener('input', syncChildrenAges);
      syncChildrenAges();
    }

    filterActivitiesByCategory(true);
  });
})();
