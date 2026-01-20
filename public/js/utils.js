/**
 * Utils.js - Shared logic for SecureFood pages
 */

// --- 1. THEME MANAGER ---
document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initAnimations();
    injectSidebar(); // Helper to update sidebar dynamically if needed
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
        gsap.from(".card", {
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
    if (sidebar && !document.getElementById('themeToggle')) {
        const toggleDiv = document.createElement('div');
        toggleDiv.className = 'theme-toggle-container';
        toggleDiv.innerHTML = `<button id="themeToggle" class="theme-btn"><span>🌙</span><span class="nav-text">Dark Mode</span></button>`;
        sidebar.appendChild(toggleDiv);
        
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
    }
}
