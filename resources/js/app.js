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

async function activeServiceWorkerRegistration() {
    if (!('serviceWorker' in navigator)) {
        return null;
    }

    return await navigator.serviceWorker.ready.catch(() => null);
}

function permissionLabel(permission) {
    return {
        granted: 'Enabled',
        denied: 'Blocked',
        default: 'Not enabled',
    }[permission] || 'Unknown';
}

function setNotificationSetupMessage(panel, message, type = 'info') {
    const messageElement = panel.querySelector('[data-notification-message]');

    if (!messageElement) {
        return;
    }

    messageElement.textContent = message;
    messageElement.className = [
        'mt-4 rounded-lg border px-3 py-2 text-xs font-semibold leading-5',
        type === 'error'
            ? 'border-red-200 bg-red-50 text-red-800 dark:border-red-900 dark:bg-red-950/35 dark:text-red-200'
            : 'border-orange-200 bg-orange-50 text-orange-800 dark:border-orange-900 dark:bg-orange-950/35 dark:text-orange-200',
    ].join(' ');
}

async function refreshNotificationSetup(panel) {
    const supportElement = panel.querySelector('[data-notification-support]');
    const permissionElement = panel.querySelector('[data-notification-permission]');
    const workerElement = panel.querySelector('[data-notification-worker]');
    const enableButton = panel.querySelector('[data-notification-enable]');
    const testButton = panel.querySelector('[data-notification-test]');
    const supported = 'Notification' in window;
    const registration = await activeServiceWorkerRegistration();

    if (supportElement) {
        supportElement.textContent = supported ? 'Supported' : 'Not supported';
    }

    if (permissionElement) {
        permissionElement.textContent = supported ? permissionLabel(Notification.permission) : 'Unavailable';
    }

    if (workerElement) {
        workerElement.textContent = registration ? 'Ready' : 'Unavailable';
    }

    if (enableButton) {
        enableButton.disabled = !supported || Notification.permission === 'granted' || Notification.permission === 'denied';
    }

    if (testButton) {
        testButton.disabled = !supported || Notification.permission !== 'granted';
    }

    if (!supported) {
        setNotificationSetupMessage(panel, 'This browser does not support device notifications. You can still use in-app and email notifications.', 'error');
    } else if (Notification.permission === 'denied') {
        setNotificationSetupMessage(panel, 'Notifications are blocked for this browser. Enable them from your browser site settings to receive device alerts.', 'error');
    } else if (Notification.permission === 'granted') {
        setNotificationSetupMessage(panel, 'Device alerts are enabled on this browser. Email and privacy preferences are still controlled by your account settings.');
    }
}

async function showTestNotification() {
    const registration = await activeServiceWorkerRegistration();
    const options = {
        body: 'Your Akatsuki Devs notification setup is working.',
        icon: '/icons/icon-192.png',
        badge: '/icons/icon-192.png',
        data: { url: '/notifications' },
        tag: 'akatsuki-test-notification',
    };

    if (registration?.showNotification) {
        await registration.showNotification('Village signal received', options);
        return;
    }

    new Notification('Village signal received', options);
}

function setupBrowserNotifications() {
    document.querySelectorAll('[data-notification-setup]').forEach(async (panel) => {
        const enableButton = panel.querySelector('[data-notification-enable]');
        const testButton = panel.querySelector('[data-notification-test]');

        await refreshNotificationSetup(panel);

        enableButton?.addEventListener('click', async () => {
            if (!('Notification' in window)) {
                await refreshNotificationSetup(panel);
                return;
            }

            markActionAsBusy(enableButton, 'Enabling...');
            const permission = await Notification.requestPermission();
            restoreBusyAction(enableButton);

            if (permission === 'granted') {
                localStorage.setItem('akatsuki-device-notifications', 'enabled');
                await showTestNotification();
            }

            await refreshNotificationSetup(panel);
        });

        testButton?.addEventListener('click', async () => {
            if (!('Notification' in window) || Notification.permission !== 'granted') {
                await refreshNotificationSetup(panel);
                return;
            }

            markActionAsBusy(testButton, 'Sending...');
            await showTestNotification();
            restoreBusyAction(testButton);
            setNotificationSetupMessage(panel, 'Test notification sent. If you did not see it, check your browser or OS notification settings.');
        });
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
document.addEventListener('DOMContentLoaded', setupBrowserNotifications);
registerServiceWorker();
applyThemePreference();

Alpine.start();
