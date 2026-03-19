/**
 * L4C.js - Student Lab Logic
 */

document.addEventListener('DOMContentLoaded', () => {
    initRadarChart();
    initLiveTempChart();
    initCalendarModal();
    initCourseAccordion();
    initJourneySimulation();
});

function initRadarChart() {
    const canvas = document.getElementById('competencyChart');
    if (!canvas || typeof Chart === 'undefined') return;
    const ctx = canvas.getContext('2d');

    const labels = ['Data Literacy', 'Systems Thinking', 'Technical', 'Communication', 'Ethics'];
    const baselineProfile = [4.1, 4.8, 3.4, 4.3, 4.7];
    const targetProfile = [5.0, 5.0, 4.6, 4.8, 5.0];

    const competencyPulsePlugin = {
        id: 'competencyPulsePlugin',
        afterDraw(chart) {
            const radarScale = chart.scales.r;
            if (!radarScale) return;

            const pulse = (Date.now() % 1800) / 1800;
            const { ctx } = chart;
            const glowColor = withAlpha(chart.data.datasets[0].borderColor, 0.16 * (1 - pulse));
            const radius = radarScale.drawingArea * (0.9 + pulse * 0.08);

            ctx.save();
            ctx.beginPath();
            ctx.arc(radarScale.xCenter, radarScale.yCenter, radius, 0, Math.PI * 2);
            ctx.strokeStyle = glowColor;
            ctx.lineWidth = 2;
            ctx.stroke();
            ctx.restore();
        }
    };

    const chart = new Chart(ctx, {
        type: 'radar',
        plugins: [competencyPulsePlugin],
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Student Profile',
                    data: [...baselineProfile],
                    borderWidth: 2.5,
                    pointRadius: 4.5,
                    pointHoverRadius: 6.5,
                    pointBorderWidth: 2
                },
                {
                    label: 'Program Target',
                    data: targetProfile,
                    borderWidth: 1.6,
                    pointRadius: 0,
                    fill: false,
                    borderDash: [6, 4]
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'nearest',
                intersect: false
            },
            animation: {
                duration: 1200,
                easing: 'easeOutQuart'
            },
            scales: {
                r: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        backdropColor: 'transparent'
                    },
                    pointLabels: {
                        font: {
                            size: 11,
                            family: 'Manrope',
                            weight: 700
                        }
                    },
                    suggestedMin: 0,
                    suggestedMax: 5
                }
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        boxWidth: 10,
                        boxHeight: 10,
                        font: {
                            family: 'Figtree',
                            size: 11,
                            weight: 600
                        }
                    }
                }
            }
        }
    });

    applyRadarTheme(chart, ctx, canvas);
    chart.update();

    // Lightweight living animation for profile fluctuations.
    setInterval(() => {
        if (document.hidden) return;
        chart.data.datasets[0].data = baselineProfile.map((baseValue, index) => {
            const trendBias = index === 1 || index === 4 ? 0.04 : 0;
            const drift = (Math.random() - 0.5) * 0.18 + trendBias;
            const nextValue = baseValue + drift;
            return Math.max(2.6, Math.min(5.0, Number(nextValue.toFixed(2))));
        });
        chart.update('none');
    }, 2600);

    // Redraw to keep pulse plugin alive even when data does not change.
    setInterval(() => {
        if (!document.hidden) {
            chart.draw();
        }
    }, 90);

    window.addEventListener('themeChanged', () => {
        applyRadarTheme(chart, ctx, canvas);
        chart.update();
    });
}

function applyRadarTheme(chart, ctx, canvas) {
    const styles = getComputedStyle(document.documentElement);
    const accent = readCssVar(styles, '--clr-accent', '#44A1A0');
    const highlight = readCssVar(styles, '--clr-highlight', '#DBA159');
    const textMain = readCssVar(styles, '--clr-text-main', '#073B4C');
    const textMuted = readCssVar(styles, '--clr-text-muted', '#5A7D8A');

    const profileGradient = ctx.createLinearGradient(0, 0, 0, canvas.clientHeight || 280);
    profileGradient.addColorStop(0, withAlpha(accent, 0.5));
    profileGradient.addColorStop(1, withAlpha(highlight, 0.14));

    chart.data.datasets[0].backgroundColor = profileGradient;
    chart.data.datasets[0].borderColor = accent;
    chart.data.datasets[0].pointBackgroundColor = '#fff';
    chart.data.datasets[0].pointBorderColor = accent;

    chart.data.datasets[1].borderColor = withAlpha(highlight, 0.95);

    chart.options.scales.r.grid.color = withAlpha(textMain, 0.12);
    chart.options.scales.r.angleLines.color = withAlpha(textMuted, 0.25);
    chart.options.scales.r.pointLabels.color = textMain;
    chart.options.scales.r.pointLabels.font.size = window.innerWidth < 768 ? 10 : 11;
    chart.options.scales.r.ticks.color = withAlpha(textMuted, 0.78);

    chart.options.plugins.legend.labels.color = textMuted;
}

