@extends('layouts.app')

@section('title', 'Messages - Akatsuki Devs')

@php
    $initialUserId = request('user');
    $reverbClientConfig = [
        'key' => config('broadcasting.connections.reverb.key'),
        'host' => config('broadcasting.connections.reverb.options.host'),
        'port' => (int) config('broadcasting.connections.reverb.options.port', 8080),
        'scheme' => config('broadcasting.connections.reverb.options.scheme', 'http'),
    ];
@endphp

@section('content')
<x-ui.page width="max-w-7xl">
    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <span class="rank-badge"><i class="fas fa-comments"></i> Squad channel</span>
            <h1 class="mt-3 text-3xl font-black tracking-normal text-slate-950 dark:text-white">Messages</h1>
            <p class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400">Chat privately with accepted allies only.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <div id="realtimeStatus" class="inline-flex h-11 items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 text-xs font-black text-slate-600 shadow-sm dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300">
                <span id="realtimeStatusDot" class="h-2.5 w-2.5 rounded-full bg-slate-400"></span>
                <span id="realtimeStatusText">Checking realtime</span>
            </div>
            <x-ui.button :href="route('friends.list')" variant="secondary">
                <i class="fas fa-user-group"></i>
                Manage Allies
            </x-ui.button>
        </div>
    </div>

    <div id="chatShell" class="grid min-h-[calc(100vh-250px)] gap-4 lg:grid-cols-[360px_1fr]">
        <aside id="usersSidebar" class="ui-card flex min-h-[440px] flex-col overflow-hidden">
            <div class="border-b border-slate-200 p-4 dark:border-slate-800">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="font-black tracking-normal text-slate-950 dark:text-white">Conversations</h2>
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $users->count() }} allies available</p>
                    </div>
                    <button id="closeUsersSidebar" type="button" class="grid h-9 w-9 place-items-center rounded-lg border border-slate-200 text-slate-500 lg:hidden dark:border-slate-800 dark:text-slate-300" aria-label="Close conversations">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="relative">
                    <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input id="userSearch" type="search" placeholder="Search allies" class="ui-input h-11 pl-10">
                </div>
            </div>

            <div id="userList" class="scrollbar-hide flex-1 overflow-y-auto p-2">
                @forelse ($users as $user)
                    @php
                        $lastMessage = $user->sentMessages->concat($user->receivedMessages)->sortByDesc('created_at')->first();
                    @endphp
                    <button
                        type="button"
                        class="user-item group flex w-full items-center gap-3 rounded-lg p-3 text-left transition hover:bg-orange-50 dark:hover:bg-orange-950/30"
                        data-id="{{ $user->id }}"
                        data-name="{{ $user->name }}"
                        data-avatar="{{ $user->profile_photo_url }}"
                        data-online="{{ $user->isOnline() ? '1' : '0' }}"
                    >
                        <span class="relative shrink-0">
                            <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="h-11 w-11 rounded-full border border-orange-200 object-cover ring-2 ring-white dark:border-orange-900 dark:ring-slate-900">
                            <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-white dark:border-slate-900 {{ $user->isOnline() ? 'bg-green-500' : 'bg-slate-400' }}"></span>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="flex items-center justify-between gap-2">
                                <span class="truncate text-sm font-black text-slate-950 group-hover:text-orange-700 dark:text-white dark:group-hover:text-orange-300">{{ $user->name }}</span>
                                @if($lastMessage)
                                    <span class="shrink-0 text-[11px] font-semibold text-slate-400">{{ $lastMessage->created_at->format('h:i A') }}</span>
                                @endif
                            </span>
                            <span class="mt-1 block truncate text-xs font-medium text-slate-500 dark:text-slate-400">
                                @if($lastMessage)
                                    {{ $lastMessage->sender_id === auth()->id() ? 'You: ' : '' }}{{ Str::limit($lastMessage->message, 36) }}
                                @else
                                    Start a new squad chat
                                @endif
                            </span>
                        </span>
                        @if(($unreadCounts[$user->id] ?? 0) > 0)
                            <span class="unread-badge grid h-6 min-w-6 place-items-center rounded-full bg-red-600 px-1.5 text-xs font-black text-white">{{ $unreadCounts[$user->id] }}</span>
                        @endif
                    </button>
                @empty
                    <div class="p-3">
                        <x-ui.empty-state
                            icon="fas fa-user-group"
                            title="No allies yet"
                            description="Add friends before opening a private chat."
                        >
                            <x-slot:action>
                                <x-ui.button :href="route('friends.list')">
                                    <i class="fas fa-user-plus"></i>
                                    Find Allies
                                </x-ui.button>
                            </x-slot:action>
                        </x-ui.empty-state>
                    </div>
                @endforelse
            </div>
        </aside>

        <section id="chatSection" class="ui-card grid min-h-[540px] grid-rows-[auto_1fr_auto] overflow-hidden">
            <header class="border-b border-slate-200 p-4 dark:border-slate-800">
                <div id="emptyChatHeader" class="flex items-center justify-between gap-3">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="grid h-11 w-11 place-items-center rounded-lg bg-orange-50 text-orange-600 dark:bg-orange-950/40 dark:text-orange-300">
                            <i class="fas fa-comment-dots"></i>
                        </div>
                        <div class="min-w-0">
                            <h2 class="font-black text-slate-950 dark:text-white">Select a conversation</h2>
                            <p class="truncate text-xs font-semibold text-slate-500 dark:text-slate-400">Choose an ally to start chatting.</p>
                        </div>
                    </div>
                    <button id="showUsersSidebar" type="button" class="grid h-10 w-10 place-items-center rounded-lg border border-slate-200 text-orange-600 lg:hidden dark:border-slate-800 dark:text-orange-300" aria-label="Show conversations">
                        <i class="fas fa-user-group"></i>
                    </button>
                </div>

                <div id="activeChatHeader" class="hidden items-center justify-between gap-3">
                    <div class="flex min-w-0 items-center gap-3">
                        <button id="backToUsers" type="button" class="grid h-10 w-10 place-items-center rounded-lg border border-slate-200 text-slate-500 lg:hidden dark:border-slate-800 dark:text-slate-300" aria-label="Back to conversations">
                            <i class="fas fa-arrow-left"></i>
                        </button>
                        <span class="relative shrink-0">
                            <img id="chatUserAvatar" src="" alt="" class="h-11 w-11 rounded-full border border-orange-200 object-cover dark:border-orange-900">
                            <span id="chatUserStatus" class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-white dark:border-slate-900"></span>
                        </span>
                        <div class="min-w-0">
                            <a id="chatUserNameLink" href="#" class="group">
                                <h2 id="chatUserName" class="truncate font-black text-slate-950 group-hover:text-orange-700 dark:text-white dark:group-hover:text-orange-300"></h2>
                            </a>
                            <p id="chatUserStatusText" class="text-xs font-semibold text-slate-500 dark:text-slate-400"></p>
                        </div>
                    </div>
                    <span class="rank-badge hidden sm:inline-flex"><i class="fas fa-lock"></i> Private</span>
                </div>
            </header>

            <div id="chatMessages" class="shinobi-grid scrollbar-hide min-h-0 overflow-y-auto bg-orange-50/35 p-4 dark:bg-slate-950/35">
                <x-ui.empty-state
                    class="h-full border-solid bg-white/62 dark:bg-slate-950/42"
                    icon="fas fa-comments"
                    title="Your messages will appear here"
                    description="Select a conversation from the ally list."
                />
            </div>

            <footer id="messageFormContainer" class="hidden border-t border-slate-200 bg-white/88 p-3 backdrop-blur dark:border-slate-800 dark:bg-slate-950/88">
                <div id="typingIndicator" class="mb-2 hidden items-center gap-2 px-2 text-xs font-bold text-orange-700 dark:text-orange-300">
                    <span class="flex items-center gap-1">
                        <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-orange-500"></span>
                        <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-orange-500 [animation-delay:120ms]"></span>
                        <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-orange-500 [animation-delay:240ms]"></span>
                    </span>
                    <span id="typingIndicatorText">Your ally is typing...</span>
                </div>
                <form id="messageForm" class="flex items-end gap-2">
                    @csrf
                    <input type="hidden" name="receiver_id" id="receiverId">
                    <textarea id="messageInput" name="message" rows="1" maxlength="1000" placeholder="Send a squad message..." class="ui-input max-h-32 min-h-11 flex-1 resize-none py-3" required></textarea>
                    <button id="sendButton" type="submit" class="ui-btn ui-btn-primary grid h-11 w-11 shrink-0 px-0" disabled aria-label="Send message">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
                <p class="mt-2 text-center text-[11px] font-semibold text-slate-400">Enter to send. Shift + Enter for a new line.</p>
            </footer>
        </section>
    </div>
