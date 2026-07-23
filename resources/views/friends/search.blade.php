@extends('layouts.app')
@section('title', 'Search Friends')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                Search Friends
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Find friends in your network
            </p>
        </div>

        <!-- Search Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <form method="GET" action="{{ route('friends.search') }}" class="flex gap-4">
                <div class="flex-1 relative">
                    <input type="text" 
                           name="q" 
                           value="{{ $search }}"
                           placeholder="Search by name or email..."
                           class="w-full px-4 py-3 pl-12 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white placeholder-gray-500 dark:placeholder-gray-400 transition-all duration-200">
                    <i class="fas fa-search absolute left-4 top-3.5 text-gray-400"></i>
                </div>
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition">
                    Search
                </button>
            </form>
        </div>

        <!-- Search Results -->
        @if($search)
            <div class="mb-4">
                <p class="text-gray-600 dark:text-gray-400">
                    @if($friends->total() > 0)
                        Found {{ $friends->total() }} friend(s) for "{{ $search }}"
                    @else
                        No friends found for "{{ $search }}"
                    @endif
                </p>
            </div>
        @endif

        <!-- Friends Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($friends as $friend)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <!-- Friend Info -->
                    <div class="flex items-center space-x-4 mb-4">
                        <a href="{{ route('user.profile', $friend->id) }}" 
                           class="flex-shrink-0 group relative">
                            <img 
                                src="{{ $friend->profile_photo_url }}"
                                alt="{{ $friend->name }}"
                                class="w-12 h-12 rounded-full object-cover border-2 border-blue-500 group-hover:border-blue-600 transition-all duration-300">
                            @if($friend->is_online)
                                <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full"></span>
                            @endif
                        </a>
                        <div class="flex-1 min-w-0">
                            <a href="{{ route('user.profile', $friend->id) }}" 
                               class="group block">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    {{ $friend->name }}
                                </h3>
                            </a>
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                {{ $friend->email }}
                            </p>
                            <div class="flex items-center space-x-2 mt-1">
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-newspaper mr-1"></i>
                                    {{ $friend->posts_count ?? 0 }} posts
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-2">
                        <a href="{{ route('chat.index') }}?user={{ $friend->id }}"
                           class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-lg transition font-medium flex items-center justify-center">
                            <i class="fas fa-comment mr-2"></i>
                            Message
                        </a>
                        <a href="{{ route('user.profile', $friend->id) }}"
                           class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-2 px-4 rounded-lg transition font-medium flex items-center justify-center">
                            <i class="fas fa-user mr-2"></i>
                            Profile
                        </a>
                    </div>
                </div>
            @empty
                @if($search)
                    <div class="col-span-full bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
                        <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <i class="fas fa-search text-2xl text-gray-400 dark:text-gray-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No friends found</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            No friends found matching "{{ $search }}". Try searching with different keywords.
                        </p>
                        <a href="{{ route('friends.search') }}" 
                           class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-medium">
                            <i class="fas fa-refresh mr-2"></i>
                            Clear Search
                        </a>
                    </div>
                @else
                    <div class="col-span-full bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 text-center">
                        <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-2xl text-gray-400 dark:text-gray-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Search for friends</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            Enter a name or email address in the search box above to find your friends.
                        </p>
                    </div>
                @endif
            @endforelse
        </div>

        <!-- Pagination -->
        @if($friends->count() > 0)
            <div class="mt-8">
                {{ $friends->links() }}
            </div>
        @endif
    </div>
</div>
@endsection