function readCssVar(styles, variable, fallback) {
    const value = styles.getPropertyValue(variable).trim();
    return value || fallback;
}

function withAlpha(color, alpha) {
    if (!color) {
        return `rgba(7, 59, 76, ${alpha})`;
    }

    if (color.startsWith('#')) {
        let hex = color.slice(1);
        if (hex.length === 3) {
            hex = hex.split('').map(char => char + char).join('');
        }
        const numeric = parseInt(hex, 16);
        const red = (numeric >> 16) & 255;
        const green = (numeric >> 8) & 255;
        const blue = numeric & 255;
        return `rgba(${red}, ${green}, ${blue}, ${alpha})`;
    }

    const rgbMatch = color.match(/rgba?\(([^)]+)\)/);
    if (rgbMatch) {
        const channels = rgbMatch[1].split(',').slice(0, 3).map(part => Number(part.trim()));
        if (channels.length === 3) {
            return `rgba(${channels[0]}, ${channels[1]}, ${channels[2]}, ${alpha})`;
        }
    }

    return color;
}

function initLiveTempChart() {
    const canvas = document.getElementById('liveTempChart');
    if (!canvas || typeof Chart === 'undefined') return;
    const ctx = canvas.getContext('2d');

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

    const textEl = document.querySelector('.feed-value');
    const trendBox = document.getElementById('sensorTrendBox');
    const trendEl = document.getElementById('sensorTrendDelta');
    const humidityEl = document.getElementById('sensorHumidity');
    const doorEl = document.getElementById('sensorDoorState');
    const alertEl = document.getElementById('sensorAlertState');
    const pulseFill = document.getElementById('sensorPulseFill');
    const sensorCard = document.querySelector('.sensor-card');

    // Simulate Live Feed
    setInterval(() => {
        const lastVal = dataPoints[dataPoints.length - 1];
        const change = (Math.random() - 0.5) * 0.2;
        let newVal = lastVal + change;
        newVal = Math.max(3.5, Math.min(5.5, newVal)); // Clamp

        dataPoints.shift();
        dataPoints.push(newVal);

        if (textEl) textEl.textContent = newVal.toFixed(1) + '°C';
        if (trendEl) trendEl.textContent = `${change >= 0 ? '+' : ''}${change.toFixed(1)}°C`;
        if (trendBox) trendBox.classList.toggle('down', change < 0);

        if (humidityEl) {
            humidityEl.textContent = `${Math.round(58 + Math.random() * 10)}%`;
        }

        const doorOpen = Math.random() < 0.15;
        if (doorEl) {
            doorEl.textContent = doorOpen ? 'Opened' : 'Closed';
        }

        const isAlert = newVal > 5 || doorOpen;
        if (alertEl) {
            alertEl.textContent = isAlert ? 'Check' : 'None';
        }

        if (sensorCard) {
            sensorCard.classList.toggle('sensor-alert', isAlert);
        }

        if (pulseFill) {
            const level = Math.max(0, Math.min(100, ((newVal - 3.5) / 2) * 100));
            pulseFill.style.width = `${level}%`;
        }

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

function initCourseAccordion() {
    const courses = Array.from(document.querySelectorAll('.program-course'));
    if (!courses.length) return;

    courses.forEach(course => {
        const toggle = course.querySelector('.course-toggle');
        if (!toggle) return;

        const collapsedByDefault = !course.classList.contains('active');
        setCourseCollapsed(course, collapsedByDefault);

        toggle.addEventListener('click', () => {
            const isCollapsed = course.classList.contains('collapsed');
            setCourseCollapsed(course, !isCollapsed);
        });
    });
}

function initJourneySimulation() {
    const btn = document.getElementById('btnStartSim');
    updateJourneyProgress();
    if (!btn) return;

    btn.addEventListener('click', () => {
        btn.innerHTML = '<span class="material-icons spin" style="font-size:1rem; vertical-align:middle;">sync</span> Running Course Lab...';
        btn.disabled = true;

        setTimeout(() => {
            btn.innerHTML = '✔ Course Completed';
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline');
            btn.style.borderColor = 'green';
            btn.style.color = 'green';

            const activeCourse = btn.closest('.program-course');
            if (activeCourse) {
                activeCourse.classList.remove('active', 'locked');
                activeCourse.classList.add('completed');
                setCourseStatus(activeCourse, 'completed');
                markCourseModulesCompleted(activeCourse);
                setCourseCollapsed(activeCourse, false);

                const actionPanel = activeCourse.querySelector('.course-action-panel');
                if (actionPanel && !actionPanel.querySelector('.course-note')) {
                    const completionNote = document.createElement('small');
                    completionNote.className = 'course-note';
                    completionNote.innerText = 'Completed. Reflection was added to your learning log.';
                    actionPanel.appendChild(completionNote);
                }
            }

            const nextCourse = activeCourse ? activeCourse.nextElementSibling : null;
            if (nextCourse && nextCourse.classList.contains('program-course')) {
                nextCourse.classList.remove('locked');
                nextCourse.classList.add('active');
                setCourseStatus(nextCourse, 'active');
                unlockCourseModules(nextCourse);
                setCourseCollapsed(nextCourse, false);

                const note = nextCourse.querySelector('.course-note');
                if (note) {
                    note.innerText = 'Unlocked. Continue with Course 3.';
                }
            }

            updateJourneyProgress();
        }, 2000);
    });
}

function setCourseStatus(courseEl, state) {
    const statusEl = courseEl.querySelector('.course-status');
    if (!statusEl) return;

    statusEl.classList.remove('status-completed', 'status-active', 'status-locked');

    if (state === 'completed') {
        statusEl.classList.add('status-completed');
        statusEl.innerText = 'Completed';
        return;
    }

    if (state === 'active') {
        statusEl.classList.add('status-active');
        statusEl.innerText = 'In Progress';
        return;
    }

    statusEl.classList.add('status-locked');
    statusEl.innerText = 'Locked';
}

function updateJourneyProgress() {
    const courses = Array.from(document.querySelectorAll('.program-course'));
    if (!courses.length) return;

    const completedCount = courses.filter(course => course.classList.contains('completed')).length;
    const percent = Math.round((completedCount / courses.length) * 100);

    const progressBar = document.getElementById('journeyProgressBar');
    if (progressBar) {
        progressBar.style.width = `${percent}%`;
    }

    const progressLabel = document.getElementById('journeyProgressLabel');
    if (progressLabel) {
        progressLabel.innerText = `${percent}%`;
    }
}

function setCourseCollapsed(courseEl, collapsed) {
    courseEl.classList.toggle('collapsed', collapsed);
    const toggle = courseEl.querySelector('.course-toggle');
    if (!toggle) return;

    toggle.setAttribute('aria-expanded', String(!collapsed));
    const icon = toggle.querySelector('.material-icons');
    if (icon) {
        icon.innerText = collapsed ? 'expand_more' : 'expand_less';
    }
}

function markCourseModulesCompleted(courseEl) {
    const modules = courseEl.querySelectorAll('.course-modules li');
    modules.forEach(module => {
        module.classList.remove('module-current', 'module-pending', 'module-locked');
        module.classList.add('module-done');

        const icon = module.querySelector('.material-icons');
        if (icon) {
            icon.innerText = 'check_circle';
        }
    });
}

function unlockCourseModules(courseEl) {
    const modules = courseEl.querySelectorAll('.course-modules li');
    modules.forEach(module => {
        if (!module.classList.contains('module-locked')) return;
        module.classList.remove('module-locked');
        module.classList.add('module-pending');

        const icon = module.querySelector('.material-icons');
        if (icon) {
            icon.innerText = 'radio_button_unchecked';
        }
    });
}
