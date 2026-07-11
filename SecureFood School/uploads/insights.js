/**
 * Insights.js - Systemic Insights Logic
 */

let map;
let healthChart;
let feedFilter = 'all'; // 'all' or 'data'

document.addEventListener('DOMContentLoaded', () => {
    initChart();
    initHeroInsights();
    initMap(); // Leaflet
    initHubs();
    initFeedFilters();
});

function initChart() {
    const canvas = document.getElementById('healthChart');
    if (!canvas || typeof Chart === 'undefined') return;
    const ctx = canvas.getContext('2d');

    healthChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Healthy', 'At Risk', 'Critical'],
            datasets: [{
                data: [78, 15, 7],
                backgroundColor: ['#44A1A0', '#DBA159', '#073B4C'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            },
            animation: {
                duration: 720,
                easing: 'easeOutQuart'
            },
            cutout: '72%'
        }
    });
}

function initHeroInsights() {
    const counters = Array.from(document.querySelectorAll('[data-hero-counter]'));
    if (counters.length) {
        animateHeroCounters(counters);
    }

    const updatedAtEl = document.getElementById('heroUpdatedAt');
    const primarySignalEl = document.getElementById('heroSignalPrimary');
    const primaryDetailEl = document.getElementById('heroSignalDetail');
    const trendSignalEl = document.getElementById('heroSignalTrend');
    const trendDetailEl = document.getElementById('heroSignalTrendDetail');

    const snapshots = [
        {
            updated: 'Updated just now',
            primarySignal: 'Cold chain stable',
            primaryDetail: '9 hubs stay in safe threshold',
            trendSignal: 'Water quality queue',
            trendDetail: '2 filters scheduled in next 48h',
            chartData: [78, 15, 7]
        },
        {
            updated: 'Updated 3 min ago',
            primarySignal: 'Thermal drift contained',
            primaryDetail: 'Rapid response resolved 4 alerts',
            trendSignal: 'Mentor review queue',
            trendDetail: '6 reports awaiting verification',
            chartData: [80, 13, 7]
        },
        {
            updated: 'Updated 6 min ago',
            primarySignal: 'Supply node synchronized',
            primaryDetail: 'Cross-region dispatch completed',
            trendSignal: 'Feedback translation',
            trendDetail: '82% validated within 24 hours',
            chartData: [77, 16, 7]
        }
    ];

    if (!updatedAtEl && !primarySignalEl && !trendSignalEl && !healthChart) return;

    let snapshotIndex = 0;
    setInterval(() => {
        snapshotIndex = (snapshotIndex + 1) % snapshots.length;
        const snapshot = snapshots[snapshotIndex];

        if (updatedAtEl) updatedAtEl.textContent = snapshot.updated;
        if (primarySignalEl) primarySignalEl.textContent = snapshot.primarySignal;
        if (primaryDetailEl) primaryDetailEl.textContent = snapshot.primaryDetail;
        if (trendSignalEl) trendSignalEl.textContent = snapshot.trendSignal;
        if (trendDetailEl) trendDetailEl.textContent = snapshot.trendDetail;

        if (healthChart) {
            healthChart.data.datasets[0].data = snapshot.chartData;
            healthChart.update();
        }
    }, 7000);
}

function animateHeroCounters(counters) {
    const durationMs = 1100;
    const startTime = performance.now();

    const draw = (timeNow) => {
        const elapsed = Math.min(1, (timeNow - startTime) / durationMs);
        const easedProgress = 1 - Math.pow(1 - elapsed, 3);

        counters.forEach(counterEl => {
            const target = Number(counterEl.dataset.target || 0);
            const format = counterEl.dataset.format || 'int';
            const currentValue = target * easedProgress;
            counterEl.textContent = formatCounterValue(currentValue, target, format);
        });

        if (elapsed < 1) {
            requestAnimationFrame(draw);
        }
    };

    requestAnimationFrame(draw);
}

function formatCounterValue(value, target, format) {
    if (format === 'k') {
        if (target < 1000) {
            return String(Math.round(value));
        }
        return `${(value / 1000).toFixed(1)}k`;
    }

    if (format === 'percent1') {
        return `${value.toFixed(1)}%`;
    }

    return String(Math.round(value));
}

function initMap() {
    const mapContainer = document.getElementById('map');
    if (!mapContainer || typeof L === 'undefined') return;

    // Leaflet setup
    // Initial view: Ukraine center
    map = L.map(mapContainer).setView([49.0, 31.0], 6);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);
}

