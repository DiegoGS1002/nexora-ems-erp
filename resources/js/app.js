import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import './pwa.js';

/* ══════════════════════════════════════════════
   NEXORA ERP — Sidebar Interactions
   ══════════════════════════════════════════════ */

// ── Constantes ──
const STORAGE_KEY = 'nx_sidebar_collapsed';

// ── Elementos ──
function getSidebar()  { return document.getElementById('nx-sidebar'); }
function getWrapper()  { return document.getElementById('nx-app-wrapper'); }
function getOverlay()  { return document.getElementById('nx-overlay'); }

// ── Toggle colapso (desktop) ──
window.nxSidebarToggle = function () {
    const sb   = getSidebar();
    const wrap = getWrapper();
    if (!sb) return;

    const isCollapsed = sb.classList.toggle('collapsed');
    wrap?.classList.toggle('sidebar-collapsed', isCollapsed);
    localStorage.setItem(STORAGE_KEY, isCollapsed ? '1' : '0');
};

// ── Abrir (mobile) ──
window.nxSidebarOpen = function () {
    getSidebar()?.classList.add('mobile-open');
    getOverlay()?.classList.add('active');
    document.body.style.overflow = 'hidden';
};

// ── Fechar (mobile) ──
window.nxSidebarClose = function () {
    getSidebar()?.classList.remove('mobile-open');
    getOverlay()?.classList.remove('active');
    document.body.style.overflow = '';
    closeAllDropdowns();
};

// ── Fechar ao pressionar Escape ──
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        nxSidebarClose();
        closeAllDropdowns();
    }
});

// ══════════════════════════════════════════════
// DROPDOWN (menu de perfil)
// ══════════════════════════════════════════════
window.toggleDropdown = function (event) {
    event.preventDefault();
    event.stopPropagation();

    const trigger    = event.currentTarget;
    const dropdownId = trigger.getAttribute('data-dropdown');
    const menu       = document.getElementById('dropdown-' + dropdownId);
    if (!menu) return;

    const isOpen = menu.classList.contains('open');
    closeAllDropdowns();

    if (!isOpen) {
        menu.classList.add('open');
        trigger.closest('.nx-has-dropdown')?.classList.add('open');
    }
};

function closeAllDropdowns() {
    document.querySelectorAll('.nx-dropdown.open').forEach(m => m.classList.remove('open'));
    document.querySelectorAll('.nx-has-dropdown.open').forEach(el => el.classList.remove('open'));
}

// Fechar ao clicar fora
document.addEventListener('click', function (e) {
    if (!e.target.closest('.nx-has-dropdown')) closeAllDropdowns();
});

// ══════════════════════════════════════════════
// INICIALIZAÇÃO
// ══════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', function () {

    // Restaurar estado do sidebar
    const sb   = getSidebar();
    const wrap = getWrapper();
    if (sb && wrap && localStorage.getItem(STORAGE_KEY) === '1') {
        sb.classList.add('collapsed');
        wrap.classList.add('sidebar-collapsed');
    }

    // Atribuir dropdowns de trigger
    document.querySelectorAll('.nx-dropdown-trigger').forEach(trigger => {
        trigger.addEventListener('click', toggleDropdown);
    });

    // Marcar link ativo
    const currentPath = window.location.pathname;
    document.querySelectorAll('.nx-sb-link').forEach(link => {
        if (link.getAttribute('href') && link.getAttribute('href') !== '#') {
            try {
                const url = new URL(link.href);
                if (url.pathname === currentPath) {
                    link.classList.add('nx-sb-active');
                }
            } catch (_) {}
        }
    });

    // Auto-dismiss alertas
    const alerts = document.querySelectorAll('.alert-success, .alert-error');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            alert.style.opacity    = '0';
            alert.style.transform  = 'translateX(20px)';
            setTimeout(() => alert.remove(), 400);
        }, 4000);
    });
});
