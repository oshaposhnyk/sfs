/**
 * Utils.js - Shared logic for SecureFood pages
 */

// --- 1. THEME MANAGER ---
document.addEventListener('DOMContentLoaded', () => {
    injectSidebar(); // Helper to update sidebar dynamically if needed
    initTheme();
    initAnimations();
});

function initTheme() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);

    // Update Toggle Button if it exists
    updateToggleBtn(savedTheme);

    // Listen for toggle click
    document.body.addEventListener('click', (e) => {
        if (e.target.closest('#themeToggle')) {
            const current = document.documentElement.getAttribute('data-theme');
            const newTheme = current === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateToggleBtn(newTheme);

            // Dispatch event for other scripts (e.g. Charts need to update colors)
            window.dispatchEvent(new CustomEvent('themeChanged', { detail: newTheme }));
        }
    });
}

function updateToggleBtn(theme) {
    const btnBox = document.getElementById('themeToggle');
    if (!btnBox) return;

    const icon = theme === 'light' ? '🌙' : '☀️';
    const text = theme === 'light' ? 'Dark Mode' : 'Light Mode';
    btnBox.innerHTML = `<span>${icon}</span> <span class="nav-text">${text}</span>`;
}

// --- 2. ANIMATIONS (GSAP) ---
function initAnimations() {
    if (typeof gsap !== 'undefined') {
        // Staggered entry for cards
        gsap.from(".card:not(.insights-hero-panel)", {
            duration: 0.6,
            y: 30,
            opacity: 0,
            stagger: 0.1,
            ease: "power2.out",
            delay: 0.2
        });

        // Header fade in
        gsap.from("h1, .hero-title", {
            duration: 0.8,
            x: -20,
            opacity: 0,
            ease: "power2.out"
        });
    }
}

// --- 3. DYNAMIC SIDEBAR (Add Toggle if missing) ---
function injectSidebar() {
    const sidebar = document.querySelector('aside.sidebar-nav');
    if (sidebar) {
        ensureSidebarUser(sidebar);

        if (!document.getElementById('themeToggle')) {
            const toggleDiv = document.createElement('div');
            toggleDiv.className = 'theme-toggle-container';
            toggleDiv.innerHTML = `<button id="themeToggle" class="theme-btn"><span>🌙</span><span class="nav-text">Dark Mode</span></button>`;
            sidebar.appendChild(toggleDiv);
        }

        // Add logo text if missing
        const logoDiv = sidebar.querySelector('.logo-icon');
        if (logoDiv && !sidebar.querySelector('.logo-text')) {
            const logoArea = document.createElement('div');
            logoArea.className = 'logo-area';
            logoArea.appendChild(logoDiv.cloneNode(true));
            logoArea.innerHTML += `<span class="logo-text">SecureFood</span>`;

            // Replace old logo container with new one
            logoDiv.parentNode.insertBefore(logoArea, logoDiv);
            logoDiv.remove();
        }

        initSidebarUserMenu(sidebar);
    }
}

function ensureSidebarUser(sidebar) {
    if (sidebar.querySelector('.sidebar-user-wrap')) return;

    const userWrap = document.createElement('div');
    userWrap.className = 'sidebar-user-wrap';

    const userButton = document.createElement('button');
    userButton.type = 'button';
    userButton.className = 'sidebar-user';
    userButton.setAttribute('aria-haspopup', 'menu');
    userButton.setAttribute('aria-expanded', 'false');
    userButton.innerHTML = `
        <div class="sidebar-user-avatar">OS</div>
        <div class="sidebar-user-meta">
            <strong class="sidebar-user-name">Olexandr Shaposhnyk</strong>
            <small class="sidebar-user-role">Analyst-Translator</small>
        </div>
        <span class="material-icons sidebar-user-caret">expand_more</span>
    `;
    userWrap.appendChild(userButton);

    const themeContainer = sidebar.querySelector('.theme-toggle-container');
    if (themeContainer) {
        sidebar.insertBefore(userWrap, themeContainer);
    } else {
        sidebar.appendChild(userWrap);
    }
}

function initSidebarUserMenu(sidebar) {
    const userButton = sidebar.querySelector('.sidebar-user');
    if (!userButton || userButton.dataset.menuReady === 'true') return;

    const menu = ensureSidebarUserMenu();

    const closeMenu = () => {
        userButton.classList.remove('is-open');
        userButton.setAttribute('aria-expanded', 'false');
        menu.classList.remove('is-open');
    };

    const openMenu = () => {
        userButton.classList.add('is-open');
        userButton.setAttribute('aria-expanded', 'true');
        menu.classList.add('is-open');
        positionSidebarUserMenu(menu, userButton);
    };

    userButton.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();

        if (menu.classList.contains('is-open')) {
            closeMenu();
            return;
        }

        openMenu();
    });

    menu.addEventListener('click', () => {
        closeMenu();
    });

    document.addEventListener('click', (event) => {
        if (!menu.classList.contains('is-open')) return;
        if (menu.contains(event.target) || userButton.contains(event.target)) return;
        closeMenu();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMenu();
        }
    });

    window.addEventListener('resize', () => {
        if (menu.classList.contains('is-open')) {
            positionSidebarUserMenu(menu, userButton);
        }
    });

    window.addEventListener('scroll', () => {
        if (menu.classList.contains('is-open')) {
            positionSidebarUserMenu(menu, userButton);
        }
    }, true);

    userButton.dataset.menuReady = 'true';
}

function ensureSidebarUserMenu() {
    const existingMenu = document.querySelector('.sidebar-user-menu');
    if (existingMenu) return existingMenu;

    const rootPrefix = window.location.pathname.includes('/mobile/') ? '..' : '.';
    const menu = document.createElement('div');
    menu.className = 'sidebar-user-menu';
    menu.setAttribute('role', 'menu');
    menu.innerHTML = `
        <a class="sidebar-user-menu-item" data-action="profile" role="menuitem" href="${rootPrefix}/user/profile.php">
            <span class="material-icons">person</span>
            <span>Profile</span>
        </a>
        <a class="sidebar-user-menu-item" data-action="settings" role="menuitem" href="${rootPrefix}/user/preferences.php">
            <span class="material-icons">settings</span>
            <span>Settings</span>
        </a>
        <a class="sidebar-user-menu-item" data-action="login" role="menuitem" href="${rootPrefix}/login/index.php">
            <span class="material-icons">login</span>
            <span>Login</span>
        </a>
    `;
    document.body.appendChild(menu);
    return menu;
}

function positionSidebarUserMenu(menu, userButton) {
    const buttonRect = userButton.getBoundingClientRect();
    const menuWidth = 220;
    const menuHeight = Math.max(menu.offsetHeight, 170);
    const viewportWidth = window.innerWidth;
    const viewportHeight = window.innerHeight;

    let left = buttonRect.right + 12;
    if (left + menuWidth > viewportWidth - 12) {
        left = Math.max(12, buttonRect.left - menuWidth - 12);
    }

    let top = buttonRect.bottom - 6;
    if (top + menuHeight > viewportHeight - 12) {
        top = Math.max(12, viewportHeight - menuHeight - 12);
    }

    menu.style.left = `${Math.round(left)}px`;
    menu.style.top = `${Math.round(top)}px`;
}
