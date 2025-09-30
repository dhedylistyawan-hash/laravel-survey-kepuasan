import './bootstrap';

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
window.Swal = Swal;

window.Alpine = Alpine;

Alpine.start();

// Dark mode toggle logic
window.setDarkMode = function(enabled) {
    const root = document.getElementById('app-root');
    if (!root) return;
    if (enabled) {
        root.classList.add('dark');
        localStorage.setItem('darkMode', 'enabled');
        const moon = document.getElementById('icon-moon');
        const sun = document.getElementById('icon-sun');
        if (moon && sun) {
            moon.style.display = 'none';
            sun.style.display = 'block';
        }
    } else {
        root.classList.remove('dark');
        localStorage.setItem('darkMode', 'disabled');
        const moon = document.getElementById('icon-moon');
        const sun = document.getElementById('icon-sun');
        if (moon && sun) {
            moon.style.display = 'block';
            sun.style.display = 'none';
        }
    }
};

document.addEventListener('DOMContentLoaded', function() {
    const darkPref = localStorage.getItem('darkMode');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    window.setDarkMode(darkPref === 'enabled' || (!darkPref && prefersDark));
    const toggle = document.getElementById('dark-toggle');
    if (toggle) {
        toggle.addEventListener('click', function() {
            const isDark = document.getElementById('app-root').classList.contains('dark');
            window.setDarkMode(!isDark);
        });
    }
});
