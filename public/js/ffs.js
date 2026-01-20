/**
 * FFS.js - Agent Hub Logic
 */

document.addEventListener('DOMContentLoaded', () => {
    animateXP();
});

// Simulate reading saved progress
function animateXP() {
    const bar = document.getElementById('xpFill');
    if (bar) {
        setTimeout(() => {
            bar.style.width = '65%'; // Initial Load
        }, 500);
    }
}

// Game Logic exposed to global scope for HTML onclick
window.correctChoice = function (btn) {
    if (typeof gsap !== 'undefined') {
        gsap.to(btn, { scale: 1.05, backgroundColor: "#44A1A0", borderColor: "#44A1A0", color: "#fff", duration: 0.2 });
    } else {
        btn.classList.add('btn-primary');
    }
    btn.innerHTML = "✔ CORRECT ACTION";

    // XP Gain
    const bar = document.getElementById('xpFill');
    if (bar) bar.style.width = '85%';

    // Feedback
    const feedback = document.getElementById('feedback');
    feedback.classList.remove('hidden');
    feedback.classList.add('fade-in');

    // Disable others
    document.querySelectorAll('.choice-btn').forEach(b => {
        if (b !== btn) {
            b.style.opacity = '0.5';
            b.setAttribute('disabled', 'true');
        }
    });
}

window.wrongChoice = function (btn) {
    if (typeof gsap !== 'undefined') {
        gsap.to(btn, { x: 10, duration: 0.1, yoyo: true, repeat: 3 });
    }
    btn.style.backgroundColor = 'rgba(255, 0, 0, 0.1)';
    btn.style.borderColor = 'red';
    btn.innerText = "❌ Risky Choice";
}
