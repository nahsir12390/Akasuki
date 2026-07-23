<footer class="mt-auto border-t border-orange-200/70 bg-white/85 text-slate-700 shadow-[0_-18px_40px_rgba(15,23,42,0.04)] backdrop-blur-xl dark:border-slate-800 dark:bg-slate-950/86 dark:text-slate-300">
    <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 lg:grid-cols-[1.4fr_1fr_1fr] lg:px-8">
        <div>
            <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                <span class="grid h-11 w-11 place-items-center rounded-lg bg-orange-50 text-orange-600 ring-1 ring-orange-100 dark:bg-orange-950/50 dark:text-orange-300 dark:ring-orange-900">
                    <i class="fas fa-cloud-sun text-xl"></i>
                </span>
                <span>
                    <span class="block text-lg font-black tracking-normal text-slate-950 dark:text-white">Akatsuki Devs</span>
                    <span class="block text-xs font-semibold text-orange-600 dark:text-orange-300">Developer community</span>
                </span>
            </a>

            <p class="mt-4 max-w-md text-sm leading-6 text-slate-500 dark:text-slate-400">
                A focused space for developers to learn, share progress, build friendships, and keep improving together.
            </p>
        </div>

        <div>
            <h3 class="text-sm font-black uppercase tracking-wide text-slate-950 dark:text-white">Explore</h3>
            <ul class="mt-4 space-y-3 text-sm font-semibold">
                <li><a href="{{ route('home') }}" class="hover:text-orange-600 dark:hover:text-orange-300">Home</a></li>
                <li><a href="{{ route('show.post') }}" class="hover:text-orange-600 dark:hover:text-orange-300">Community</a></li>
                <li><a href="{{ route('tutorial.php') }}" class="hover:text-orange-600 dark:hover:text-orange-300">PHP Tutorials</a></li>
                <li><a href="{{ route('tutorial.laravel') }}" class="hover:text-orange-600 dark:hover:text-orange-300">Laravel Tutorials</a></li>
                <li><a href="https://nasiru-portfolio.onrender.com/" target="_blank" rel="noopener noreferrer" class="hover:text-orange-600 dark:hover:text-orange-300">Nasiru Portfolio</a></li>
            </ul>
        </div>

        <div>
            <h3 class="text-sm font-black uppercase tracking-wide text-slate-950 dark:text-white">Connect</h3>
            <div class="mt-4 flex gap-2">
                <a href="https://github.com/nahsir12390" target="_blank" rel="noopener noreferrer" aria-label="GitHub" class="grid h-10 w-10 place-items-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:border-orange-300 hover:text-orange-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-orange-700 dark:hover:text-orange-300">
                    <i class="fab fa-github"></i>
                </a>
                <a href="https://linkedin.com/in/nasiru-zakari-a5ba7a31b" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn" class="grid h-10 w-10 place-items-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:border-orange-300 hover:text-orange-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-orange-700 dark:hover:text-orange-300">
                    <i class="fab fa-linkedin"></i>
                </a>
                <a href="https://twitter.com/nasiru_zakari" target="_blank" rel="noopener noreferrer" aria-label="Twitter" class="grid h-10 w-10 place-items-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:border-orange-300 hover:text-orange-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-orange-700 dark:hover:text-orange-300">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="https://nasiru-portfolio.onrender.com/" target="_blank" rel="noopener noreferrer" aria-label="Portfolio" class="grid h-10 w-10 place-items-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:border-orange-300 hover:text-orange-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-orange-700 dark:hover:text-orange-300">
                    <i class="fas fa-briefcase"></i>
                </a>
            </div>
            <a href="mailto:contact@akatsukidevs.com" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold hover:text-orange-600 dark:hover:text-orange-300">
                <i class="fas fa-envelope text-orange-500"></i>
                contact@akatsukidevs.com
            </a>
        </div>
    </div>

    <div class="border-t border-orange-200/70 px-4 py-4 text-center text-xs font-semibold text-slate-500 dark:border-slate-800 dark:text-slate-500">
        &copy; {{ date('Y') }} Akatsuki Devs Community. Built for developers who keep showing up.
    </div>
</footer>
