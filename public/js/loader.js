(function () {
    // Inject Styles & Loader HTML immediately to ensure it covers content loading
    document.write(`
        <style>
            #app-loader {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100vh;
                background-color: #F8F9FA;
                z-index: 9999;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                transition: opacity 0.5s ease-out, visibility 0.5s;
            }
            [data-theme="dark"] #app-loader {
                background-color: #073B4C;
            }
            .loader-logo {
                width: 120px;
                height: auto;
                animation: pulse 2s infinite;
            }
            .loader-text {
                margin-top: 20px;
                font-family: sans-serif;
                font-size: 14px;
                font-weight: 600;
                letter-spacing: 2px;
                color: #44A1A0;
                text-transform: uppercase;
            }
            @keyframes pulse {
                0% { transform: scale(0.95); opacity: 0.8; }
                50% { transform: scale(1.05); opacity: 1; }
                100% { transform: scale(0.95); opacity: 0.8; }
            }
            body.loaded #app-loader {
                opacity: 0;
                visibility: hidden;
                pointer-events: none;
            }
        </style>
        <div id="app-loader">
            <img src="SecureFood-logo.png" class="loader-logo" alt="SecureFood">
            <div class="loader-text">Loading...</div>
        </div>
    `);

    // Hide loader when page is fully loaded
    window.addEventListener('load', () => {
        // Small delay to ensure minimum visibility of brand
        setTimeout(() => {
            document.body.classList.add('loaded');
            // Element removal for cleanup after transition
            setTimeout(() => {
                const loader = document.getElementById('app-loader');
                if (loader) loader.remove();
            }, 600);
        }, 500);
    });
})();
