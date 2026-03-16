import 'bootstrap/dist/js/bootstrap.bundle.min.js';

window.toggleDropdown = function(event) {
    event.preventDefault();
    const link = event.target;
    const dropdownId = link.getAttribute('data-dropdown');
    const menu = document.getElementById('dropdown-' + dropdownId);

    // Fechar outros dropdowns
    document.querySelectorAll('[id^="dropdown-"]').forEach(m => {
        if (m.id !== menu.id) {
            m.style.display = 'none';
        }
    });

    // Toggle do dropdown atual
    menu.style.display = menu.style.display === 'none' ? 'flex' : 'none';
}

document.addEventListener('click', function(event) {
    // Fechar dropdowns ao clicar fora
    if (!event.target.closest('[data-dropdown]') && !event.target.closest('[id^="dropdown-"]')) {
        document.querySelectorAll('[id^="dropdown-"]').forEach(menu => {
            menu.style.display = 'none';
        });
    }
});

