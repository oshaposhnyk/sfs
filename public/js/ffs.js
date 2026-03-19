/**
 * FFS.js - Future Food School Logic
 */

document.addEventListener('DOMContentLoaded', () => {
    initDecisionGame();
    initMissionHub();
    initMissionLogic();
});

// --- Decision Game Logic ---

function initDecisionGame() {
    // No specific initialization needed for onclick handlers in HTML,
    // but we can add global listeners if preferred.
    // Keeping simple onclick for now as per previous design.
}

function initMissionHub() {
    const liveBox = document.getElementById('missionHubLive');
    const liveStatus = document.getElementById('missionLiveStatus');
    if (!liveBox || !liveStatus) return;

    const states = [
        '2 missions active now',
        'Mentor feedback stream online',
        '1 mission completed in your cohort',
        'Live simulation synced'
    ];

    let stateIndex = 0;
    setInterval(() => {
        stateIndex = (stateIndex + 1) % states.length;
        liveStatus.textContent = states[stateIndex];
        liveBox.classList.remove('is-pulse');
        void liveBox.offsetWidth;
        liveBox.classList.add('is-pulse');
    }, 4500);
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
    const btnResume = document.getElementById('btnResumeMission');
    const progressBar = document.getElementById('videoProgressBar');
    const resumeStatus = document.getElementById('missionResumeStatus');
    const liveStatus = document.getElementById('missionLiveStatus');

    if (btnResume && progressBar) {
        let isRunning = false;
        btnResume.addEventListener('click', () => {
            if (isRunning || btnResume.disabled) return;
            isRunning = true;

            btnResume.innerText = 'Running...';
            btnResume.classList.add('btn-primary');
            btnResume.classList.remove('btn-outline');
            btnResume.disabled = true;

            let width = 40; // element has 40% inline style initially
            const interval = setInterval(() => {
                width += 1;
                progressBar.style.width = width + '%';
                if (resumeStatus) {
                    resumeStatus.innerText = `Progress: ${width}%`;
                }

                if (width >= 100) {
                    clearInterval(interval);
                    btnResume.innerText = 'Completed ✔';
                    if (resumeStatus) {
                        resumeStatus.innerText = 'Mission complete. Debrief unlocked.';
                    }
                    if (liveStatus) {
                        liveStatus.innerText = 'Mission completion registered';
                    }
                }
            }, 50); // Fast fill for demo
        });
    }

    const btnStart = document.getElementById('btnStartMission');
    if (btnStart) {
        btnStart.addEventListener('click', () => {
            if (btnStart.disabled) return;
            btnStart.disabled = true;
            btnStart.innerHTML = '<span class="material-icons spin" style="font-size:1rem; vertical-align:middle;">sync</span> Initializing...';

            setTimeout(() => {
                const missionCard = btnStart.closest('.mission-card');
                if (missionCard) {
                    missionCard.classList.add('is-active');
                }
                btnStart.innerHTML = 'Mission Started ✔';
                btnStart.classList.remove('btn-primary');
                btnStart.classList.add('btn-outline');
                if (liveStatus) {
                    liveStatus.innerText = 'New mission launched successfully';
                }
            }, 1500);
        });
    }
}
