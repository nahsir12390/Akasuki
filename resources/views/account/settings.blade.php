@extends('layouts.app')

@section('title', 'Settings - Akatsuki Devs')

@php
    $user = $user ?? auth()->user();
    $skills = old('skills', is_array($user->skills) ? implode(', ', $user->skills) : '');
    $interests = old('interests', is_array($user->interests) ? implode(', ', $user->interests) : '');
    $tabs = [
        'profile' => ['label' => 'Profile', 'icon' => 'fas fa-user-ninja'],
        'skills' => ['label' => 'Jutsu', 'icon' => 'fas fa-scroll'],
        'security' => ['label' => 'Security', 'icon' => 'fas fa-shield-alt'],
        'preferences' => ['label' => 'Preferences', 'icon' => 'fas fa-sliders-h'],
        'social' => ['label' => 'Connections', 'icon' => 'fas fa-link'],
    ];
@endphp

@section('content')
<x-ui.page width="max-w-7xl">
    <div
        x-data="{
            tab: window.location.hash ? window.location.hash.replace('#', '') : 'profile',
            photoUrl: '{{ $user->profile_photo ? asset('storage/' . $user->profile_photo) : $user->profile_photo_url }}',
            showCurrent: false,
            showNew: false,
            showConfirm: false,
            setTab(value) {
                this.tab = value;
                history.replaceState(null, '', `#${value}`);
            },
            previewPhoto(event) {
                const file = event.target.files[0];
                if (!file) return;
                this.photoUrl = URL.createObjectURL(file);
            }
        }"
        class="grid gap-6 lg:grid-cols-[300px_1fr]"
    >
        <aside class="space-y-5">
            <x-ui.card class="p-5">
                <div class="flex items-center gap-3">
                    <div class="chakra-orbit grid h-14 w-14 place-items-center rounded-lg bg-gradient-to-br from-orange-500 to-red-600 text-white shadow-lg shadow-orange-500/25">
                        <i class="fas fa-cog text-xl"></i>
                    </div>
                    <div class="min-w-0">
                        <h1 class="truncate text-xl font-black tracking-normal text-slate-950 dark:text-white">Settings</h1>
                        <p class="text-sm font-semibold text-orange-600 dark:text-orange-300">Shinobi control panel</p>
                    </div>
                </div>

                <nav class="mt-6 grid gap-2">
                    @foreach($tabs as $key => $item)
                        <button
                            type="button"
                            @click="setTab('{{ $key }}')"
                            class="flex items-center gap-3 rounded-lg px-3 py-3 text-left text-sm font-black transition"
                            :class="tab === '{{ $key }}'
                                ? 'bg-orange-50 text-orange-700 ring-1 ring-orange-200 dark:bg-orange-950/35 dark:text-orange-300 dark:ring-orange-900'
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white'"
                        >
                            <i class="{{ $item['icon'] }} w-5 text-orange-500"></i>
                            {{ $item['label'] }}
                        </button>
                    @endforeach
                </nav>
            </x-ui.card>

            <x-ui.card class="p-5">
                <h2 class="text-sm font-black uppercase tracking-wide text-slate-950 dark:text-white">Account Rank</h2>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="ui-muted">Joined</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="ui-muted">Scrolls</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $user->posts()->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="ui-muted">Allies</span>
                        <span class="font-bold text-slate-900 dark:text-white">{{ $user->getFriends()->count() }}</span>
                    </div>
                </div>
            </x-ui.card>
        </aside>

        <div class="min-w-0 space-y-5">
            @if (session('success'))
                <x-ui.alert>{{ session('success') }}</x-ui.alert>
            @endif

            @if (session('error'))
                <x-ui.alert type="error">{{ session('error') }}</x-ui.alert>
            @endif

            @if ($errors->any())
                <x-ui.alert type="error">
                    {{ $errors->first() }}
                </x-ui.alert>
            @endif

            <x-ui.card class="scroll-panel p-5 sm:p-6" x-show="tab === 'profile'" x-cloak>
                <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="rank-badge"><i class="fas fa-id-card"></i> Public identity</p>
                        <h2 class="mt-3 text-2xl font-black tracking-normal text-slate-950 dark:text-white">Profile Information</h2>
                    </div>
                    <x-ui.button :href="route('user.myprofile')" variant="secondary">
                        <i class="fas fa-eye"></i>
                        View Profile
                    </x-ui.button>
                </div>

                <form action="{{ route('account.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="flex flex-col gap-5 rounded-lg border border-orange-200/70 bg-white/70 p-4 dark:border-slate-800 dark:bg-slate-900/70 sm:flex-row sm:items-center">
                        <img :src="photoUrl" alt="{{ $user->name }}" class="h-24 w-24 rounded-full border-4 border-white object-cover shadow-lg ring-1 ring-orange-200 dark:border-slate-900 dark:ring-orange-900">
                        <div class="min-w-0 flex-1">
                            <label for="profile_photo" class="ui-label">Profile Photo</label>
                            <input id="profile_photo" name="profile_photo" type="file" accept="image/*" @change="previewPhoto" class="mt-2 block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-orange-50 file:px-4 file:py-2 file:font-bold file:text-orange-700 hover:file:bg-orange-100 dark:text-slate-300 dark:file:bg-orange-950/45 dark:file:text-orange-300">
                            <p class="mt-2 text-xs font-medium text-slate-500 dark:text-slate-400">JPG, PNG, or GIF. Maximum 2MB.</p>
                            @error('profile_photo')
                                <p class="ui-field-error mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <x-ui.input label="Full Name" name="name" icon="fas fa-user" :value="$user->name" required />
                        <x-ui.input label="Email Address" name="email" type="email" icon="fas fa-envelope" :value="$user->email" required />
                    </div>

                    <div class="space-y-2">
                        <label for="bio" class="ui-label"><i class="fas fa-scroll text-orange-500"></i> Bio</label>
                        <textarea id="bio" name="bio" rows="4" class="ui-input min-h-28 resize-y" placeholder="Tell the village about your developer path.">{{ old('bio', $user->bio) }}</textarea>
                        @error('bio')
                            <p class="ui-field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <x-ui.input label="Location" name="location" icon="fas fa-map-marker-alt" :value="$user->location" placeholder="Hidden Leaf Village" />
                        <x-ui.input label="Website" name="website" type="url" icon="fas fa-globe" :value="$user->website" placeholder="https://example.com" />
                    </div>

                    <div class="flex justify-end">
                        <x-ui.button type="submit">
                            <i class="fas fa-save"></i>
                            Save Profile
                        </x-ui.button>
                    </div>
                </form>
            </x-ui.card>

            <x-ui.card class="p-5 sm:p-6" x-show="tab === 'skills'" x-cloak>
                <div class="mb-6">
                    <p class="rank-badge"><i class="fas fa-scroll"></i> Jutsu scrolls</p>
                    <h2 class="mt-3 text-2xl font-black tracking-normal text-slate-950 dark:text-white">Skills & Interests</h2>
                </div>

                <form action="{{ route('account.update-skills') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-2">
                        <label for="skills" class="ui-label"><i class="fas fa-code text-orange-500"></i> Technical Jutsu</label>
                        <textarea id="skills" name="skills" rows="4" class="ui-input min-h-28 resize-y" placeholder="Laravel, Vue, React, MySQL">{{ $skills }}</textarea>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Separate skills with commas.</p>
                        @error('skills')
                            <p class="ui-field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="interests" class="ui-label"><i class="fas fa-heart text-orange-500"></i> Interests</label>
                        <textarea id="interests" name="interests" rows="4" class="ui-input min-h-28 resize-y" placeholder="Backend, UI design, security, teamwork">{{ $interests }}</textarea>
                        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Separate interests with commas.</p>
                        @error('interests')
                            <p class="ui-field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    @if($user->skills || $user->interests)
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900/70">
                            <h3 class="font-black text-slate-950 dark:text-white">Current Scroll Tags</h3>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach(($user->skills ?? []) as $skill)
                                    <span class="rounded-lg bg-orange-50 px-3 py-1.5 text-sm font-bold text-orange-700 ring-1 ring-orange-200 dark:bg-orange-950/35 dark:text-orange-300 dark:ring-orange-900">{{ $skill }}</span>
                                @endforeach
                                @foreach(($user->interests ?? []) as $interest)
                                    <span class="rounded-lg bg-slate-100 px-3 py-1.5 text-sm font-bold text-slate-700 ring-1 ring-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700">{{ $interest }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end">
                        <x-ui.button type="submit">
                            <i class="fas fa-save"></i>
                            Save Jutsu
                        </x-ui.button>
                    </div>
                </form>
            </x-ui.card>

            <x-ui.card class="p-5 sm:p-6" x-show="tab === 'security'" x-cloak>
                <div class="mb-6">
                    <p class="rank-badge"><i class="fas fa-shield-alt"></i> Account seal</p>
                    <h2 class="mt-3 text-2xl font-black tracking-normal text-slate-950 dark:text-white">Security & Password</h2>
                </div>

                <form action="{{ route('account.update-password') }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div class="space-y-2">
                        <label for="current_password" class="ui-label"><i class="fas fa-key text-orange-500"></i> Current Password</label>
                        <div class="relative">
                            <input id="current_password" name="current_password" :type="showCurrent ? 'text' : 'password'" class="ui-input pr-12" required>
                            <button type="button" @click="showCurrent = !showCurrent" class="absolute inset-y-0 right-0 grid w-11 place-items-center text-slate-400 hover:text-orange-600" aria-label="Toggle current password">
                                <i class="fas" :class="showCurrent ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="ui-field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="space-y-2">
                            <label for="password" class="ui-label"><i class="fas fa-lock text-orange-500"></i> New Password</label>
                            <div class="relative">
                                <input id="password" name="password" :type="showNew ? 'text' : 'password'" class="ui-input pr-12" required>
                                <button type="button" @click="showNew = !showNew" class="absolute inset-y-0 right-0 grid w-11 place-items-center text-slate-400 hover:text-orange-600" aria-label="Toggle new password">
                                    <i class="fas" :class="showNew ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="ui-field-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="password_confirmation" class="ui-label"><i class="fas fa-check text-orange-500"></i> Confirm Password</label>
                            <div class="relative">
                                <input id="password_confirmation" name="password_confirmation" :type="showConfirm ? 'text' : 'password'" class="ui-input pr-12" required>
                                <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-0 grid w-11 place-items-center text-slate-400 hover:text-orange-600" aria-label="Toggle password confirmation">
                                    <i class="fas" :class="showConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg border border-orange-200 bg-orange-50 p-4 text-sm leading-6 text-orange-800 dark:border-orange-900 dark:bg-orange-950/35 dark:text-orange-200">
                        Use at least 8 characters with uppercase, lowercase, numbers, and symbols.
                    </div>

                    <div class="flex justify-end">
                        <x-ui.button type="submit">
                            <i class="fas fa-key"></i>
                            Update Password
                        </x-ui.button>
                    </div>
                </form>
            </x-ui.card>

            <x-ui.card class="p-5 sm:p-6" x-show="tab === 'preferences'" x-cloak>
                <div class="mb-6">
                    <p class="rank-badge"><i class="fas fa-sliders-h"></i> Village preferences</p>
                    <h2 class="mt-3 text-2xl font-black tracking-normal text-slate-950 dark:text-white">Notifications & Privacy</h2>
                </div>

                <x-notification-setup class="mb-5" />

                <form action="{{ route('account.update-preferences') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    @foreach([
                        'email_course_updates' => ['title' => 'Jutsu Updates', 'copy' => 'Receive updates when new course scrolls are available.', 'icon' => 'fas fa-scroll'],
                        'email_messages' => ['title' => 'Alliance Messages', 'copy' => 'Receive notifications for new messages from allies.', 'icon' => 'fas fa-comments'],
                        'public_profile' => ['title' => 'Public Profile', 'copy' => 'Allow other developers to view your profile.', 'icon' => 'fas fa-eye'],
                        'show_online_status' => ['title' => 'Online Status', 'copy' => 'Show when you are active in the village.', 'icon' => 'fas fa-signal'],
                    ] as $field => $pref)
                        <div class="flex items-center justify-between gap-4 rounded-lg border border-slate-200 bg-white/70 p-4 dark:border-slate-800 dark:bg-slate-900/72">
                            <div class="flex min-w-0 items-start gap-3">
                                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-orange-50 text-orange-600 dark:bg-orange-950/40 dark:text-orange-300">
                                    <i class="{{ $pref['icon'] }}"></i>
                                </div>
                                <div>
                                    <h3 class="font-black text-slate-950 dark:text-white">{{ $pref['title'] }}</h3>
                                    <p class="mt-1 text-sm leading-6 text-slate-500 dark:text-slate-400">{{ $pref['copy'] }}</p>
                                </div>
                            </div>
                            <label class="relative inline-flex shrink-0 cursor-pointer items-center">
                                <input type="checkbox" name="{{ $field }}" value="1" class="peer sr-only" {{ old($field, $user->{$field}) ? 'checked' : '' }}>
                                <span class="h-6 w-11 rounded-full bg-slate-300 transition peer-checked:bg-orange-500 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-200 dark:bg-slate-700 dark:peer-focus:ring-orange-900"></span>
                                <span class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow transition peer-checked:translate-x-5"></span>
                            </label>
                        </div>
                    @endforeach

                    <div class="flex justify-end pt-2">
                        <x-ui.button type="submit">
                            <i class="fas fa-save"></i>
                            Save Preferences
                        </x-ui.button>
                    </div>
                </form>
            </x-ui.card>

            <x-ui.card class="p-5 sm:p-6" x-show="tab === 'social'" x-cloak>
                <div class="mb-6">
                    <p class="rank-badge"><i class="fas fa-link"></i> Connected accounts</p>
                    <h2 class="mt-3 text-2xl font-black tracking-normal text-slate-950 dark:text-white">Social Connections</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-500 dark:text-slate-400">Use OAuth providers to sign in faster. LinkedIn is shown as planned because no route exists for it yet.</p>
                </div>

                <div class="grid gap-3">
                    <a href="{{ route('auth.github') }}" class="flex items-center justify-between rounded-lg border border-slate-200 bg-white/70 p-4 transition hover:border-orange-300 hover:bg-orange-50 dark:border-slate-800 dark:bg-slate-900/72 dark:hover:border-orange-800 dark:hover:bg-orange-950/35">
                        <span class="flex items-center gap-3 font-black text-slate-950 dark:text-white"><i class="fab fa-github text-xl"></i> GitHub</span>
                        <span class="text-sm font-bold text-orange-600 dark:text-orange-300">Connect</span>
                    </a>
                    <a href="{{ route('auth.google') }}" class="flex items-center justify-between rounded-lg border border-slate-200 bg-white/70 p-4 transition hover:border-orange-300 hover:bg-orange-50 dark:border-slate-800 dark:bg-slate-900/72 dark:hover:border-orange-800 dark:hover:bg-orange-950/35">
                        <span class="flex items-center gap-3 font-black text-slate-950 dark:text-white"><i class="fab fa-google text-xl text-red-500"></i> Google</span>
                        <span class="text-sm font-bold text-orange-600 dark:text-orange-300">Connect</span>
                    </a>
                    <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-900/52">
                        <span class="flex items-center gap-3 font-black text-slate-500 dark:text-slate-400"><i class="fab fa-linkedin text-xl text-blue-600"></i> LinkedIn</span>
                        <span class="text-sm font-bold text-slate-400">Coming soon</span>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>
</x-ui.page>
@endsection
