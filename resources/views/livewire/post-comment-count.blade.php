<div class="inline-flex items-center gap-1 group">
    <i class="fas fa-comment text-orange-500 text-xs sm:text-sm group-hover:text-red-500 transition-colors duration-300"></i>
    <span class="font-medium text-gray-700 dark:text-gray-300">{{ $commentsCount }}</span>
    <span class="text-gray-500 dark:text-gray-400 text-xs">{{ \Illuminate\Support\Str::plural('comment', $commentsCount) }}</span>
</div>