/**
 * FFS.js - Future Food School Logic
 */

document.addEventListener('DOMContentLoaded', () => {
    initDecisionGame();
    initMissionLogic();
});

// --- Decision Game Logic ---

function initDecisionGame() {
    // No specific initialization needed for onclick handlers in HTML,
    // but we can add global listeners if preferred.
    // Keeping simple onclick for now as per previous design.
}

window.correctChoice = function (btn) {
    const card = btn.closest('.card');
    card.innerHTML = `
        <div class="fade-in" style="text-align:center; padding:20px;">
            <div style="font-size:3rem;">🎉</div>
            <h3 style="color:var(--clr-accent)">Excellent Choice!</h3>
            <p>Rejecting unsafe food builds trust. You gained <strong>+50 XP</strong> in Food Safety.</p>
            <button class="btn btn-outline" onclick="location.reload()">Next Scenario</button>
        </div>
    `;
    // Trigger XP animation if sidebar exists? (Simulated)
}

window.wrongChoice = function (btn) {
    const card = btn.closest('.card');
    card.innerHTML = `
        <div class="fade-in" style="text-align:center; padding:20px;">
            <div style="font-size:3rem;">⚠️</div>
            <h3 style="color:var(--clr-highlight)">Risky Business...</h3>
            <p>Saving money isn't worth a potential outbreak. Monitor trust score decreased.</p>
            <button class="btn btn-outline" onclick="location.reload()">Try Again</button>
        </div>
    `;
}

// --- Mission & Resume Logic ---

function initMissionLogic() {
    // 1. Resume Video Progress
    const btnResume = document.getElementById('btnResumeMission');
    const progressBar = document.getElementById('videoProgressBar');

    if (btnResume && progressBar) {
        btnResume.addEventListener('click', () => {
            btnResume.innerText = 'Playing...';
            btnResume.classList.add('btn-primary');
            btnResume.classList.remove('btn-outline');

            // Simulate progress filling up
            let width = 40; // element has 40% inline style initially
            const interval = setInterval(() => {
                width += 1;
                progressBar.style.width = width + '%';

                if (width >= 100) {
                    clearInterval(interval);
                    btnResume.innerText = 'Completed ✔';
                    btnResume.disabled = true;
                    // Maybe trigger confetti?
                }
            }, 50); // Fast fill for demo
        });
    }

    // 2. Start Mission
    const btnStart = document.getElementById('btnStartMission');
    if (btnStart) {
        btnStart.addEventListener('click', () => {
            // Animate button
            const originalText = btnStart.innerText;
            btnStart.innerHTML = '<span class="material-icons spin" style="font-size:1rem; vertical-align:middle;">sync</span> Initializing...';

            // Simulate loading
            setTimeout(() => {
                btnStart.innerHTML = '🚀 Mission Started!';
                btnStart.classList.add('btn-accent'); // Assuming accent color class

                // Maybe redirect or show overlay?
                setTimeout(() => {
                    alert("Mission 'Urban Farming' initialized! (Simulation)");
                    btnStart.innerText = originalText;
                    btnStart.classList.remove('btn-accent');
                }, 1000);
            }, 1500);
        });
    }
}
