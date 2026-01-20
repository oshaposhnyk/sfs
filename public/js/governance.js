/**
 * Governance.js - Dashboard Logic
 */

document.addEventListener('DOMContentLoaded', () => {
    startKpiSimulation();
});

function startKpiSimulation() {
    // Select elements with numeric values to jitter
    const socialIndex = document.querySelector('.kpi-value-social');

    if (socialIndex) {
        setInterval(() => {
            // Random small fluctuation
            const current = parseFloat(socialIndex.innerText);
            const change = (Math.random() - 0.5) * 0.1;
            let newVal = (current + change).toFixed(1);
            if (newVal > 5) newVal = 5.0;
            if (newVal < 1) newVal = 1.0;

            socialIndex.innerText = newVal;

            // Visual indicator
            const el = socialIndex;
            el.style.color = change > 0 ? "var(--clr-accent)" : "var(--clr-highlight)";
            setTimeout(() => el.style.color = "", 500);

        }, 5000);
    }
}

// Voting Logic
window.submitVote = function (type) {
    const parent = document.getElementById('votePanel');
    parent.innerHTML = `
        <div class="fade-in" style="text-align:center; padding:20px;">
            <h3 style="color:var(--clr-accent)">Thank you!</h3>
            <p>Your input has been recorded on the blockchain.</p>
            <div style="width:100%; height:4px; background:var(--clr-border); margin-top:10px;">
                <div style="width:0%; height:100%; background:var(--clr-accent); transition:width 1s;" id="voteProgress"></div>
            </div>
            <small style="display:block; margin-top:5px;">Validating...</small>
        </div>
    `;

    setTimeout(() => {
        document.getElementById('voteProgress').style.width = '100%';
    }, 100);
}
