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

function markActionAsBusy(element, label = 'Please wait...') {
    if (!element || element.dataset.disableOnClick === 'false') {
        return;
    }

    element.dataset.busy = 'true';
    element.setAttribute('aria-busy', 'true');
    element.classList.add('pointer-events-none', 'opacity-60', 'cursor-not-allowed');

    if ('disabled' in element) {
        element.disabled = true;
    } else {
        element.setAttribute('aria-disabled', 'true');
    }

    if (element.matches('.ui-btn') && !element.dataset.originalHtml) {
        element.dataset.originalHtml = element.innerHTML;
        element.innerHTML = `<i class="fas fa-spinner fa-spin"></i><span>${label}</span>`;
    }
}

function restoreBusyAction(element) {
    if (!element) {
        return;
    }

    element.dataset.busy = 'false';
    element.removeAttribute('aria-busy');
    element.classList.remove('pointer-events-none', 'opacity-60', 'cursor-not-allowed');

    if ('disabled' in element) {
        element.disabled = false;
    } else {
        element.removeAttribute('aria-disabled');
    }

    if (element.dataset.originalHtml) {
        element.innerHTML = element.dataset.originalHtml;
        delete element.dataset.originalHtml;
    }
}

function setupRequestGuards() {
    document.addEventListener('submit', (event) => {
        const form = event.target;

        if (!(form instanceof HTMLFormElement) || form.dataset.disableOnSubmit === 'false') {
            return;
        }

        if (form.dataset.submitting === 'true') {
            event.preventDefault();
            return;
        }

        const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"], .ui-btn[type="submit"]');
        form.dataset.submitting = 'true';
        submitButtons.forEach((button) => {
            markActionAsBusy(button);
        });

        requestAnimationFrame(() => {
            if (event.defaultPrevented) {
                form.dataset.submitting = 'false';
                submitButtons.forEach((button) => restoreBusyAction(button));
            }
        });
    }, true);

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a.ui-btn, a[data-disable-on-click="true"]');

        if (!link || link.dataset.disableOnClick === 'false') {
            return;
        }

        const href = link.getAttribute('href') || '';
        const isExternalTarget = link.target && link.target !== '_self';
        const isNonNavigation = href === '' || href.startsWith('#') || href.startsWith('javascript:') || link.hasAttribute('download');

        if (isExternalTarget || isNonNavigation) {
            return;
        }

        if (link.dataset.busy === 'true') {
            event.preventDefault();
            return;
        }

        markActionAsBusy(link, 'Opening...');
    }, true);
}

function setupPwaInstallPrompt() {
    const promptCard = document.getElementById('pwaInstallPrompt');
    const installButton = document.getElementById('pwaInstallButton');
    const dismissButton = document.getElementById('pwaDismissButton');

    if (!promptCard || !installButton || !dismissButton) {
        return;
    }

    let deferredPrompt = null;
    const dismissedAt = Number(localStorage.getItem('pwa-install-dismissed-at') || 0);
    const dismissedRecently = dismissedAt && Date.now() - dismissedAt < 1000 * 60 * 60 * 24 * 7;

    window.addEventListener('beforeinstallprompt', (event) => {
        event.preventDefault();
        deferredPrompt = event;

        if (!dismissedRecently) {
            promptCard.classList.add('is-visible');
        }
    });

    installButton.addEventListener('click', async () => {
        if (!deferredPrompt) {
            promptCard.classList.remove('is-visible');
            return;
        }

        markActionAsBusy(installButton, 'Installing...');
        deferredPrompt.prompt();
        await deferredPrompt.userChoice;
        deferredPrompt = null;
        promptCard.classList.remove('is-visible');
        restoreBusyAction(installButton);
    });

    dismissButton.addEventListener('click', () => {
        localStorage.setItem('pwa-install-dismissed-at', String(Date.now()));
        promptCard.classList.remove('is-visible');
    });

    window.addEventListener('appinstalled', () => {
        deferredPrompt = null;
        promptCard.classList.remove('is-visible');
        localStorage.setItem('pwa-installed', 'true');
    });
}

function registerServiceWorker() {
    if (!('serviceWorker' in navigator)) {
        return;
    }

    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch((error) => {
            console.warn('Service worker registration failed:', error);
        });
    });
}

document.addEventListener('DOMContentLoaded', applyThemePreference);
document.addEventListener('DOMContentLoaded', setupRequestGuards);
document.addEventListener('DOMContentLoaded', setupPwaInstallPrompt);
registerServiceWorker();
applyThemePreference();

Alpine.start();