</x-ui.page>
@endsection

@push('scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const userList = document.getElementById('userList');
    const userSearch = document.getElementById('userSearch');
    const usersSidebar = document.getElementById('usersSidebar');
    const chatSection = document.getElementById('chatSection');
    const emptyChatHeader = document.getElementById('emptyChatHeader');
    const activeChatHeader = document.getElementById('activeChatHeader');
    const chatUserAvatar = document.getElementById('chatUserAvatar');
    const chatUserName = document.getElementById('chatUserName');
    const chatUserNameLink = document.getElementById('chatUserNameLink');
    const chatUserStatus = document.getElementById('chatUserStatus');
    const chatUserStatusText = document.getElementById('chatUserStatusText');
    const chatMessages = document.getElementById('chatMessages');
    const messageFormContainer = document.getElementById('messageFormContainer');
    const messageForm = document.getElementById('messageForm');
    const receiverIdInput = document.getElementById('receiverId');
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendButton');
    const typingIndicator = document.getElementById('typingIndicator');
    const typingIndicatorText = document.getElementById('typingIndicatorText');
    const showUsersSidebar = document.getElementById('showUsersSidebar');
    const closeUsersSidebar = document.getElementById('closeUsersSidebar');
    const backToUsers = document.getElementById('backToUsers');
    const realtimeStatus = document.getElementById('realtimeStatus');
    const realtimeStatusDot = document.getElementById('realtimeStatusDot');
    const realtimeStatusText = document.getElementById('realtimeStatusText');

    const authId = {{ auth()->id() }};
    const currentUserAvatar = @json(auth()->user()->profile_photo_url);
    const chatLoadBase = @json(url('/chat/user'));
    const sendUrl = @json(route('chat.send'));
    const searchUrl = @json(route('chat.search'));
    const profileUrlTemplate = @json(route('user.profile', ':id'));
    const reverbConfig = @json($reverbClientConfig);
    const initialUserId = @json($initialUserId);

    let currentChatUser = null;
    let messageDates = new Set();
    let displayedMessageIds = new Set();
    let pusher = null;
    let channel = null;
    let pollingTimer = null;
    let pollingInFlight = false;
    let typingTimer = null;
    let lastTypingSentAt = 0;

    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (character) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    }[character]));

    const imageUrl = (value) => /^(https?:\/\/|\/|data:image\/)/i.test(String(value ?? '')) ? value : '/profile/profile.png';
    const profileUrl = (userId) => profileUrlTemplate.replace(':id', encodeURIComponent(userId));

    function setRealtimeStatus(status, message) {
        const colors = {
            online: 'bg-green-500',
            connecting: 'bg-amber-400',
            offline: 'bg-red-500'
        };

        realtimeStatusDot.className = `h-2.5 w-2.5 rounded-full ${colors[status] || colors.offline}`;
        realtimeStatusText.textContent = message;
        realtimeStatus.classList.toggle('border-green-200', status === 'online');
        realtimeStatus.classList.toggle('text-green-700', status === 'online');
        realtimeStatus.classList.toggle('border-red-200', status === 'offline');
        realtimeStatus.classList.toggle('text-red-700', status === 'offline');
    }

    function setMobileMode(mode) {
        if (window.innerWidth >= 1024) return;
        usersSidebar.classList.toggle('hidden', mode === 'chat');
        chatSection.classList.toggle('hidden', mode === 'list');
    }

    function resetComposerHeight() {
        messageInput.style.height = 'auto';
        messageInput.style.height = `${Math.min(messageInput.scrollHeight, 128)}px`;
    }

    function dateDivider(date) {
        const wrapper = document.createElement('div');
        wrapper.className = 'my-4 flex justify-center';
        wrapper.innerHTML = `<span class="rounded-lg border border-orange-200 bg-white/88 px-3 py-1 text-[11px] font-black uppercase tracking-wide text-orange-700 shadow-sm dark:border-orange-900 dark:bg-slate-900/88 dark:text-orange-300">${escapeHtml(date)}</span>`;
        return wrapper;
    }

    function messageMarkup(message, isMe, pending = false) {
        const avatar = isMe ? currentUserAvatar : message.sender?.profile_photo_url;
        const senderName = isMe ? 'You' : (message.sender?.name ?? 'Ally');
        const bubbleClass = isMe
            ? 'bg-gradient-to-br from-orange-500 to-red-600 text-white rounded-br-sm'
            : 'border border-slate-200 bg-white text-slate-900 rounded-bl-sm dark:border-slate-800 dark:bg-slate-900 dark:text-slate-100';

        return `
            <div class="flex ${isMe ? 'justify-end' : 'justify-start'} message-appear">
                <div class="flex max-w-[88%] gap-2 ${isMe ? 'flex-row-reverse' : ''} sm:max-w-[70%]">
                    <a href="${isMe ? profileUrl(authId) : profileUrl(message.sender_id)}" class="shrink-0">
                        <img src="${escapeHtml(imageUrl(avatar))}" alt="${escapeHtml(senderName)}" class="h-8 w-8 rounded-full border border-orange-200 object-cover dark:border-orange-900">
                    </a>
                    <div class="${isMe ? 'text-right' : 'text-left'}">
                        <div class="inline-block rounded-2xl px-4 py-2.5 shadow-sm ${bubbleClass} ${pending ? 'opacity-75' : ''}">
                            <p class="whitespace-pre-wrap break-words text-sm leading-6">${escapeHtml(message.message)}</p>
                        </div>
                        <p data-message-status class="mt-1 px-1 text-[11px] font-semibold text-slate-400">${escapeHtml(pending ? 'Sending...' : message.created_at)}</p>
                    </div>
                </div>
            </div>
        `;
    }

    function appendMessage(message, pending = false) {
        const date = message.date;
        if (!messageDates.has(date)) {
            messageDates.add(date);
            chatMessages.appendChild(dateDivider(date));
        }

        const isMe = parseInt(message.sender_id, 10) === parseInt(authId, 10);
        const wrapper = document.createElement('div');
        wrapper.id = pending ? message.id : `msg-${message.id}`;
        wrapper.className = 'mb-3';
        wrapper.innerHTML = messageMarkup(message, isMe, pending);
        chatMessages.appendChild(wrapper);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function appendNewMessages(messages) {
        let added = 0;

        messages.forEach((message) => {
            if (displayedMessageIds.has(message.id)) {
                return;
            }

            displayedMessageIds.add(message.id);
            appendMessage(message);
            added += 1;
        });

        return added;
    }

    function renderMessages(messages) {
        chatMessages.innerHTML = '';
        messageDates.clear();
        displayedMessageIds.clear();

        if (!messages.length) {
            chatMessages.innerHTML = `
                <div class="ui-empty-state h-full border-solid bg-white/62 dark:bg-slate-950/42">
                    <div>
                        <div class="ui-empty-icon mx-auto">
                            <i class="fas fa-comment-slash text-xl"></i>
                        </div>
                        <h3 class="mt-5 text-2xl font-black text-slate-950 dark:text-white">No messages yet</h3>
                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500 dark:text-slate-400">Send the first message to open this squad channel.</p>
                    </div>
                </div>
            `;
            return;
        }

        messages.forEach((message) => {
            displayedMessageIds.add(message.id);
            appendMessage(message);
        });
    }

    function isRealtimeConnected() {
        return pusher?.connection?.state === 'connected';
    }

    async function syncConversation() {
        if (!currentChatUser || pollingInFlight || isRealtimeConnected()) {
            return;
        }

        pollingInFlight = true;

        try {
            const response = await fetch(`${chatLoadBase}/${encodeURIComponent(currentChatUser)}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (!response.ok) {
                throw new Error('Unable to sync messages.');
            }

            const added = appendNewMessages(await response.json());
            if (added > 0) {
                setRealtimeStatus('online', 'Messages synced');
            } else if (!isRealtimeConnected()) {
                setRealtimeStatus('connecting', 'Syncing messages');
            }
        } catch (error) {
            if (!isRealtimeConnected()) {
                setRealtimeStatus('offline', 'Message sync paused');
            }
        } finally {
            pollingInFlight = false;
        }
    }

    function startConversationSync() {
        window.clearInterval(pollingTimer);
        pollingTimer = window.setInterval(syncConversation, 3000);
    }

    function setTypingIndicator(isTyping, name = 'Your ally') {
        if (!typingIndicator || !typingIndicatorText) {
            return;
        }

        typingIndicatorText.textContent = `${name} is typing...`;
        typingIndicator.classList.toggle('hidden', !isTyping);
        typingIndicator.classList.toggle('flex', isTyping);

        window.clearTimeout(typingTimer);
        if (isTyping) {
            typingTimer = window.setTimeout(() => setTypingIndicator(false), 3500);
        }
    }

    function whisperTyping(isTyping = true) {
        if (!channel || !currentChatUser || !isRealtimeConnected()) {
            return;
        }

        const now = Date.now();
        if (isTyping && now - lastTypingSentAt < 1400) {
            return;
        }

        lastTypingSentAt = now;

        try {
            channel.trigger('client-typing', {
                sender_id: authId,
                receiver_id: parseInt(currentChatUser, 10),
                name: @json(auth()->user()->name),
                typing: isTyping,
            });
        } catch (error) {
            console.warn('Typing indicator failed:', error);
        }
    }

    function initializePusher() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!window.Pusher || !reverbConfig.key || !reverbConfig.host || !csrfToken) {
            setRealtimeStatus('offline', 'Realtime unavailable');
            return;
        }

        setRealtimeStatus('connecting', 'Connecting realtime');

        pusher = new Pusher(reverbConfig.key, {
            wsHost: reverbConfig.host,
            wsPort: reverbConfig.port,
            wssPort: reverbConfig.port,
            forceTLS: reverbConfig.scheme === 'https',
            enabledTransports: reverbConfig.scheme === 'https' ? ['wss'] : ['ws'],
            disableStats: true,
            cluster: 'mt1',
            authEndpoint: '/broadcasting/auth',
            auth: { headers: { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' } }
        });

        pusher.connection.bind('connected', () => setRealtimeStatus('online', 'Realtime connected'));
        pusher.connection.bind('connecting', () => setRealtimeStatus('connecting', 'Connecting realtime'));
        pusher.connection.bind('unavailable', () => setRealtimeStatus('offline', 'Realtime unavailable'));
        pusher.connection.bind('failed', () => setRealtimeStatus('offline', 'Realtime failed'));
        pusher.connection.bind('error', (error) => {
            const detail = error?.error?.data?.message || error?.data?.message || error?.message || error?.type || 'Realtime error';
            setRealtimeStatus('offline', detail);
            console.error('Akatsuki realtime connection error', {
                error,
                host: reverbConfig.host,
                port: reverbConfig.port,
                scheme: reverbConfig.scheme,
            });
        });
    }

    function subscribeToConversation(otherUserId) {
        if (!pusher) return;
        if (channel) pusher.unsubscribe(channel.name);

        const channelName = `private-chat.${Math.min(authId, otherUserId)}.${Math.max(authId, otherUserId)}`;
        channel = pusher.subscribe(channelName);
        channel.bind('pusher:subscription_succeeded', () => setRealtimeStatus('online', 'Realtime connected'));
        channel.bind('pusher:subscription_error', () => setRealtimeStatus('offline', 'Private channel blocked'));
        channel.bind('client-typing', (data) => {
            if (!currentChatUser || parseInt(data.sender_id, 10) !== parseInt(currentChatUser, 10)) return;
            if (parseInt(data.receiver_id, 10) !== parseInt(authId, 10)) return;

            setTypingIndicator(Boolean(data.typing), data.name || 'Your ally');
        });
        channel.bind('message.sent', (data) => {
            if (!currentChatUser || parseInt(data.sender_id, 10) === parseInt(authId, 10)) return;
            if (parseInt(data.sender_id, 10) !== parseInt(currentChatUser, 10) && parseInt(data.receiver_id, 10) !== parseInt(currentChatUser, 10)) return;
            if (displayedMessageIds.has(data.id)) return;
            displayedMessageIds.add(data.id);
            appendMessage(data);
        });
    }

    function setActiveUser(item) {
        userList.querySelectorAll('.user-item').forEach((node) => {
            node.classList.remove('bg-orange-50', 'ring-1', 'ring-orange-200', 'dark:bg-orange-950/35', 'dark:ring-orange-900');
        });
        item.classList.add('bg-orange-50', 'ring-1', 'ring-orange-200', 'dark:bg-orange-950/35', 'dark:ring-orange-900');
    }

    async function openConversation(item) {
        const userId = item.dataset.id;
        currentChatUser = userId;
        receiverIdInput.value = userId;
        setTypingIndicator(false);
        setActiveUser(item);

        chatUserAvatar.src = imageUrl(item.dataset.avatar);
        chatUserName.textContent = item.dataset.name;
        chatUserNameLink.href = profileUrl(userId);
        const online = item.dataset.online === '1';
        chatUserStatus.className = `absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-white dark:border-slate-900 ${online ? 'bg-green-500' : 'bg-slate-400'}`;
        chatUserStatusText.textContent = online ? 'Online now' : 'Offline';

        emptyChatHeader.classList.add('hidden');
        activeChatHeader.classList.remove('hidden');
        activeChatHeader.classList.add('flex');
        messageFormContainer.classList.remove('hidden');
        setMobileMode('chat');

        chatMessages.innerHTML = `
            <div class="space-y-5 p-2">
                <div class="flex justify-start">
                    <div class="flex w-[78%] max-w-md gap-2">
                        <div class="ui-skeleton h-8 w-8 shrink-0 rounded-full"></div>
                        <div class="flex-1 space-y-2">
                            <div class="ui-skeleton h-12 w-full"></div>
                            <div class="ui-skeleton h-3 w-24"></div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end">
                    <div class="w-[72%] max-w-sm space-y-2">
                        <div class="ui-skeleton h-12 w-full"></div>
                        <div class="ui-skeleton ml-auto h-3 w-20"></div>
                    </div>
                </div>
                <div class="flex justify-start">
                    <div class="flex w-[64%] max-w-sm gap-2">
                        <div class="ui-skeleton h-8 w-8 shrink-0 rounded-full"></div>
                        <div class="flex-1 space-y-2">
                            <div class="ui-skeleton h-10 w-full"></div>
                            <div class="ui-skeleton h-3 w-16"></div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        subscribeToConversation(userId);
        startConversationSync();

        try {
            const response = await fetch(`${chatLoadBase}/${encodeURIComponent(userId)}`, { headers: { 'Accept': 'application/json' } });
            if (!response.ok) throw new Error('Unable to load conversation.');
            renderMessages(await response.json());
            item.querySelector('.unread-badge')?.remove();
            await syncConversation();
        } catch (error) {
            chatMessages.innerHTML = `
                <div class="ui-empty-state h-full border-red-200 bg-red-50/60 dark:border-red-900/60 dark:bg-red-950/20">
                    <div>
                        <div class="ui-empty-icon mx-auto text-red-600 dark:text-red-300">
                            <i class="fas fa-triangle-exclamation text-xl"></i>
                        </div>
                        <h3 class="mt-5 text-xl font-black text-red-700 dark:text-red-200">Conversation failed to load</h3>
                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-red-600 dark:text-red-300">${escapeHtml(error.message)}</p>
                    </div>
                </div>
            `;
        }
    }

    function bindUserItems() {
        userList.querySelectorAll('.user-item').forEach((item) => {
            item.addEventListener('click', () => openConversation(item));
        });
    }

    function renderSearchResults(users) {
        if (!users.length) {
            userList.innerHTML = `
                <div class="p-3">
                    <div class="ui-empty-state min-h-48">
                        <div>
                            <div class="ui-empty-icon mx-auto"><i class="fas fa-magnifying-glass text-xl"></i></div>
                            <h3 class="mt-4 font-black text-slate-950 dark:text-white">No allies found</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">Try another name or add more allies.</p>
                        </div>
                    </div>
                </div>
            `;
            return;
        }

        userList.innerHTML = users.map((user) => `
            <button type="button" class="user-item group flex w-full items-center gap-3 rounded-lg p-3 text-left transition hover:bg-orange-50 dark:hover:bg-orange-950/30"
                data-id="${escapeHtml(user.id)}"
                data-name="${escapeHtml(user.name)}"
                data-avatar="${escapeHtml(imageUrl(user.profile_photo_url))}"
                data-online="${user.is_online ? '1' : '0'}">
                <span class="relative shrink-0">
                    <img src="${escapeHtml(imageUrl(user.profile_photo_url))}" alt="${escapeHtml(user.name)}" class="h-11 w-11 rounded-full border border-orange-200 object-cover ring-2 ring-white dark:border-orange-900 dark:ring-slate-900">
                    <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-white dark:border-slate-900 ${user.is_online ? 'bg-green-500' : 'bg-slate-400'}"></span>
                </span>
                <span class="min-w-0 flex-1">
                    <span class="flex items-center justify-between gap-2">
                        <span class="truncate text-sm font-black text-slate-950 dark:text-white">${escapeHtml(user.name)}</span>
                        ${user.last_message_time ? `<span class="shrink-0 text-[11px] font-semibold text-slate-400">${escapeHtml(user.last_message_time)}</span>` : ''}
                    </span>
                    <span class="mt-1 block truncate text-xs font-medium text-slate-500 dark:text-slate-400">${escapeHtml(user.last_message || 'Click to start chatting')}</span>
                </span>
            </button>
        `).join('');
        bindUserItems();
    }

    let searchTimeout;
    userSearch.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        const query = userSearch.value.trim();
        searchTimeout = setTimeout(async () => {
            if (query.length === 0) {
                window.location.href = @json(route('chat.index'));
                return;
            }
            if (query.length < 2) return;

            try {
                const response = await fetch(`${searchUrl}?q=${encodeURIComponent(query)}`, { headers: { 'Accept': 'application/json' } });
                if (!response.ok) throw new Error('Search failed.');
                renderSearchResults(await response.json());
            } catch (error) {
                userList.innerHTML = `<div class="p-6 text-center text-sm font-bold text-red-600">${escapeHtml(error.message)}</div>`;
            }
        }, 250);
    });

    messageInput.addEventListener('input', () => {
        sendButton.disabled = messageInput.value.trim() === '';
        resetComposerHeight();
        whisperTyping(messageInput.value.trim() !== '');
    });

    messageInput.addEventListener('blur', () => whisperTyping(false));

    messageInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            if (messageInput.value.trim()) messageForm.requestSubmit();
        }
    });

    messageForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const message = messageInput.value.trim();
        const receiverId = receiverIdInput.value;
        if (!message || !receiverId) return;

        const tempId = `temp-${Date.now()}`;
        appendMessage({
            id: tempId,
            sender_id: authId,
            receiver_id: receiverId,
            sender: { name: 'You', profile_photo_url: currentUserAvatar },
            message,
            created_at: 'Sending...',
            date: new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
        }, true);

        messageInput.value = '';
        resetComposerHeight();
        whisperTyping(false);
        setTypingIndicator(false);
        sendButton.disabled = true;
        sendButton.dataset.originalHtml = sendButton.dataset.originalHtml || sendButton.innerHTML;
        sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        try {
            const response = await fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ receiver_id: receiverId, message })
            });
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.message || 'Failed to send message.');

            const temp = document.getElementById(tempId);
            if (temp) {
                temp.id = `msg-${data.data.id}`;
                temp.innerHTML = messageMarkup(data.data, true);
            }
            displayedMessageIds.add(data.data.id);
            window.setTimeout(syncConversation, 400);
        } catch (error) {
            const temp = document.getElementById(tempId);
            const status = temp?.querySelector('[data-message-status]');
            if (status) status.textContent = 'Failed to send';
        } finally {
            sendButton.innerHTML = sendButton.dataset.originalHtml || '<i class="fas fa-paper-plane"></i>';
            sendButton.disabled = messageInput.value.trim() === '';
        }
    });

    showUsersSidebar?.addEventListener('click', () => setMobileMode('list'));
    closeUsersSidebar?.addEventListener('click', () => setMobileMode('chat'));
    backToUsers?.addEventListener('click', () => setMobileMode('list'));
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            usersSidebar.classList.remove('hidden');
            chatSection.classList.remove('hidden');
        }
    });

    initializePusher();
    bindUserItems();
    resetComposerHeight();

    if (initialUserId) {
        const item = userList.querySelector(`.user-item[data-id="${CSS.escape(String(initialUserId))}"]`);
        if (item) openConversation(item);
    }
});
</script>
@endpush