function initHubs() {
    if (!map) return;

    // 12 Hubs
    const hubs = [
        { name: "Kyiv Hub", coords: [50.4501, 30.5234], status: "Active", color: "#44A1A0" },
        { name: "Lviv Hub", coords: [49.8397, 24.0297], status: "Active", color: "#DBA159" },
        { name: "Odesa Hub", coords: [46.4825, 30.7233], status: "Maintenance", color: "#5A7D8A" },
        { name: "Kharkiv Hub", coords: [49.9935, 36.2304], status: "Active", color: "#44A1A0" },
        { name: "Dnipro Hub", coords: [48.4647, 35.0462], status: "Active", color: "#44A1A0" },
        { name: "Vinnytsia Hub", coords: [49.2331, 28.4682], status: "Active", color: "#DBA159" },
        { name: "Zaporizhzhia Hub", coords: [47.8388, 35.1396], status: "Active", color: "#44A1A0" },
        { name: "Ivano-Frankivsk", coords: [48.9226, 24.7111], status: "Active", color: "#44A1A0" },
        { name: "Chernivtsi Hub", coords: [48.2908, 25.9348], status: "Active", color: "#DBA159" },
        { name: "Ternopil Hub", coords: [49.5535, 25.5948], status: "Maintenance", color: "#5A7D8A" },
        { name: "Poltava Hub", coords: [49.5883, 34.5514], status: "Active", color: "#44A1A0" },
        { name: "Cherkasy Hub", coords: [49.4444, 32.0598], status: "Active", color: "#44A1A0" }
    ];

    const listContainer = document.getElementById('hubList');
    if (!listContainer) return;
    listContainer.innerHTML = '';

    hubs.forEach((hub, index) => {
        // Add Marker to Map
        L.circleMarker(hub.coords, {
            color: hub.color,
            radius: 8,
            fillOpacity: 0.8
        }).addTo(map).bindPopup(`<b>${hub.name}</b><br>Status: ${hub.status}`);

        // Create List Item
        const li = document.createElement('li');
        li.style.padding = '15px 10px'; // Added horizontal padding for cleaner scroll look
        // Add border bottom to all except last
        if (index < hubs.length - 1) {
            li.style.borderBottom = '1px solid var(--clr-border)';
        }
        li.style.cursor = 'pointer';
        li.style.transition = 'background 0.2s';

        // Define badge class based on status
        let badgeClass = 'badge-accent';
        let badgeBg = '';
        if (hub.status === 'Maintenance') {
            badgeClass = 'badge';
            badgeBg = 'background:var(--clr-surface-alt);';
        }

        li.innerHTML = `
            <strong style="color:${hub.status === 'Active' ? 'var(--clr-text-main)' : 'var(--clr-text-muted)'};">${hub.name}</strong>
            <span style="float:right; ${badgeBg}" class="badge ${badgeClass}">${hub.status}</span>
        `;

        // Click Event
        li.addEventListener('click', () => {
            map.flyTo(hub.coords, 10, {
                animate: true,
                duration: 1.5
            });
            // Also highlight list item briefly
            li.style.backgroundColor = 'var(--clr-surface-alt)';
            setTimeout(() => li.style.backgroundColor = 'transparent', 500);
        });

        li.addEventListener('mouseenter', () => {
            li.style.backgroundColor = 'var(--clr-surface-alt)';
        });
        li.addEventListener('mouseleave', () => {
            li.style.backgroundColor = 'transparent';
        });

        listContainer.appendChild(li);
    });
}

function initFeedFilters() {
    const btnAll = document.getElementById('btnStreamAll');
    const btnData = document.getElementById('btnStreamData');

    if (!btnAll || !btnData) return;

    btnAll.addEventListener('click', () => {
        setFeedFilter('all', btnAll, btnData);
    });

    btnData.addEventListener('click', () => {
        setFeedFilter('data', btnData, btnAll);
    });
}

function setFeedFilter(type, activeBtn, inactiveBtn) {
    feedFilter = type;

    // Toggle Button Styles
    activeBtn.classList.remove('btn-outline');
    activeBtn.classList.add('btn-primary');

    activeBtn.style.background = 'var(--clr-primary)';
    activeBtn.style.color = '#ffffff';
    activeBtn.style.border = '1px solid var(--clr-primary)';

    inactiveBtn.style.background = 'var(--clr-surface)';
    inactiveBtn.style.color = 'var(--clr-text-main)';
    inactiveBtn.style.border = '1px solid var(--clr-border)';

    // Filter Items
    const items = document.querySelectorAll('.feed-card');
    items.forEach(item => {
        if (type === 'all') {
            item.style.display = 'block';
            // Animation reset
            item.style.animation = 'none';
            item.offsetHeight;
            item.style.animation = 'fadeIn 0.5s ease-in';
        } else {
            // "Data Only" -> Show only items with "Data" or "Digital Twin" badges
            const text = item.innerText.toLowerCase();
            const isData = text.includes('sensor') || text.includes('twin') || text.includes('data') || text.includes('tech') || text.includes('iot');

            if (isData) {
                item.style.display = 'block';
                item.style.animation = 'fadeIn 0.5s ease-in';
            } else {
                item.style.display = 'none';
            }
        }
    });
}
