import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

function updateThemeControls(isDark) {
    document.querySelectorAll('[data-theme-icon]').forEach((icon) => {
        icon.className = isDark
            ? 'fas fa-sun text-amber-400'
            : 'fas fa-moon text-orange-600 dark:text-orange-300';
    });

    document.querySelectorAll('[data-theme-label]').forEach((label) => {
        label.textContent = isDark ? 'Light' : 'Dark';
    });
}

function applyThemePreference() {
    const savedTheme = localStorage.getItem('dark-mode');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const isDark = savedTheme === 'dark' || (!savedTheme && prefersDark);

    document.documentElement.classList.toggle('dark', isDark);
    updateThemeControls(isDark);
}

window.toggleDarkMode = function toggleDarkMode() {
    const isDark = document.documentElement.classList.toggle('dark');

    localStorage.setItem('dark-mode', isDark ? 'dark' : 'light');
    updateThemeControls(isDark);
};

document.addEventListener('DOMContentLoaded', applyThemePreference);
applyThemePreference();

Alpine.start();
