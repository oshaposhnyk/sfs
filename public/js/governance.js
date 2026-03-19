/**
 * Governance.js - Dashboard Logic
 */

document.addEventListener('DOMContentLoaded', () => {
    startKpiSimulation();
    initResourceLibrary();
    initManagementTools();
    initReportDownload();
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

// --- Resource Library Logic ---

const resources = [
    { title: "L4C Learning Program Strategy", role: "Director", date: "Jan 10, 2026", action: "Download", icon: "description", type: "pdf" },
    { title: "Cold Chain Report Q1", role: "Tech", date: "Jan 09, 2026", action: "Download", icon: "bar_chart", type: "pdf" },
    { title: "Staff Training: Hygiene", role: "All", date: "Jan 05, 2026", action: "View", icon: "movie", type: "video" },
    { title: "Supplier Code of Conduct", role: "Supplier", date: "Jan 04, 2026", action: "Download", icon: "gavel", type: "pdf" },
    { title: "Digital Twin Specs v2.0", role: "Tech", date: "Jan 02, 2026", action: "Download", icon: "settings", type: "pdf" },
    { title: "Q4 Financial Overview", role: "Director", date: "Dec 28, 2025", action: "Download", icon: "pie_chart", type: "xls" },
    { title: "Community Feedback Summary", role: "All", date: "Dec 25, 2025", action: "View", icon: "groups", type: "doc" },
    { title: "Safety Protocol Update", role: "Director", date: "Dec 20, 2025", action: "Download", icon: "shield", type: "pdf" },
    { title: "New Ingredient Vendor List", role: "Supplier", date: "Dec 18, 2025", action: "View", icon: "list_alt", type: "doc" },
    { title: "IoT Sensor Calibration Log", role: "Tech", date: "Dec 15, 2025", action: "Download", icon: "build", type: "csv" },
    { title: "Marketing Campaign 2026", role: "Director", date: "Dec 10, 2025", action: "View", icon: "campaign", type: "ppt" },
    { title: "Waste Management Audit", role: "Supplier", date: "Dec 05, 2025", action: "Download", icon: "delete_outline", type: "pdf" },
    { title: "Energy Consumption Report", role: "Tech", date: "Dec 01, 2025", action: "Download", icon: "bolt", type: "pdf" },
    { title: "Employee Handbook 2026", role: "All", date: "Nov 28, 2025", action: "Download", icon: "menu_book", type: "pdf" },
    { title: "Crisis Management Plan", role: "Director", date: "Nov 20, 2025", action: "Download", icon: "warning", type: "doc" },
    { title: "Supplier Onboarding Kit", role: "Supplier", date: "Nov 15, 2025", action: "Download", icon: "inventory_2", type: "zip" },
    { title: "Server Maintenance Schedule", role: "Tech", date: "Nov 10, 2025", action: "View", icon: "schedule", type: "doc" },
    { title: "Customer Satisfaction Metrics", role: "Director", date: "Nov 05, 2025", action: "View", icon: "thumb_up", type: "ppt" },
    { title: "Organic Certification", role: "Supplier", date: "Oct 30, 2025", action: "Download", icon: "verified", type: "pdf" },
    { title: "Fire Safety Drill Log", role: "All", date: "Oct 25, 2025", action: "View", icon: "local_fire_department", type: "doc" },
    { title: "IT Security Policy", role: "Tech", date: "Oct 20, 2025", action: "Download", icon: "security", type: "pdf" },
    { title: "Budget Proposal 2026", role: "Director", date: "Oct 15, 2025", action: "Download", icon: "attach_money", type: "xls" },
    { title: "Logistics Partner Contract", role: "Supplier", date: "Oct 10, 2025", action: "Download", icon: "local_shipping", type: "pdf" },
    { title: "System Architecture Diagram", role: "Tech", date: "Oct 05, 2025", action: "View", icon: "account_tree", type: "img" }
];

let currentFilter = 'All';
let isExpanded = false;

function initResourceLibrary() {
    const tableBody = document.getElementById('resourceTableBody');
    const toggleBtn = document.getElementById('btnViewAllResources');
    const filterContainer = document.getElementById('resourceFilters');

    if (!tableBody || !toggleBtn || !filterContainer) return;

    // Filter Click Logic
    const filters = filterContainer.querySelectorAll('.badge');
    filters.forEach(badge => {
        badge.addEventListener('click', () => {
            filters.forEach(b => b.classList.remove('badge-accent'));
            badge.classList.add('badge-accent');

            currentFilter = badge.getAttribute('data-filter');
            isExpanded = false; // Reset expansion
            renderResources();
            updateToggleButton();
        });
    });

    // Toggle Button Logic
    toggleBtn.addEventListener('click', (e) => {
        e.preventDefault();
        isExpanded = !isExpanded;
        renderResources();
        updateToggleButton();
    });

    // Initial Render
    renderResources();
}

function renderResources() {
    const tableBody = document.getElementById('resourceTableBody');
    if (!tableBody) return;

    tableBody.innerHTML = '';

    const filtered = resources.filter(r => currentFilter === 'All' || r.role === currentFilter);
    const limit = isExpanded ? filtered.length : 3;
    const toShow = filtered.slice(0, limit);

    toShow.forEach(item => {
        const tr = document.createElement('tr');
        tr.style.borderBottom = '1px solid var(--clr-surface-alt)';

        let badgeClass = 'badge';
        if (item.role === 'Tech') badgeClass = 'badge badge-accent';
        if (item.role === 'All') badgeClass = 'badge badge-highlight';

        tr.innerHTML = `
            <td style="padding:15px 10px; display:flex; align-items:center; gap:8px;">
                <span class="material-icons" style="font-size:1.2rem; color:var(--clr-text-muted);">${item.icon}</span> 
                ${item.title}
            </td>
            <td><span class="${badgeClass}">${item.role}</span></td>
            <td>${item.date}</td>
            <td><a href="#" class="resource-action" style="color:var(--clr-accent); font-weight:600;">${item.action}</a></td>
        `;
        tr.classList.add('fade-in');

        // Add click listener to the action link
        const actionLink = tr.querySelector('.resource-action');
        actionLink.addEventListener('click', (e) => {
            e.preventDefault();
            handleResourceAction(item.action, item.title, actionLink);
        });

        tableBody.appendChild(tr);
    });
}

function handleResourceAction(action, title, element) {
    if (action === 'Download') {
        const originalText = element.innerText;
        element.innerText = 'Downloading...';
        element.style.pointerEvents = 'none';

        setTimeout(() => {
            element.innerText = 'Saved ✔';

            // Simulating download behavior (file won't actually exist)
            const link = document.createElement('a');
            link.href = '#';
            link.download = title.replace(/\s/g, '_') + '.pdf';
            // link.click(); 

            alert(`Report '${title}' downloaded successfully!`);

            setTimeout(() => {
                element.innerText = originalText;
                element.style.pointerEvents = 'auto';
            }, 2000);
        }, 1000);

    } else if (action === 'View') {
        showManagementModal(title); // Reuse generic modal
    }
}

function updateToggleButton() {
    const toggleBtn = document.getElementById('btnViewAllResources');
    const filteredCount = resources.filter(r => currentFilter === 'All' || r.role === currentFilter).length;

    if (isExpanded) {
        toggleBtn.innerText = 'Show Less ↑';
    } else {
        toggleBtn.innerText = `View all ${filteredCount} documents ↓`;
    }
}

// --- Management Tools Logic ---

function initManagementTools() {
    const toolCards = document.querySelectorAll('.management-card');
    toolCards.forEach(card => {
        card.addEventListener('click', () => {
            const titleElement = card.querySelector('h4');
            if (titleElement) {
                const title = titleElement.innerText;
                showManagementModal(title);
            }
        });
    });
}

function showManagementModal(title) {
    let modal = document.getElementById('managementModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'managementModal';
        modal.className = 'modal-overlay';
        modal.innerHTML = `
            <div class="modal-card" style="max-width:500px;">
                <div class="section-header" style="margin-bottom:15px; display:flex; justify-content:space-between;">
                    <h3 id="mgmtModalTitle">Title</h3>
                    <button class="btn btn-outline" onclick="closeManagementModal()" style="padding:5px 10px; border:none; font-size:1.2rem;">&times;</button>
                </div>
                <div id="mgmtModalContent">
                    <!-- Dynamic Content -->
                </div>
                <button class="btn btn-primary" onclick="closeManagementModal()" style="width:100%; margin-top:20px;">Close</button>
            </div>
        `;
        document.body.appendChild(modal);
    }

    const titleEl = document.getElementById('mgmtModalTitle');
    const contentEl = document.getElementById('mgmtModalContent');
    titleEl.innerText = title;

    if (title.includes('Protocols')) {
        contentEl.innerHTML = `
            <p><strong>Safety Standard #44-B: Active</strong></p>
            <ul style="text-align:left; margin-bottom:15px; padding-left:20px;">
                <li>Hand washing interval: 30 mins</li>
                <li>Surface sanitation grade: A+</li>
                <li>Mask mandate: Optional (Zone Green)</li>
            </ul>
            <div class="badge badge-accent">Compliance: 98%</div>
        `;
    } else if (title.includes('Tech Check')) {
        contentEl.innerHTML = `
           <p><strong>Digital Twin Status: Online</strong></p>
           <p>Sync Latency: 45ms</p>
           <div style="background:var(--clr-surface-alt); padding:10px; border-radius:5px;">
                <span style="color:green;">●</span> All sensors operational
           </div>
        `;
    } else if (title.includes('Surveys')) {
        contentEl.innerHTML = `
           <p><strong>Latest Poll: School Lunch Quality</strong></p>
           <p>Votes: 452</p>
           <div style="height:10px; background:var(--clr-surface-alt); border-radius:5px; overflow:hidden; margin:10px 0;">
                <div style="width:75%; height:100%; background:var(--clr-highlight);"></div>
           </div>
           <small>75% Positive</small>
        `;
    } else if (title.includes('Legal')) {
        contentEl.innerHTML = `
           <p><strong>Smart Contracts Active: 3</strong></p>
           <ul style="text-align:left;">
                <li>Supplier A (Milk): Auto-Payment Enabled</li>
                <li>Supplier B (Bread): Review Pending</li>
           </ul>
        `;
    } else {
        // Generic content for View actions or unknown titles
        contentEl.innerHTML = `
            <p><strong>Content Preview</strong></p>
            <p>This is a simulated preview of the document "${title}".</p>
            <div style="background:var(--clr-surface-alt); padding:20px; text-align:center; margin-top:10px;">
                [Document Content Placeholder]
            </div>
         `;
    }

    setTimeout(() => modal.classList.add('active'), 10);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeManagementModal();
    });
}

window.closeManagementModal = function () {
    const modal = document.getElementById('managementModal');
    if (modal) modal.classList.remove('active');
}

// --- Report Download Logic ---

function initReportDownload() {
    const btn = document.getElementById('btnDownloadReport');
    if (!btn) return;

    btn.addEventListener('click', () => {
        const originalText = btn.innerText;
        btn.innerHTML = '<span class="material-icons spin" style="font-size:1rem; vertical-align:middle;">sync</span> Generating...';
        btn.disabled = true;

        setTimeout(() => {
            btn.innerHTML = '✔ Downloaded';
            btn.classList.add('btn-accent');

            // Simulated fake download effect
            const link = document.createElement('a');
            link.href = '#';
            link.download = 'Governance_Report_2026.pdf';
            // link.click(); // Avoid actual download behavior to keep it clean in preview

            alert("Report 'Governance_Report_2026.pdf' downloaded successfully!");

            setTimeout(() => {
                btn.innerText = originalText;
                btn.disabled = false;
                btn.classList.remove('btn-accent');
            }, 2000);
        }, 1500);
    });
}
