@extends('layouts.app')

@section('title', 'MySQL Tutorials - Learn Database Management')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-indigo-50 dark:from-gray-900 dark:to-gray-800 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-100 dark:bg-indigo-900 rounded-full mb-4">
                <i class="fas fa-database text-3xl text-indigo-600 dark:text-indigo-400"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                MySQL Tutorials
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto leading-relaxed">
                Master MySQL - the world's most popular open-source relational database. 
                Learn SQL fundamentals, database design, optimization, and administration for web applications.
            </p>
        </div>

        <!-- Stats Section -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mb-2">8+</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Video Tutorials</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400 mb-2">Beginner+</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Difficulty Level</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-2">Free</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">No Cost</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="text-2xl font-bold text-orange-600 dark:text-orange-400 mb-2">Relational</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Database Type</div>
            </div>
        </div>

        <!-- Video Grid -->
        <div class="mb-12">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Featured Tutorials</h2>
                <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                    <i class="fas fa-play-circle"></i>
                    <span>{{ count($videos) }} videos available</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($videos as $index => $video)
                <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 hover:border-indigo-200 dark:hover:border-indigo-800">
                    <div class="relative aspect-w-16 aspect-h-9 bg-gray-900">
                        <img 
                            src="{{ $video['snippet']['thumbnails']['high']['url'] ?? $video['snippet']['thumbnails']['medium']['url'] }}" 
                            alt="{{ $video['snippet']['title'] }}"
                            class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300"
                        >
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-300"></div>
                        <div class="absolute top-3 right-3">
                            <span class="bg-black bg-opacity-70 text-white text-xs px-2 py-1 rounded-full">
                                {{ $index + 1 }}/{{ count($videos) }}
                            </span>
                        </div>
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="bg-red-600 text-white p-3 rounded-full transform scale-90 group-hover:scale-100 transition-transform duration-300">
                                <i class="fas fa-play text-lg"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2 text-sm leading-tight group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                            {{ $video['snippet']['title'] }}
                        </h3>
                        <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-2 mb-3">
                            {{ Str::limit($video['snippet']['description'], 80) }}
                        </p>
                        
                        <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            <span class="flex items-center">
                                <i class="fas fa-eye mr-1"></i>
                                {{ $video['snippet']['channelTitle'] ?? 'Unknown' }}
                            </span>
                            <span class="flex items-center">
                                <i class="far fa-clock mr-1"></i>
                                {{ date('M j, Y', strtotime($video['snippet']['publishedAt'])) }}
                            </span>
                        </div>
                        
                        <a 
                            href="https://www.youtube.com/watch?v={{ $video['id']['videoId'] }}" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            class="mt-4 w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center justify-center group/btn"
                        >
                            <i class="fas fa-play mr-2 text-xs"></i>
                            Watch Tutorial
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Practice Section -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8 mb-12">
            <div class="text-center max-w-3xl mx-auto">
                <div class="w-16 h-16 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-server text-2xl text-white"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                    Practice SQL Online
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-300 mb-8 leading-relaxed">
                    Write and test SQL queries directly in your browser with these interactive database playgrounds.
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-2xl mx-auto">
                    <a href="https://www.w3schools.com/sql/trysql.asp?filename=trysql_select_all" target="_blank" rel="noopener noreferrer"
                       class="group bg-gradient-to-r from-indigo-600 to-indigo-700 text-white p-6 rounded-xl hover:from-indigo-700 hover:to-indigo-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <div class="text-center">
                            <i class="fas fa-graduation-cap text-3xl mb-3"></i>
                            <h3 class="font-semibold text-lg mb-2">W3Schools</h3>
                            <p class="text-indigo-100 text-sm opacity-90">Interactive SQL</p>
                        </div>
                    </a>
                    
                    <a href="https://sqliteonline.com/" target="_blank" rel="noopener noreferrer"
                       class="group bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6 rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <div class="text-center">
                            <i class="fas fa-database text-3xl mb-3"></i>
                            <h3 class="font-semibold text-lg mb-2">SQLite Online</h3>
                            <p class="text-blue-100 text-sm opacity-90">Multiple Databases</p>
                        </div>
                    </a>
                    
                    <a href="https://extendsclass.com/mysql-online.html" target="_blank" rel="noopener noreferrer"
                       class="group bg-gradient-to-r from-green-600 to-green-700 text-white p-6 rounded-xl hover:from-green-700 hover:to-green-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <div class="text-center">
                            <i class="fas fa-bolt text-3xl mb-3"></i>
                            <h3 class="font-semibold text-lg mb-2">ExtendsClass</h3>
                            <p class="text-green-100 text-sm opacity-90">MySQL Specific</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Learning Path Section -->
        <div class="bg-gradient-to-r from-indigo-600 to-blue-600 rounded-2xl p-8 text-white mb-12">
            <div class="text-center max-w-3xl mx-auto">
                <h2 class="text-3xl font-bold mb-4">Database Development Path</h2>
                <p class="text-indigo-100 text-lg mb-8">
                    Expand your database skills with advanced topics and related technologies.
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="https://dev.mysql.com/doc/" target="_blank"
                       class="bg-white text-indigo-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors duration-200">
                        MySQL Documentation
                    </a>
                    <a href="{{ route('tutorial.laravel') }}" 
                       class="bg-transparent border-2 border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-indigo-600 transition-all duration-200">
                        Laravel with Eloquent
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection