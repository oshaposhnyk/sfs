/* Shared shell + interactions for all SecureFood School pages */

(function() {
  // Inject Material Icons font link as early as possible (more reliable than @import in CSS)
  if (!document.querySelector('link[data-mi]')) {
    const l = document.createElement('link');
    l.rel = 'stylesheet';
    l.href = 'https://fonts.googleapis.com/icon?family=Material+Icons';
    l.setAttribute('data-mi', '');
    document.head.appendChild(l);
  }

  window.SF = window.SF || {};

  // Inject a reusable sidebar+topbar pair
  window.SF.shell = function(opts) {
    const { crumb = [] } = opts || {};
    const crumbHTML = crumb.map((c, i) => {
      const last = i === crumb.length - 1;
      return last
        ? `<strong>${c}</strong>`
        : `<span>${c}</span><span class="material-symbols-rounded" style="font-size:14px;">chevron_right</span>`;
    }).join('');

    return `
      <aside class="sidebar" id="sf-sidebar">
        <div class="brand">
          <img src="assets/logo-full-light.png" alt="SecureFood School" class="brand-logo-full brand-logo-light">
          <img src="assets/logo-full-dark.png" alt="SecureFood School" class="brand-logo-full brand-logo-dark">
          <img src="assets/logo-icon-light.png" alt="SecureFood School" class="brand-logo-icon brand-logo-light">
          <img src="assets/logo-icon-dark.png" alt="SecureFood School" class="brand-logo-icon brand-logo-dark">
        </div>

        <div class="nav-section-label">Learning</div>
        <a href="insights.html" class="nav-item" data-page="insights" data-tip="About the Project">
          <span class="material-symbols-rounded">info</span>
          <span class="nav-label">About the Project</span>
        </a>
        <a href="l4c.html" class="nav-item" data-page="l4c" data-tip="Student Lab">
          <span class="material-symbols-rounded">school</span>
          <span class="nav-label">Student Lab</span>
        </a>
        <a href="ffs.html" class="nav-item" data-page="ffs" data-tip="Future Food">
          <span class="material-symbols-rounded">videogame_asset</span>
          <span class="nav-label">Future Food</span>
        </a>
        <a href="governance.html" class="nav-item" data-page="governance" data-tip="Resources &amp; Standards">
          <span class="material-symbols-rounded">gavel</span>
          <span class="nav-label">Resources &amp; Standards</span>
        </a>

        <div class="nav-section-label">Account</div>
        <a href="#" class="nav-item" data-tip="Notifications">
          <span class="material-symbols-rounded">notifications</span>
          <span class="nav-label">Notifications</span>
        </a>
        <a href="#" class="nav-item" data-tip="Messages">
          <span class="material-symbols-rounded">forum</span>
          <span class="nav-label">Messages</span>
        </a>

        <div class="sidebar-spacer"></div>
        <div class="user-card">
          <div class="avatar">OS</div>
          <div class="meta">
            <div class="name">Olexandr S.</div>
            <div class="role">Student · Cohort 2026</div>
          </div>
        </div>
      </aside>

      <div class="main-col">
        <div class="topbar">
          <button class="icon-btn topbar-toggle" id="sf-sidebar-toggle" aria-label="Toggle sidebar">
            <span class="material-symbols-rounded">menu</span>
          </button>
          <div class="crumb">${crumbHTML}</div>
          <div class="search">
            <span class="material-symbols-rounded">search</span>
            <input type="text" placeholder="Search courses, documents…">
          </div>
          <div class="topbar-actions">
            <button class="icon-btn" id="sf-theme-toggle" aria-label="Toggle theme">
              <span class="material-symbols-rounded" id="sf-theme-icon">dark_mode</span>
            </button>
            <button class="icon-btn has-dot" aria-label="Notifications">
              <span class="material-symbols-rounded">notifications</span>
            </button>
            <button class="icon-btn" aria-label="Help">
              <span class="material-symbols-rounded">help</span>
            </button>
          </div>
        </div>

        <main class="content" id="page-content"></main>
      </div>

      <div class="sidebar-backdrop" id="sf-sidebar-backdrop"></div>
    `;
  };

  function applyStoredTheme() {
    const stored = localStorage.getItem('sf-theme');
    if (stored) document.documentElement.dataset.theme = stored;
    syncThemeIcon();
  }
  function syncThemeIcon() {
    const icon = document.getElementById('sf-theme-icon');
    if (!icon) return;
    const cur = document.documentElement.dataset.theme || 'light';
    icon.textContent = cur === 'dark' ? 'light_mode' : 'dark_mode';
  }
  function applyStoredCollapsed() {
    const collapsed = localStorage.getItem('sf-sidebar-collapsed') === '1';
    document.documentElement.classList.toggle('sidebar-collapsed', collapsed);
  }

  function bindShell() {
    // Active nav highlight
    const page = document.body.dataset.page;
    if (page) {
      document.querySelectorAll('.sidebar .nav-item').forEach(a => {
        if (a.dataset.page === page) a.classList.add('active');
      });
    }

    // Theme toggle
    const themeBtn = document.getElementById('sf-theme-toggle');
    if (themeBtn) {
      themeBtn.addEventListener('click', () => {
        const cur = document.documentElement.dataset.theme || 'light';
        const next = cur === 'light' ? 'dark' : 'light';
        document.documentElement.dataset.theme = next;
        localStorage.setItem('sf-theme', next);
        syncThemeIcon();
      });
    }

    // Sidebar toggle
    const sideBtn = document.getElementById('sf-sidebar-toggle');
    const backdrop = document.getElementById('sf-sidebar-backdrop');
    const isMobile = () => window.matchMedia('(max-width: 820px)').matches;

    if (sideBtn) {
      sideBtn.addEventListener('click', () => {
        if (isMobile()) {
          document.documentElement.classList.toggle('sidebar-mobile-open');
        } else {
          const isCollapsed = document.documentElement.classList.toggle('sidebar-collapsed');
          localStorage.setItem('sf-sidebar-collapsed', isCollapsed ? '1' : '0');
        }
      });
    }
    if (backdrop) {
      backdrop.addEventListener('click', () => {
        document.documentElement.classList.remove('sidebar-mobile-open');
      });
    }

    applyStoredTheme();
    applyStoredCollapsed();
  }

  // Easy mount
  window.SF.mount = function(opts, contentHTML) {
    document.body.innerHTML = `<div class="app">${window.SF.shell(opts)}</div>`;
    document.getElementById('page-content').innerHTML = contentHTML;
    bindShell();
  };

  // Eager apply (before mount, to avoid flash)
  applyStoredTheme();
  applyStoredCollapsed();
})();
