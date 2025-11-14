import './bootstrap';

import 'flowbite';


let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}



// Dropdown functionality
document.addEventListener('DOMContentLoaded', () => {
    const dropdowns = document.querySelectorAll('[data-dropdown]');

    dropdowns.forEach(dropdown => {
        const trigger = dropdown.querySelector('[data-dropdown-trigger]');
        const menu = dropdown.querySelector('[data-dropdown-menu]');

        trigger.addEventListener('click', () => {
            const isVisible = menu.style.display === 'block';
            menu.style.display = isVisible ? 'none' : 'block';
        });

        document.addEventListener('click', (event) => {
            if (!dropdown.contains(event.target)) {
                menu.style.display = 'none';
            }
        });
    });
});
