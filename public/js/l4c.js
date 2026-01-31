/**
 * L4C.js - Student Lab Logic
 */

document.addEventListener('DOMContentLoaded', () => {
    initRadarChart();
    initLiveTempChart();
    initCalendarModal();
    initJourneySimulation();
});

function initRadarChart() {
    const ctx = document.getElementById('competencyChart').getContext('2d');

    // Theme aware colors (read from CSS var if possible, else hardcode)
    // Simplified:
    const colorAccent = 'rgba(68, 161, 160, 0.5)';
    const colorBorder = '#44A1A0';

    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['Data Literacy', 'Systems Thinking', 'Technical', 'Communication', 'Ethics'],
            datasets: [{
                label: 'Student Profile',
                data: [4, 5, 3, 4, 5],
                backgroundColor: colorAccent,
                borderColor: colorBorder,
                borderWidth: 2,
                pointBackgroundColor: '#fff',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    angleLines: { color: 'rgba(0,0,0,0.1)' },
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    pointLabels: {
                        font: { size: 10, family: 'Figtree' }
                    },
                    suggestedMin: 0,
                    suggestedMax: 5
                }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
}

function initLiveTempChart() {
    const ctx = document.getElementById('liveTempChart').getContext('2d');

    // Fake live data
    const dataPoints = [4.2, 4.3, 4.1, 4.4, 4.3, 4.2, 4.3, 4.3, 4.2, 4.3];
    const labels = dataPoints.map((_, i) => i);

    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Temp',
                data: dataPoints,
                borderColor: '#44A1A0',
                borderWidth: 2,
                tension: 0.4,
                pointRadius: 0,
                fill: true,
                backgroundColor: 'rgba(68, 161, 160, 0.1)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { display: false },
                y: { display: false, min: 3, max: 6 }
            },
            plugins: { legend: { display: false } },
            animation: false
        }
    });

    // Simulate Live Feed
    setInterval(() => {
        const lastVal = dataPoints[dataPoints.length - 1];
        const change = (Math.random() - 0.5) * 0.2;
        let newVal = lastVal + change;
        newVal = Math.max(3.5, Math.min(5.5, newVal)); // Clamp

        dataPoints.shift();
        dataPoints.push(newVal);

        // Update DOM text if present
        const textEl = document.querySelector('.feed-value');
        if (textEl) textEl.textContent = newVal.toFixed(1) + '°C';

        chart.update();
    }, 2000);
}

function initCalendarModal() {
    const btnOpen = document.getElementById('btnViewCalendar');
    const btnClose = document.getElementById('btnCloseCalendar');
    const modal = document.getElementById('calendarModal');
    const btnAddReminder = document.getElementById('btnAddReminder');

    if (btnOpen && modal) {
        btnOpen.addEventListener('click', () => {
            modal.classList.add('active');
        });
    }

    if (btnClose && modal) {
        btnClose.addEventListener('click', () => {
            modal.classList.remove('active');
        });
    }

    // Close on click outside
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
            }
        });
    }

    // Calendar Day Selection Logic
    const days = document.querySelectorAll('.cal-day');
    days.forEach(day => {
        if (!day.classList.contains('empty')) {
            day.addEventListener('click', () => {
                days.forEach(d => d.classList.remove('selected'));
                day.classList.add('selected');
            });
        }
    });

    // Add Reminder Logic
    if (btnAddReminder) {
        btnAddReminder.addEventListener('click', () => {
            const selectedDay = document.querySelector('.cal-day.selected');
            if (selectedDay) {
                const dayNum = selectedDay.innerText;
                alert(`Reminder added for Feb ${dayNum}, 2026!`);

                // Add visual indicator (dot)
                selectedDay.classList.add('event');
                selectedDay.classList.remove('selected'); // Deselect

            } else {
                alert("Please select a date first.");
            }
        });
    }
}

function initJourneySimulation() {
    const btn = document.getElementById('btnStartSim');
    if (!btn) return;

    btn.addEventListener('click', () => {
        btn.innerHTML = '<span class="material-icons spin" style="font-size:1rem; vertical-align:middle;">sync</span> Running Simulation...';
        btn.disabled = true;

        setTimeout(() => {
            // Success State
            btn.innerHTML = '✔ Phase Completed';
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline');
            btn.style.borderColor = 'green';
            btn.style.color = 'green';

            // Find parent timeline station
            const activeStation = btn.closest('.timeline-station');
            if (activeStation) {
                // Change style to completed
                activeStation.classList.remove('active');
                activeStation.classList.add('completed');
                activeStation.querySelector('strong').innerText = 'PHASE 2: COMPLETED';
            }

            // Unlock next phase
            const nextStation = activeStation.nextElementSibling;
            if (nextStation && nextStation.classList.contains('timeline-station')) {
                nextStation.style.opacity = '1';
                nextStation.classList.add('active');
                nextStation.querySelector('small').innerText = 'Unlocked! Access granted.';
            }

        }, 2000);
    });
}
