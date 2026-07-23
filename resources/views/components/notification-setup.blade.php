@props([
    'compact' => false,
])

<x-ui.card
    {{ $attributes->merge(['class' => $compact ? 'p-4' : 'p-5 sm:p-6']) }}
    data-notification-setup
    data-public-key-url="{{ route('push.public-key') }}"
    data-subscribe-url="{{ route('push.subscriptions.store') }}"
    data-unsubscribe-url="{{ route('push.subscriptions.destroy') }}"
>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <p class="rank-badge"><i class="fas fa-satellite-dish"></i> Notification setup</p>
            <h2 class="mt-3 text-xl font-black tracking-normal text-slate-950 dark:text-white">Device Alerts</h2>
            <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">
                Enable browser notifications for this device so Akatsuki Devs can show local alerts while you are using the PWA.
            </p>
        </div>

        <div class="flex shrink-0 flex-wrap gap-2">
            <button type="button" class="ui-btn ui-btn-primary min-h-10 px-3" data-notification-enable>
                <i class="fas fa-bell"></i>
                Subscribe
            </button>
            <button type="button" class="ui-btn ui-btn-secondary min-h-10 px-3" data-notification-disable>
                <i class="fas fa-bell-slash"></i>
                Unsubscribe
            </button>
            <button type="button" class="ui-btn ui-btn-secondary min-h-10 px-3" data-notification-test>
                <i class="fas fa-paper-plane"></i>
                Test
            </button>
        </div>
    </div>

    <div class="mt-4 grid gap-3 sm:grid-cols-3">
        <div class="rounded-lg border border-slate-200 bg-white/72 p-3 dark:border-slate-800 dark:bg-slate-900/72">
            <p class="text-xs font-black uppercase tracking-wide text-slate-400">Browser</p>
            <p class="mt-1 text-sm font-black text-slate-950 dark:text-white" data-notification-support>Checking</p>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white/72 p-3 dark:border-slate-800 dark:bg-slate-900/72">
            <p class="text-xs font-black uppercase tracking-wide text-slate-400">Permission</p>
            <p class="mt-1 text-sm font-black text-slate-950 dark:text-white" data-notification-permission>Checking</p>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white/72 p-3 dark:border-slate-800 dark:bg-slate-900/72">
            <p class="text-xs font-black uppercase tracking-wide text-slate-400">Service Worker</p>
            <p class="mt-1 text-sm font-black text-slate-950 dark:text-white" data-notification-worker>Checking</p>
        </div>
        <div class="rounded-lg border border-slate-200 bg-white/72 p-3 dark:border-slate-800 dark:bg-slate-900/72 sm:col-span-3">
            <p class="text-xs font-black uppercase tracking-wide text-slate-400">This Device</p>
            <p class="mt-1 text-sm font-black text-slate-950 dark:text-white" data-notification-subscription>Checking</p>
        </div>
    </div>

    <p class="mt-4 rounded-lg border border-orange-200 bg-orange-50 px-3 py-2 text-xs font-semibold leading-5 text-orange-800 dark:border-orange-900 dark:bg-orange-950/35 dark:text-orange-200" data-notification-message>
        Browser push is controlled per device. Subscribe each phone or browser where you want alerts.
    </p>
</x-ui.card>
