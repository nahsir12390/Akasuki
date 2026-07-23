@extends('layouts.app')

@section('title', 'HTML Tutorials - Learn Web Development')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-900 dark:to-gray-800 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full mb-4">
                <i class="fab fa-html5 text-3xl text-blue-600 dark:text-blue-400"></i>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                HTML Tutorials
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto leading-relaxed">
                Master the foundation of web development with comprehensive HTML tutorials. 
                Learn to structure web pages and create semantic, accessible content.
            </p>
        </div>

        <!-- Stats Section -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-2">8+</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Video Tutorials</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400 mb-2">Beginner</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Difficulty Level</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400 mb-2">Free</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">No Cost</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-center shadow-sm border border-gray-100 dark:border-gray-700">
                <div class="text-2xl font-bold text-orange-600 dark:text-orange-400 mb-2">100%</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">Practical</div>
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
                <div class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 hover:border-blue-200 dark:hover:border-blue-800">
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
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2 text-sm leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
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
                            class="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center justify-center group/btn"
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
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-code text-2xl text-white"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                    Practice HTML Live
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-300 mb-8 leading-relaxed">
                    Apply what you've learned in our interactive playground. Experiment with HTML code 
                    and see results instantly without any setup required.
                </p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-2xl mx-auto">
                    <a href="https://codepen.io/pen/" target="_blank" rel="noopener noreferrer"
                       class="group bg-gradient-to-r from-blue-600 to-blue-700 text-white p-6 rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <div class="text-center">
                            <i class="fab fa-codepen text-3xl mb-3"></i>
                            <h3 class="font-semibold text-lg mb-2">CodePen</h3>
                            <p class="text-blue-100 text-sm opacity-90">Live HTML/CSS/JS Editor</p>
                        </div>
                    </a>
                    
                    <a href="https://jsfiddle.net/" target="_blank" rel="noopener noreferrer"
                       class="group bg-gradient-to-r from-green-600 to-green-700 text-white p-6 rounded-xl hover:from-green-700 hover:to-green-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <div class="text-center">
                            <i class="fab fa-jsfiddle text-3xl mb-3"></i>
                            <h3 class="font-semibold text-lg mb-2">JSFiddle</h3>
                            <p class="text-green-100 text-sm opacity-90">Quick Code Testing</p>
                        </div>
                    </a>
                    
                    <a href="https://playcode.io/" target="_blank" rel="noopener noreferrer"
                       class="group bg-gradient-to-r from-purple-600 to-purple-700 text-white p-6 rounded-xl hover:from-purple-700 hover:to-purple-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <div class="text-center">
                            <i class="fas fa-laptop-code text-3xl mb-3"></i>
                            <h3 class="font-semibold text-lg mb-2">PlayCode</h3>
                            <p class="text-purple-100 text-sm opacity-90">Modern Playground</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Learning Path Section -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 text-white mb-12">
            <div class="text-center max-w-3xl mx-auto">
                <h2 class="text-3xl font-bold mb-4">Ready to Continue Your Journey?</h2>
                <p class="text-blue-100 text-lg mb-8">
                    After mastering HTML, explore CSS to style your websites and JavaScript to make them interactive.
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ route('tutorial.css') }}" 
                       class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors duration-200">
                        Learn CSS Next
                    </a>
                    <a href="{{ route('tutorial.js') }}" 
                       class="bg-transparent border-2 border-white text-white px-6 py-3 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-all duration-200">
                        Learn JavaScript
                    </a>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white text-center mb-8">
                Frequently Asked Questions
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto">
                <div class="space-y-4">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Is HTML difficult to learn?</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            HTML is one of the easiest languages to start with. It uses simple tags and has a gentle learning curve.
                        </p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">How long does it take to learn HTML?</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Most beginners can learn the basics of HTML in 1-2 weeks with consistent practice.
                        </p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Do I need any prior experience?</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            No prior programming experience is needed. HTML is perfect for absolute beginners.
                        </p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">What can I build with HTML?</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            You can create websites, web applications, emails, and much more. It's the foundation of the web.
                        </p>
                    </div>
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
    
    .aspect-w-16 {
        position: relative;
    }
    
    .aspect-w-16::before {
        content: "";
        display: block;
        padding-bottom: 56.25%;
    }
    
    .aspect-w-16 > * {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
</style>
@endsection