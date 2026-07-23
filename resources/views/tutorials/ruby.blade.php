@extends('layouts.app')

@section('title', 'Ruby Tutorials - Learn Elegant Programming')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-rose-50 dark:from-gray-900 dark:to-gray-800 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-rose-100 dark:bg-rose-900 rounded-full mb-4">
                <i class="fas fa-gem text-3xl text-rose-600 dark:text-rose-400"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                Ruby Tutorials
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto leading-relaxed">
                Master Ruby - the elegant, programmer-friendly language designed for productivity and fun. 
                Learn clean syntax, object-oriented programming, and build web applications with Ruby on Rails.
            </p>
        </div>

        <!-- Stats Section -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="text-2xl font-bold text-rose-600 dark:text-rose-400 mb-2">8+</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Video Tutorials</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400 mb-2">Beginner</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Difficulty Level</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-2">Free</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">No Cost</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400 mb-2">Elegant</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Clean Syntax</div>
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
                <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 hover:border-rose-200 dark:hover:border-rose-800">
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
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2 text-sm leading-tight group-hover:text-rose-600 dark:group-hover:text-rose-400 transition-colors">
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
                            class="mt-4 w-full bg-rose-600 hover:bg-rose-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center justify-center group/btn"
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
                <div class="w-16 h-16 bg-gradient-to-r from-rose-500 to-pink-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-terminal text-2xl text-white"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                    Practice Ruby Online
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-300 mb-8 leading-relaxed">
                    Write and test Ruby code directly in your browser with these interactive REPL environments.
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-2xl mx-auto">
                    <a href="https://replit.com/languages/ruby" target="_blank" rel="noopener noreferrer"
                       class="group bg-gradient-to-r from-rose-600 to-rose-700 text-white p-6 rounded-xl hover:from-rose-700 hover:to-rose-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <div class="text-center">
                            <i class="fas fa-code text-3xl mb-3"></i>
                            <h3 class="font-semibold text-lg mb-2">Replit</h3>
                            <p class="text-rose-100 text-sm opacity-90">Full Environment</p>
                        </div>
                    </a>
                    
                    <a href="https://try.ruby-lang.org/" target="_blank" rel="noopener noreferrer"
                       class="group bg-gradient-to-r from-pink-600 to-pink-700 text-white p-6 rounded-xl hover:from-pink-700 hover:to-pink-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <div class="text-center">
                            <i class="fas fa-play text-3xl mb-3"></i>
                            <h3 class="font-semibold text-lg mb-2">Try Ruby</h3>
                            <p class="text-pink-100 text-sm opacity-90">Interactive Tutorial</p>
                        </div>
                    </a>
                    
                    <a href="https://www.jdoodle.com/execute-ruby-online" target="_blank" rel="noopener noreferrer"
                       class="group bg-gradient-to-r from-purple-600 to-purple-700 text-white p-6 rounded-xl hover:from-purple-700 hover:to-purple-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <div class="text-center">
                            <i class="fas fa-bolt text-3xl mb-3"></i>
                            <h3 class="font-semibold text-lg mb-2">JDoodle</h3>
                            <p class="text-purple-100 text-sm opacity-90">Quick Compiler</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Learning Path Section -->
        <div class="bg-gradient-to-r from-rose-600 to-red-600 rounded-2xl p-8 text-white mb-12">
            <div class="text-center max-w-3xl mx-auto">
                <h2 class="text-3xl font-bold mb-4">Ruby on Rails Path</h2>
                <p class="text-rose-100 text-lg mb-8">
                    Take your Ruby skills to the next level with the powerful Rails framework.
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="https://rubyonrails.org/" target="_blank"
                       class="bg-white text-rose-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors duration-200">
                        Learn Ruby on Rails
                    </a>
                    <a href="https://guides.rubyonrails.org/" target="_blank"
                       class="bg-transparent border-2 border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-rose-600 transition-all duration-200">
                        Rails Guides
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