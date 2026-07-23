@php
    $courses = \App\Models\Course::where('is_active', true)->orderBy('sort_order')->orderBy('title')->get();
@endphp

<div
    x-data="{
        canLeft: false,
        canRight: false,
        update() {
            const rail = this.$refs.rail;
            if (!rail) return;
            this.canLeft = rail.scrollLeft > 4;
            this.canRight = rail.scrollLeft < rail.scrollWidth - rail.clientWidth - 4;
        },
        move(amount) {
            this.$refs.rail?.scrollBy({ left: amount, behavior: 'smooth' });
            setTimeout(() => this.update(), 260);
        },
        wheel(event) {
            const rail = this.$refs.rail;
            if (!rail || Math.abs(event.deltaX) > Math.abs(event.deltaY)) return;
            event.preventDefault();
            rail.scrollLeft += event.deltaY;
            this.update();
        }
    }"
    x-init="$nextTick(() => { update(); $refs.rail?.addEventListener('scroll', () => update(), { passive: true }); window.addEventListener('resize', () => update()); })"
    class="border-t border-orange-100/80 bg-white/78 backdrop-blur-xl dark:border-slate-800 dark:bg-slate-950/78"
>
    <div class="mx-auto max-w-7xl px-3 py-3 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            <div class="hidden shrink-0 items-center gap-2 rounded-lg border border-orange-200 bg-orange-50 px-3 py-2 text-xs font-black uppercase tracking-wide text-orange-700 dark:border-orange-900 dark:bg-orange-950/45 dark:text-orange-300 sm:flex">
                <i class="fas fa-graduation-cap"></i>
                Jutsu Scrolls
            </div>
            <div class="flex shrink-0 items-center gap-2 rounded-lg border border-orange-200 bg-orange-50 px-3 py-2 text-xs font-black uppercase tracking-wide text-orange-700 dark:border-orange-900 dark:bg-orange-950/45 dark:text-orange-300 sm:hidden">
                <i class="fas fa-hand-pointer"></i>
                Swipe
            </div>

            <button
                type="button"
                @click="move(-220)"
                class="grid h-10 w-10 shrink-0 place-items-center rounded-lg border border-slate-200 bg-white text-orange-600 shadow-sm transition hover:border-orange-300 hover:bg-orange-50 disabled:pointer-events-none disabled:opacity-35 dark:border-slate-800 dark:bg-slate-900 dark:text-orange-300 dark:hover:border-orange-800 dark:hover:bg-orange-950/35"
                :disabled="!canLeft"
                aria-label="Scroll courses left"
            >
                <i class="fas fa-chevron-left text-xs"></i>
            </button>

            <div class="relative min-w-0 flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 z-10 w-6 bg-gradient-to-r from-white/95 to-transparent dark:from-slate-950/95"></div>
                <div class="pointer-events-none absolute inset-y-0 right-0 z-10 w-6 bg-gradient-to-l from-white/95 to-transparent dark:from-slate-950/95"></div>

                <div
                    x-ref="rail"
                    @wheel="wheel($event)"
                    class="course-scroll-rail scrollbar-hide flex gap-2 overflow-x-auto scroll-smooth px-3 py-1"
                >
                    @foreach($courses as $course)
                        @php($active = request()->routeIs('tutorial.show') ? request()->route('course')?->is($course) : request()->path() === 'tutorial/' . $course->slug)
                        <a
                            href="{{ route('tutorial.show', $course) }}"
                            class="inline-flex min-h-10 shrink-0 items-center gap-2 rounded-lg border px-3 py-2 text-sm font-bold transition
                                {{ $active
                                    ? 'border-transparent bg-gradient-to-r from-orange-500 to-red-600 text-white shadow-lg shadow-orange-500/20'
                                    : 'border-slate-200 bg-white text-slate-700 hover:border-orange-300 hover:bg-orange-50 hover:text-orange-700 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-orange-800 dark:hover:bg-orange-950/35 dark:hover:text-orange-300' }}">
                            <i class="{{ $course->icon }} text-sm {{ $active ? 'text-white' : 'text-orange-500' }}"></i>
                            <span>{{ str_replace('Advanced ', '', $course->title) }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <button
                type="button"
                @click="move(220)"
                class="grid h-10 w-10 shrink-0 place-items-center rounded-lg border border-slate-200 bg-white text-orange-600 shadow-sm transition hover:border-orange-300 hover:bg-orange-50 disabled:pointer-events-none disabled:opacity-35 dark:border-slate-800 dark:bg-slate-900 dark:text-orange-300 dark:hover:border-orange-800 dark:hover:bg-orange-950/35"
                :disabled="!canRight"
                aria-label="Scroll courses right"
            >
                <i class="fas fa-chevron-right text-xs"></i>
            </button>
        </div>
    </div>
</div>
