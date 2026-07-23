@extends('layouts.app')

@section('title', 'Search Users - ' . config('app.name'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Find Users</h1>
            <p class="text-gray-600 dark:text-gray-400">Connect with other developers in the community</p>
        </div>

        <!-- Search Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <form method="GET" action="{{ route('users.search') }}" class="flex flex-col md:flex-row gap-4 items-center">
                <div class="flex-1 w-full relative">
                    <input type="text" 
                           name="q" 
                           value="{{ $search }}"
                           placeholder="Search by name or email..."
                           class="w-full px-4 py-3 pl-12 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white placeholder-gray-500 dark:placeholder-gray-400 transition-all duration-200"
                           autocomplete="off">
                    <i class="fas fa-search absolute left-4 top-3.5 text-gray-400"></i>
                    
                    @if($search)
                    <button type="button" 
                            onclick="window.location='{{ route('users.search') }}'"
                            class="absolute right-3 top-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                    @endif
                </div>
                <button type="submit" 
                        class="w-full md:w-auto px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-all duration-200 transform hover:scale-105 flex items-center justify-center">
                    <i class="fas fa-search mr-2"></i>
                    Search
                </button>
            </form>
        </div>

        <!-- Quick Stats -->
        @if($search && $users->total() > 0)
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-info-circle text-blue-500"></i>
                    <span class="text-blue-700 dark:text-blue-300 text-sm">
                        Found {{ $users->total() }} user(s) for "{{ $search }}"
                    </span>
                </div>
                <span class="text-blue-600 dark:text-blue-400 text-sm">
                    Page {{ $users->currentPage() }} of {{ $users->lastPage() }}
                </span>
            </div>
        </div>
        @endif

        <!-- Users Grid -->
        @if($users->count() > 0)
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($users as $user)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 user-card">
                        <!-- User Header -->
                        <div class="flex items-center space-x-4 mb-4">
                            <a href="{{ route('user.profile', $user->id) }}" class="flex-shrink-0 group relative">
                                <img src="{{ $user->profile_photo_url }}" 
                                     alt="{{ $user->name }}"
                                     class="w-16 h-16 rounded-full object-cover border-2 border-blue-500 group-hover:border-blue-600 transition-all duration-300">
                                @if($user->isOnline())
                                    <span class="absolute bottom-0 right-0 w-4 h-4 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full"></span>
                                @endif
                            </a>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('user.profile', $user->id) }}" 
                                   class="block font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200 truncate text-lg">
                                    {{ $user->name }}
                                </a>
                                <p class="text-gray-500 dark:text-gray-400 text-sm truncate">{{ $user->email }}</p>
                                <div class="flex items-center space-x-3 mt-2">
                                    <span class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-newspaper mr-1"></i>
                                        {{ $user->posts_count }} posts
                                    </span>
                                    <span class="flex items-center text-xs {{ $user->isOnline() ? 'text-green-600 dark:text-green-400' : 'text-gray-500 dark:text-gray-400' }}">
                                        <i class="fas fa-circle mr-1 text-xs"></i>
                                        {{ $user->isOnline() ? 'Online' : 'Offline' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Friendship Status & Actions -->
                        @php
                            $status = $friendshipStatuses[$user->id] ?? 'none';
                        @endphp
                        
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            @if($status === 'none')
                                <form action="{{ route('friends.send', $user->id) }}" method="POST" class="inline w-full">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-all duration-200 transform hover:scale-105 flex items-center justify-center">
                                        <i class="fas fa-user-plus mr-2"></i>Add Friend
                                    </button>
                                </form>
                            @elseif($status === 'pending_sent')
                                <div class="flex space-x-2">
                                    <button class="flex-1 px-3 py-2 bg-yellow-500 text-white text-sm font-medium rounded-lg cursor-not-allowed opacity-90 flex items-center justify-center">
                                        <i class="fas fa-clock mr-2"></i>Request Sent
                                    </button>
                                    <form action="{{ route('friends.cancel', $user->id) }}" method="POST" class="flex-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-full px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors duration-200 flex items-center justify-center">
                                            <i class="fas fa-times mr-1"></i>Cancel
                                        </button>
                                    </form>
                                </div>
                            @elseif($status === 'pending_received')
                                <div class="flex space-x-2">
                                    <form action="{{ route('friends.accept', $user->id) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors duration-200 flex items-center justify-center">
                                            <i class="fas fa-check mr-2"></i>Accept
                                        </button>
                                    </form>
                                    <form action="{{ route('friends.reject', $user->id) }}" method="POST" class="flex-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-full px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors duration-200 flex items-center justify-center">
                                            <i class="fas fa-times mr-2"></i>Reject
                                        </button>
                                    </form>
                                </div>
                            @elseif($status === 'accepted')
                                <div class="flex space-x-2">
                                    <span class="flex-1 px-3 py-2 bg-green-500 text-white text-sm font-medium rounded-lg text-center flex items-center justify-center">
                                        <i class="fas fa-check-circle mr-2"></i>Friends
                                    </span>
                                    <form action="{{ route('friends.remove', $user->id) }}" method="POST" class="flex-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-full px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors duration-200 flex items-center justify-center">
                                            <i class="fas fa-user-minus mr-2"></i>Remove
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $users->links() }}
                </div>
            @endif

        @elseif($search)
            <!-- Empty State -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                    <i class="fas fa-search text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-3">No users found</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto">
                    No users found matching "{{ $search }}". Try searching with different keywords or check the spelling.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('users.search') }}" 
                       class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-refresh mr-2"></i>
                        Clear Search
                    </a>
                    <a href="{{ route('friends.list') }}" 
                       class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-200 transform hover:scale-105">
                        <i class="fas fa-user-friends mr-2"></i>
                        View Your Friends
                    </a>
                </div>
            </div>
        @else
            <!-- Initial State -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                <div class="w-24 h-24 mx-auto mb-6 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-3xl text-blue-500"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-3">Search for Users</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto">
                    Enter a name or email address in the search box above to find other users and connect with them. You can send friend requests and build your developer network.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 max-w-2xl mx-auto text-sm text-gray-600 dark:text-gray-400">
                    <div class="flex items-center justify-center">
                        <i class="fas fa-user-plus mr-2 text-blue-500"></i>
                        Send Friend Requests
                    </div>
                    <div class="flex items-center justify-center">
                        <i class="fas fa-comments mr-2 text-green-500"></i>
                        Start Conversations
                    </div>
                    <div class="flex items-center justify-center">
                        <i class="fas fa-code mr-2 text-purple-500"></i>
                        Collaborate on Projects
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    .user-card {
        transition: all 0.3s ease;
    }
    
    .user-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
</style>
@endsection