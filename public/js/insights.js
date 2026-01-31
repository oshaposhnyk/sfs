/**
 * Insights.js - Systemic Insights Logic
 */

let map;
let feedFilter = 'all'; // 'all' or 'data'

document.addEventListener('DOMContentLoaded', () => {
    initChart();
    initMap(); // Leaflet
    initHubs();
    initFeedFilters();
});

function initChart() {
    const ctx = document.getElementById('healthChart').getContext('2d');

    new Chart(ctx, {
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
            cutout: '70%'
        }
    });
}

function initMap() {
    // Leaflet setup
    // Initial view: Ukraine center
    map = L.map('map').setView([49.0, 31.0], 6);

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 19
    }).addTo(map);
}

function initHubs() {
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
    activeBtn.classList.add('btn-primary'); // Assuming btn-primary exists or just using default active style logic
    // Actually, based on HTML usage:
    // Active was "btn btn-outline" (or inverse?). Let's stick to the visual language:
    // Primary/Filled = Active, Outline = Inactive

    // Let's force styles manually for clarity as I don't recall exact btn class logic
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
            item.offsetHeight; /* trigger reflow */
            item.style.animation = 'fadeIn 0.5s ease-in';
        } else {
            // "Data Only" -> Show only items with "Data" or "Digital Twin" badges
            // Let's assume badges containing "Data", "Sensor", "Twin", "Tech" are data.
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
