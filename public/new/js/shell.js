/* Shared shell + interactions for all SecureFood School pages */

(function() {
  window.SF = window.SF || {};
  const TOP_NOTIF_STORAGE_KEY = 'sf-topbar-notifications';
  const DEFAULT_TOP_NOTIFICATIONS = [
    {
      id: 'n-1',
      icon: 'grading',
      title: 'New grade posted: Data Quality Checks',
      meta: 'Assessment Office',
      time: '18 min ago',
      unread: true
    },
    {
      id: 'n-2',
      icon: 'workspace_premium',
      title: 'Badge earned: Streak Keeper',
      meta: 'Future Food',
      time: '1 h ago',
      unread: true
    },
    {
      id: 'n-3',
      icon: 'forum',
      title: 'Mentor replied in Report Translation Lab',
      meta: 'Olena H.',
      time: 'Today',
      unread: true
    },
    {
      id: 'n-4',
      icon: 'event',
      title: 'Live lab rescheduled to 16:00',
      meta: 'Student Lab',
      time: 'Yesterday',
      unread: false
    }
  ];

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
          <img src="./assets/SecureFood-logo.png" alt="SecureFood School" class="brand-logo-full">
          <div class="brand-mark">SF</div>
        </div>

        <div class="nav-section-label">Learning</div>
        <a href="./insights.html" class="nav-item" data-page="insights" data-tip="About the Project">
          <span class="material-symbols-rounded">info</span>
          <span class="nav-label">About the Project</span>
        </a>
        <a href="./l4c.html" class="nav-item" data-page="l4c" data-tip="Student Lab">
          <span class="material-symbols-rounded">school</span>
          <span class="nav-label">Student Lab</span>
        </a>
        <a href="./ffs.html" class="nav-item" data-page="ffs" data-tip="Future Food">
          <span class="material-symbols-rounded">stadia_controller</span>
          <span class="nav-label">Future Food</span>
        </a>
        <a href="./governance.html" class="nav-item" data-page="governance" data-tip="Resources &amp; Standards">
          <span class="material-symbols-rounded">gavel</span>
          <span class="nav-label">Resources &amp; Standards</span>
        </a>

        <div class="nav-section-label">Account</div>
        <a href="./notifications.html" class="nav-item" data-page="notifications" data-tip="Notifications">
          <span class="material-symbols-rounded">notifications</span>
          <span class="nav-label">Notifications</span>
        </a>
        <a href="./messages.html" class="nav-item" data-page="messages" data-tip="Messages">
          <span class="material-symbols-rounded">forum</span>
          <span class="nav-label">Messages</span>
        </a>
        <a href="./settings.html" class="nav-item" data-page="settings" data-tip="Settings">
          <span class="material-symbols-rounded">settings</span>
          <span class="nav-label">Settings</span>
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
            <div class="notif-wrap" id="sf-notif-wrap">
              <button class="icon-btn" id="sf-notif-toggle" type="button" aria-label="Notifications" aria-expanded="false">
                <span class="material-symbols-rounded">notifications</span>
              </button>
              <div class="notif-popover" id="sf-notif-popover" hidden>
                <div class="notif-popover-head">
                  <strong>Notifications</strong>
                  <button class="notif-mark-all" id="sf-notif-mark-all" type="button">Mark all read</button>
                </div>
                <div class="notif-popover-list" id="sf-notif-list"></div>
                <a href="./notifications.html" class="notif-popover-foot">Open notifications page</a>
              </div>
            </div>
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

  function escapeHTML(value) {
    return String(value)
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  function cloneDefaultTopNotifications() {
    return DEFAULT_TOP_NOTIFICATIONS.map(item => ({ ...item }));
  }

  function loadTopNotifications() {
    try {
      const raw = localStorage.getItem(TOP_NOTIF_STORAGE_KEY);
      if (!raw) return cloneDefaultTopNotifications();
      const parsed = JSON.parse(raw);
      if (!Array.isArray(parsed)) return cloneDefaultTopNotifications();
      return parsed
        .filter(item => item && item.id && item.title)
        .map(item => ({
          id: String(item.id),
          icon: item.icon ? String(item.icon) : 'notifications',
          title: String(item.title),
          meta: item.meta ? String(item.meta) : '',
          time: item.time ? String(item.time) : '',
          unread: Boolean(item.unread)
        }));
    } catch (_) {
      return cloneDefaultTopNotifications();
    }
  }

  function saveTopNotifications(items) {
    localStorage.setItem(TOP_NOTIF_STORAGE_KEY, JSON.stringify(items));
  }

  function bindTopbarNotifications() {
    const wrap = document.getElementById('sf-notif-wrap');
    const toggle = document.getElementById('sf-notif-toggle');
    const popover = document.getElementById('sf-notif-popover');
    const list = document.getElementById('sf-notif-list');
    const markAll = document.getElementById('sf-notif-mark-all');
    if (!wrap || !toggle || !popover || !list || !markAll) return;

    let items = loadTopNotifications();

    function unreadCount() {
      return items.filter(item => item.unread).length;
    }

    function syncToggleDot() {
      toggle.classList.toggle('has-dot', unreadCount() > 0);
    }

    function renderList() {
      syncToggleDot();
      markAll.disabled = unreadCount() === 0;
      if (!items.length) {
        list.innerHTML = '<div class="notif-empty">No notifications</div>';
        return;
      }

      list.innerHTML = items.slice(0, 8).map(item => `
        <button class="notif-item ${item.unread ? 'unread' : ''}" type="button" data-notif-id="${escapeHTML(item.id)}">
          <span class="notif-item-icon">
            <span class="material-symbols-rounded">${escapeHTML(item.icon || 'notifications')}</span>
          </span>
          <span class="notif-item-main">
            <span class="notif-item-title">${escapeHTML(item.title)}</span>
            <span class="notif-item-meta">${escapeHTML(item.meta)}</span>
          </span>
          <span class="notif-item-time">${escapeHTML(item.time)}</span>
        </button>
      `).join('');
    }

    function setOpen(next) {
      popover.hidden = !next;
      toggle.setAttribute('aria-expanded', next ? 'true' : 'false');
    }

    toggle.addEventListener('click', (event) => {
      event.stopPropagation();
      setOpen(popover.hidden);
    });

    document.addEventListener('click', (event) => {
      if (!wrap.contains(event.target)) setOpen(false);
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') setOpen(false);
    });

    markAll.addEventListener('click', () => {
      items = items.map(item => ({ ...item, unread: false }));
      saveTopNotifications(items);
      renderList();
    });

    list.addEventListener('click', (event) => {
      const row = event.target.closest('[data-notif-id]');
      if (!row) return;
      const id = row.dataset.notifId;
      const current = items.find(item => item.id === id);
      if (!current) return;
      current.unread = false;
      saveTopNotifications(items);
      renderList();
    });

    renderList();
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

    bindTopbarNotifications();
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
