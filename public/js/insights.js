/**
 * Insights.js - Feed & Charts
 */
let healthChart;

document.addEventListener('DOMContentLoaded', () => {
    initChart();

    // Simulate live feed
    setInterval(addFeedItem, 8000);
});

// Update chart when theme changes
window.addEventListener('themeChanged', (e) => {
    if (healthChart) {
        const isDark = e.detail === 'dark';
        healthChart.options.plugins.legend.labels.color = isDark ? '#A8C0CB' : '#5A7D8A';
        healthChart.update();
    }
});

function initChart() {
    const ctx = document.getElementById('healthChart');
    if (!ctx) return;

    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';

    healthChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Technical', 'Social', 'Economic'],
            datasets: [{
                data: [45, 30, 25],
                backgroundColor: ['#44A1A0', '#DBA159', '#073B4C'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { color: isDark ? '#A8C0CB' : '#5A7D8A', font: { family: 'Figtree' } } }
            }
        }
    });
}

function addFeedItem() {
    const grid = document.querySelector('.masonry-feed');
    if (!grid) return;

    const messages = [
        { title: "Sensor Alert", tag: "IoT", text: "Freezer #4 in Kyiv School 12 reporting temp deviation.", type: "tech" },
        { title: "New Survey", tag: "Social", text: "Parents association voted 90% in favor of new menu.", type: "social" },
        { title: "Logistics", tag: "Supply", text: "Delivery truck arriving at Lviv Hub in 15 mins.", type: "tech" }
    ];

    const msg = messages[Math.floor(Math.random() * messages.length)];

    const card = document.createElement('div');
    card.className = "card feed-card fade-in";
    const bgClass = msg.type === 'tech' ? 'thumb-gradient-1' : 'thumb-gradient-2';

    card.innerHTML = `
        <div class="feed-img ${bgClass}" style="height:100px;"></div>
        <div style="padding:15px;">
            <span class="badge ${msg.type === 'tech' ? 'badge-accent' : 'badge-highlight'}">${msg.tag}</span>
            <h4 style="margin:10px 0 5px 0;">${msg.title}</h4>
            <p style="font-size:0.9rem; color:var(--clr-text-muted);">${msg.text}</p>
            <small style="color:var(--clr-accent)">Just now</small>
        </div>
    `;

    grid.prepend(card);

    // Animate in
    if (typeof gsap !== 'undefined') {
        gsap.from(card, { y: -20, opacity: 0, duration: 0.5 });
    }

    // Remove last if too many
    if (grid.children.length > 6) {
        grid.lastElementChild.remove();
    }
}
