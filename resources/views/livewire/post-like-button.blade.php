<div class="{{ $fullWidth ? 'flex-1' : '' }}">
    @if($showButton)
    <button
        type="button"
        wire:click="setLike({{ $isLiked ? 'false' : 'true' }})"
        wire:loading.attr="disabled"
        wire:target="setLike"
        class="{{ $fullWidth ? 'w-full' : '' }} inline-flex items-center justify-center gap-1 sm:gap-2 px-2 sm:px-4 py-2 text-xs sm:text-sm rounded-xl transition-all duration-300 transform hover:scale-105 disabled:opacity-60 disabled:cursor-not-allowed {{ $isLiked
            ? 'bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900/30 dark:to-red-800/30 text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800 hover:from-red-100 hover:to-red-200 dark:hover:from-red-900/50 dark:hover:to-red-800/50 shadow-md'
            : 'bg-gradient-to-r from-orange-50 to-orange-100 dark:from-orange-900/30 dark:to-orange-800/30 text-orange-600 dark:text-orange-400 border border-orange-200 dark:border-orange-800 hover:from-orange-100 hover:to-orange-200 dark:hover:from-orange-900/50 dark:hover:to-orange-800/50' }}"
    >
        <span wire:loading.remove wire:target="setLike" class="inline-flex items-center gap-1 sm:gap-2">
            @if($isLiked)
                <i class="fas fa-heart text-red-500 text-xs sm:text-sm animate-pulse"></i>
                <span class="hidden xs:inline">Liked</span>
            @else
                <i class="far fa-heart text-xs sm:text-sm group-hover:scale-110 transition-transform"></i>
                <span class="hidden xs:inline">Like</span>
            @endif
        </span>

        <span wire:loading wire:target="setLike" class="inline-flex items-center gap-1 sm:gap-2">
            <i class="fas fa-spinner fa-spin text-xs sm:text-sm"></i>
            <span class="hidden xs:inline">Loading...</span>
        </span>
    </button>
    @endif

    @if($showCount)
        <div class="mt-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400 flex items-center {{ $fullWidth ? 'justify-center' : '' }}">
            <div class="flex items-center gap-1">
                <i class="fas fa-heart text-red-500 text-xs animate-pulse-slow"></i>
                <span class="font-medium">{{ $likesCount }}</span>
                <span>{{ \Illuminate\Support\Str::plural('shinobi', $likesCount) }} {{ \Illuminate\Support\Str::plural('likes', $likesCount) }}</span>
            </div>
        </div>
    @endif
</div>

<style>
    @keyframes pulse-slow {
        0%, 100% { transform: scale(1); opacity: 0.7; }
        50% { transform: scale(1.1); opacity: 1; }
    }
    .animate-pulse-slow {
        animation: pulse-slow 2s ease-in-out infinite;
    }
</style>
