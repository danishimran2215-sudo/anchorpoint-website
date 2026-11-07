/**
 * Anchor Point — Admin Dashboard JS
 * Handles Theme Toggle, Mobile Sidebar, Search/Filter in tables, Status updating
 */

document.addEventListener('DOMContentLoaded', () => {
  // Theme Toggle
  const themeBtn = document.getElementById('theme-toggle');
  const currentTheme = localStorage.getItem('ap_admin_theme') || 'dark';
  document.documentElement.setAttribute('data-theme', currentTheme);

  if (themeBtn) {
    themeBtn.addEventListener('click', () => {
      const active = document.documentElement.getAttribute('data-theme');
      const next = active === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-theme', next);
      localStorage.setItem('ap_admin_theme', next);
    });
  }

  // Mobile sidebar toggle
  const sidebarBtn = document.getElementById('sidebar-toggle');
  const sidebar = document.querySelector('.admin-sidebar');
  if (sidebarBtn && sidebar) {
    sidebarBtn.addEventListener('click', () => {
      sidebar.classList.toggle('show');
    });
  }

  // Client-side table search
  const searchInput = document.getElementById('table-search');
  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      const term = e.target.value.toLowerCase();
      const rows = document.querySelectorAll('.admin-table tbody tr');
      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
      });
    });
  }

  // Auto-dismiss alerts
  const alert = document.querySelector('.admin-alert');
  if (alert) {
    setTimeout(() => {
      alert.style.opacity = '0';
      setTimeout(() => alert.remove(), 400);
    }, 4000);
  }
});
