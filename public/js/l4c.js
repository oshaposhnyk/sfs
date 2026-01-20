/**
 * L4C.js - Student Lab Logic
 */

document.addEventListener('DOMContentLoaded', () => {
    initRadar();
    initLiveGraph();
});

let radarChart;

// Dynamic Radar colors based on theme
window.addEventListener('themeChanged', (e) => {
    updateRadarTheme(e.detail);
});

function initRadar() {
    const ctx = document.getElementById('competencyChart');
    if (!ctx) return;

    radarChart = new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['Tech', 'Social', 'Data', 'Ethics', 'Systems'],
            datasets: [{
                label: 'Student Profile',
                data: [80, 60, 75, 90, 65],
                backgroundColor: 'rgba(68, 161, 160, 0.2)',
                borderColor: '#44A1A0',
                pointBackgroundColor: '#44A1A0'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    angleLines: { color: 'rgba(128,128,128,0.2)' },
                    grid: { color: 'rgba(128,128,128,0.2)' },
                    pointLabels: { color: '#5A7D8A' }
                }
            },
            plugins: { legend: { display: false } }
        }
    });
}

function updateRadarTheme(theme) {
    if (!radarChart) return;
    const color = theme === 'dark' ? '#A8C0CB' : '#5A7D8A';
    radarChart.options.scales.r.pointLabels.color = color;
    radarChart.update();
}

function initLiveGraph() {
    const ctx = document.getElementById('liveTempChart');
    if (!ctx) return;

    const liveChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: Array(10).fill(''),
            datasets: [{
                data: [4, 4.2, 4.1, 4.3, 4.0, 4.2, 3.9, 4.1, 4.2, 4.0],
                borderColor: '#DBA159',
                borderWidth: 2,
                tension: 0.4,
                pointRadius: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { display: false }, y: { display: false, min: 2, max: 6 } },
            animation: false
        }
    });

    // Simulate Data Stream
    setInterval(() => {
        const data = liveChart.data.datasets[0].data;
        const newVal = 4.0 + (Math.random() - 0.5);
        data.shift();
        data.push(newVal);
        liveChart.update('none');

        const display = document.querySelector('.feed-value');
        if (display) display.innerText = newVal.toFixed(1) + "°C";
    }, 2000);
}
