// === Your JS (with minor addition for vertical green line) ===
const SIDEBAR_STATE_KEY = 'sidebarState';
const ACTIVE_ACCORDION_KEY = 'activeAccordion';

function updateToggleIcon(isSidebarCollapsed) {
    const toggleIcon = document.getElementById('toggleSidebar').querySelector('i');
    toggleIcon.className = isSidebarCollapsed ? 'fas fa-times' : 'fas fa-bars';
}

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    let isSidebarCollapsed;

    if (window.innerWidth <= 768) {
        sidebar.classList.toggle('open');
        isSidebarCollapsed = !sidebar.classList.contains('open');
    } else {
        sidebar.classList.toggle('closed');
        mainContent.classList.toggle('expanded');
        isSidebarCollapsed = sidebar.classList.contains('closed');
    }

    updateToggleIcon(isSidebarCollapsed);
    localStorage.setItem(SIDEBAR_STATE_KEY, isSidebarCollapsed ? 'collapsed' : 'expanded');
}

function initializeSidebarState() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const savedState = localStorage.getItem(SIDEBAR_STATE_KEY);
    let isSidebarCollapsed = savedState === 'collapsed' || (window.innerWidth <= 768);
    if (window.innerWidth <= 768) sidebar.classList.toggle('open', !isSidebarCollapsed);
    else {
        sidebar.classList.toggle('closed', isSidebarCollapsed);
        mainContent.classList.toggle('expanded', isSidebarCollapsed);
    }
    updateToggleIcon(isSidebarCollapsed);
}

document.addEventListener('DOMContentLoaded', () => {
    initializeSidebarState();
    const savedAccordion = localStorage.getItem(ACTIVE_ACCORDION_KEY);
    if (savedAccordion) openAccordion(savedAccordion);
});

document.getElementById('toggleSidebar').addEventListener('click', toggleSidebar);

const accordions = document.querySelectorAll('.accordion');

function closeAllAccordions() {
    accordions.forEach(acc => {
        acc.querySelector('.accordion-content').classList.remove('show');
        acc.querySelector('.fa-chevron-down').classList.remove('fa-rotate-180');
        acc.classList.remove('active-accordion'); // remove green line class
    });
}

function openAccordion(id) {
    closeAllAccordions();
    const acc = document.querySelector(`[data-accordion-id="${id}"]`);
    if (acc) {
        acc.querySelector('.accordion-content').classList.add('show');
        acc.querySelector('.fa-chevron-down').classList.add('fa-rotate-180');
        acc.classList.add('active-accordion'); // add green line class
        localStorage.setItem(ACTIVE_ACCORDION_KEY, id);
    }
}

accordions.forEach(acc => {
    const header = acc.querySelector('.accordion-header');
    const id = acc.getAttribute('data-accordion-id');
    header.addEventListener('click', () => {
        const content = acc.querySelector('.accordion-content');
        if (content.classList.contains('show')) {
            closeAllAccordions();
            localStorage.removeItem(ACTIVE_ACCORDION_KEY);
        } else openAccordion(id);
    });
});

document.getElementById('dashboardLink').addEventListener('click', e => {
    // e.preventDefault();
    closeAllAccordions();
    localStorage.removeItem(ACTIVE_ACCORDION_KEY);
    
});

window.addEventListener('resize', initializeSidebarState);
