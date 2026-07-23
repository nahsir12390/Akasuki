@extends('layouts.app')
@section('title', 'Create Jutsu - ' . config('app.name'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-orange-50 via-red-50 to-orange-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 py-8 relative overflow-hidden">
    <!-- Floating Elements -->
    <div class="absolute inset-0 opacity-5 pointer-events-none">
        <div class="absolute top-0 left-0 w-full h-full" style="background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><path fill="%23FF6B35" d="M20,40 Q30,30 40,40 T60,40 T80,40" stroke="none"/><path fill="%23D32F2F" d="M30,50 L50,30 L70,50 L50,70 Z" stroke="none"/><circle fill="%23FF8C42" cx="50" cy="50" r="8"/></svg>'); background-repeat: repeat; background-size: 80px;"></div>
    </div>
    
    <div class="max-w-3xl mx-auto px-4 relative z-10">
        <!-- Header with Stylish Naruto Font -->
        <div class="text-center mb-8 animate-fade-in-down">
            <div class="relative inline-block">
                <div class="absolute -top-4 -left-4 w-12 h-12 bg-orange-500 rounded-full opacity-20 animate-ping"></div>
                <div class="absolute -bottom-4 -right-4 w-12 h-12 bg-red-500 rounded-full opacity-20 animate-pulse"></div>
                <i class="fas fa-scroll text-5xl text-orange-500 mb-4 inline-block animate-float"></i>
            </div>
            <h1 class="text-4xl sm:text-5xl font-bold mb-3 relative">
                <span class="bg-gradient-to-r from-orange-500 via-red-500 to-orange-500 bg-clip-text text-transparent animate-gradient-x">
                    Create Your Jutsu
                </span>
                <div class="absolute -bottom-2 left-1/2 transform -translate-x-1/2 w-24 h-1 bg-gradient-to-r from-orange-500 to-red-500 rounded-full"></div>
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-4 text-lg">
                <i class="fas fa-cloud-sun text-orange-500 mr-2"></i>
                Share your forbidden techniques with the shinobi world
                <i class="fas fa-cloud-sun text-orange-500 ml-2"></i>
            </p>
            <p class="text-sm text-orange-600 dark:text-orange-400 mt-2 animate-pulse-slow">
                "A true ninja masters the art of sharing knowledge"
            </p>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-l-4 border-green-500 rounded-xl shadow-lg animate-slide-down">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 text-lg mr-3 animate-bounce"></i>
                    <span class="text-green-800 dark:text-green-200">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="mb-6 p-4 bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 border-l-4 border-red-500 rounded-xl shadow-lg animate-shake">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-3 animate-pulse"></i>
                    <span class="text-red-800 dark:text-red-200 font-semibold">Please fix the following errors:</span>
                </div>
                <ul class="list-disc list-inside text-red-700 dark:text-red-300 text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li><i class="fas fa-chevron-right text-xs mr-2"></i>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 border-l-4 border-red-500 rounded-xl shadow-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 mr-3 animate-pulse"></i>
                    <span class="text-red-800 dark:text-red-200">{{ session('error') }}</span>
                </div>
            </div>
        @endif
        
        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Create Post Form -->
            <div class="lg:col-span-2">
                <div class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-sm rounded-2xl shadow-2xl border border-orange-200/50 dark:border-orange-800/50 p-6 transform transition-all duration-300 hover:shadow-orange-500/20">
                    <!-- Form Header with User Info -->
                    <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-orange-200 dark:border-orange-800">
                        <div class="flex-shrink-0 group relative">
                            <div class="absolute inset-0 rounded-full bg-gradient-to-r from-orange-500 to-red-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300 blur-sm"></div>
                            <img 
                                src="{{ auth()->user()->profile_photo_url }}" 
                                alt="{{ auth()->user()->name }}"
                                class="w-12 h-12 rounded-full object-cover border-2 border-orange-500 shadow-lg relative z-10 group-hover:scale-110 transition-transform duration-300">
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white">{{ auth()->user()->name }}</h3>
                            <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-cloud-sun text-orange-500 text-xs"></i>
                                <span>Public Scroll</span>
                                <span class="w-1 h-1 bg-gray-400 rounded-full"></span>
                                <span><i class="fas fa-users mr-1"></i>Community</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-500 dark:text-gray-400">Shinobi Rank</div>
                            <div class="text-sm font-semibold text-orange-600 dark:text-orange-400">Jonin Level</div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('storeData') }}" id="createPostForm" enctype="multipart/form-data">
                        @csrf

                        <!-- Content Field -->
                        <div class="mb-6">
                            <label for="content" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                <i class="fas fa-feather-alt text-orange-500 mr-2"></i>
                                What technique will you share?
                            </label>
                            <textarea
                                name="content"
                                id="content"
                                rows="6"
                                placeholder="Write your jutsu here... Share your knowledge, ask a question, or start a discussion..."
                                class="w-full px-4 py-4 rounded-xl border-2 border-orange-200 dark:border-orange-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300 resize-none placeholder-gray-400 dark:placeholder-gray-500 font-medium"
                                maxlength="1000"
                            >{{ old('content') }}</textarea>
                            
                            <!-- Character Counter & Validation -->
                            <div class="flex justify-between items-center mt-3">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-scroll mr-1 text-orange-500"></i>
                                    <span id="charCount" class="font-mono font-bold">0</span>
                                    <span>/ 1000 characters</span>
                                </div>
                                @error('content')
                                    <p class="text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1 animate-pulse"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <!-- Media Upload Section -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                <i class="fas fa-image text-orange-500 mr-2"></i>
                                Add Summoning Scroll (Optional)
                            </label>
                            
                            <!-- Media Upload Area -->
                            <div class="border-2 border-dashed border-orange-300 dark:border-orange-700 rounded-xl p-6 text-center transition-all duration-300 hover:border-orange-500 hover:bg-orange-50/50 dark:hover:bg-orange-900/20 cursor-pointer"
                                 id="mediaUploadArea">
                                <div id="uploadPlaceholder">
                                    <div class="w-20 h-20 mx-auto mb-4 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center animate-float">
                                        <i class="fas fa-cloud-upload-alt text-3xl text-orange-500"></i>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-400 mb-2 font-medium">
                                        Drag & drop images or videos here
                                    </p>
                                    <p class="text-sm text-gray-400 dark:text-gray-500 mb-4">
                                        Supports: JPG, PNG, GIF, MP4, MOV, AVI (Max: 100MB)
                                    </p>
                                    <button type="button" 
                                            onclick="document.getElementById('media').click()"
                                            class="akatsuki-btn inline-flex items-center px-6 py-3 rounded-xl text-white font-semibold shadow-md hover:shadow-lg transition-all duration-300">
                                        <i class="fas fa-plus mr-2"></i>
                                        Choose File
                                    </button>
                                </div>
                                
                                <!-- Media Preview -->
                                <div id="mediaPreview" class="hidden">
                                    <div class="flex items-center justify-between mb-4 p-3 bg-orange-50 dark:bg-orange-900/20 rounded-xl">
                                        <div class="flex items-center space-x-3">
                                            <div id="previewThumbnail" class="w-16 h-16 bg-orange-200 dark:bg-orange-800 rounded-lg overflow-hidden flex items-center justify-center">
                                                <i class="fas fa-file text-orange-400 text-2xl"></i>
                                            </div>
                                            <div class="text-left">
                                                <p id="previewFileName" class="font-semibold text-gray-900 dark:text-white"></p>
                                                <p id="previewFileSize" class="text-sm text-gray-500 dark:text-gray-400"></p>
                                                <p id="previewFileType" class="text-xs text-orange-600 dark:text-orange-400 font-mono"></p>
                                            </div>
                                        </div>
                                        <button type="button" 
                                                onclick="removeMedia()"
                                                class="p-2 text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all duration-200">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Media Display -->
                                    <div id="mediaDisplay" class="rounded-xl overflow-hidden border border-orange-200 dark:border-orange-800 shadow-lg">
                                        <!-- Media will be displayed here -->
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Hidden file input -->
                            <input type="file" 
                                   name="media" 
                                   id="media" 
                                   class="hidden" 
                                   accept="image/*,video/*"
                                   onchange="handleMediaSelect(event)">
                            
                            @error('media')
                                <p class="text-sm text-red-600 mt-2 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Post Preview Section -->
                        <div id="postPreview" class="mb-6 p-4 bg-gradient-to-r from-orange-50 to-red-50 dark:from-gray-700 dark:to-gray-700 rounded-xl border border-orange-200 dark:border-orange-800 hidden animate-fade-in">
                            <h4 class="text-sm font-bold text-orange-600 dark:text-orange-400 mb-3 flex items-center">
                                <i class="fas fa-eye mr-2"></i>
                                Jutsu Preview
                            </h4>
                            <p id="previewContent" class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap mb-4 font-medium italic"></p>
                            <div id="previewMedia" class="hidden">
                                <!-- Media preview will be inserted here -->
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 justify-end pt-6 border-t border-orange-200 dark:border-orange-800">
                            <a href="{{ route('show.post') }}" 
                               class="inline-flex items-center justify-center px-6 py-3 border-2 border-orange-300 dark:border-orange-700 text-gray-700 dark:text-gray-300 font-semibold rounded-xl hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-all duration-300 hover:scale-105">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Back to Village
                            </a>
                            <div class="flex gap-3">
                                <button type="button" 
                                        id="previewBtn"
                                        class="inline-flex items-center justify-center px-6 py-3 border-2 border-orange-500 text-orange-600 dark:text-orange-400 font-semibold rounded-xl hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-all duration-300 hover:scale-105 group">
                                    <i class="fas fa-eye mr-2 group-hover:animate-pulse"></i>
                                    Preview Scroll
                                </button>
                                <button type="submit" 
                                        id="submitBtn"
                                        class="akatsuki-btn inline-flex items-center justify-center px-8 py-3 text-white font-bold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Publish Jutsu
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar with Ninja Wisdom -->
            <div class="lg:col-span-1">
                <!-- Wisdom Card -->
                <div class="bg-gradient-to-br from-orange-50 to-red-50 dark:from-gray-800 dark:to-gray-800 rounded-2xl shadow-xl border border-orange-200 dark:border-orange-800 p-6 mb-6 animate-fade-in-up delay-100">
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 mx-auto bg-orange-500 rounded-full flex items-center justify-center mb-3 animate-float">
                            <i class="fas fa-user-ninja text-white text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold bg-gradient-to-r from-orange-500 to-red-500 bg-clip-text text-transparent">
                            Ninja Wisdom
                        </h3>
                    </div>
                    <div class="space-y-4">
                        <div class="p-3 bg-white/50 dark:bg-gray-700/50 rounded-xl">
                            <i class="fas fa-quote-left text-orange-500 text-sm mr-2"></i>
                            <p class="text-sm text-gray-700 dark:text-gray-300 italic">"Hard work is worthless for those that don't believe in themselves."</p>
                            <p class="text-xs text-orange-600 dark:text-orange-400 mt-1 text-right">— Naruto Uzumaki</p>
                        </div>
                        <div class="p-3 bg-white/50 dark:bg-gray-700/50 rounded-xl">
                            <i class="fas fa-quote-left text-orange-500 text-sm mr-2"></i>
                            <p class="text-sm text-gray-700 dark:text-gray-300 italic">"A dropout will beat a genius through hard work."</p>
                            <p class="text-xs text-orange-600 dark:text-orange-400 mt-1 text-right">— Rock Lee</p>
                        </div>
                    </div>
                </div>

                <!-- Posting Tips -->
                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-xl border border-orange-200/50 dark:border-orange-800/50 p-6 mb-6 animate-fade-in-up delay-200">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-lightbulb text-orange-500 mr-2 animate-pulse"></i>
                        Jutsu Scroll Tips
                    </h3>
                    <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                        <li class="flex items-start group">
                            <i class="fas fa-check-circle text-green-500 mr-2 mt-1 flex-shrink-0 group-hover:scale-110 transition-transform"></i>
                            <span class="group-hover:text-orange-600 transition-colors">Be clear and concise in your technique</span>
                        </li>
                        <li class="flex items-start group">
                            <i class="fas fa-check-circle text-green-500 mr-2 mt-1 flex-shrink-0 group-hover:scale-110 transition-transform"></i>
                            <span class="group-hover:text-orange-600 transition-colors">Add summoning scrolls (images/videos)</span>
                        </li>
                        <li class="flex items-start group">
                            <i class="fas fa-check-circle text-green-500 mr-2 mt-1 flex-shrink-0 group-hover:scale-110 transition-transform"></i>
                            <span class="group-hover:text-orange-600 transition-colors">Ask questions to encourage discussion</span>
                        </li>
                        <li class="flex items-start group">
                            <i class="fas fa-check-circle text-green-500 mr-2 mt-1 flex-shrink-0 group-hover:scale-110 transition-transform"></i>
                            <span class="group-hover:text-orange-600 transition-colors">Share your experiences and insights</span>
                        </li>
                    </ul>
                </div>

                <!-- Community Stats -->
                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-xl border border-orange-200/50 dark:border-orange-800/50 p-6 animate-fade-in-up delay-300">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                        <i class="fas fa-chart-bar text-orange-500 mr-2 animate-pulse"></i>
                        Hidden Village Stats
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-2 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded-lg transition-all">
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                <i class="fas fa-scroll text-orange-500 mr-2"></i>Your Jutsu
                            </span>
                            <span class="font-bold text-orange-600 dark:text-orange-400 text-lg">{{ auth()->user()->posts->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center p-2 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded-lg transition-all">
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                <i class="fas fa-scroll text-orange-500 mr-2"></i>Total Scrolls
                            </span>
                            <span class="font-bold text-orange-600 dark:text-orange-400 text-lg">{{ \App\Models\Post::count() }}</span>
                        </div>
                        <div class="flex justify-between items-center p-2 hover:bg-orange-50 dark:hover:bg-orange-900/20 rounded-lg transition-all">
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                <i class="fas fa-users text-orange-500 mr-2"></i>Shinobi
                            </span>
                            <span class="font-bold text-orange-600 dark:text-orange-400 text-lg">{{ \App\Models\User::count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let selectedMedia = null;

    document.addEventListener('DOMContentLoaded', function() {
        const contentTextarea = document.getElementById('content');
        const charCount = document.getElementById('charCount');
        const postPreview = document.getElementById('postPreview');
        const previewContent = document.getElementById('previewContent');
        const previewBtn = document.getElementById('previewBtn');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('createPostForm');
        const mediaUploadArea = document.getElementById('mediaUploadArea');

        // Character counter with animation
        function updateCharCount() {
            const length = contentTextarea.value.length;
            charCount.textContent = length;
            
            if (length > 900) {
                charCount.className = 'text-red-600 font-bold animate-pulse';
            } else if (length > 750) {
                charCount.className = 'text-orange-500 font-bold';
            } else {
                charCount.className = 'text-gray-500 dark:text-gray-400 font-bold';
            }
        }

        contentTextarea.addEventListener('input', updateCharCount);
        updateCharCount();

        // Drag and drop functionality
        mediaUploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-orange-500', 'bg-orange-50/50', 'dark:bg-orange-900/20');
        });

        mediaUploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-orange-500', 'bg-orange-50/50', 'dark:bg-orange-900/20');
        });

        mediaUploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-orange-500', 'bg-orange-50/50', 'dark:bg-orange-900/20');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleMediaFile(files[0]);
            }
        });

        // Preview functionality
        previewBtn.addEventListener('click', function() {
            const content = contentTextarea.value.trim();
            const hasMedia = selectedMedia !== null;
            
            if (content.length === 0 && !hasMedia) {
                showToast('Please enter some content or add media to preview.', 'warning');
                return;
            }

            previewContent.textContent = content || '(No text content)';
            
            const previewMedia = document.getElementById('previewMedia');
            if (hasMedia) {
                const previewUrl = URL.createObjectURL(selectedMedia);
                previewMedia.innerHTML = `
                    <div class="rounded-xl overflow-hidden border-2 border-orange-300 dark:border-orange-700">
                        ${selectedMedia.type.startsWith('image/') ? 
                            `<img src="${previewUrl}" alt="Preview" class="w-full h-auto max-h-96 object-contain bg-gray-100 dark:bg-gray-800">` :
                            `<div class="relative bg-black">
                                <video src="${previewUrl}" controls class="w-full h-auto max-h-96"></video>
                             </div>`
                        }
                    </div>
                `;
                previewMedia.classList.remove('hidden');
            } else {
                previewMedia.classList.add('hidden');
            }

            postPreview.classList.remove('hidden');
            postPreview.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });

        // Form submission
        form.addEventListener('submit', function(e) {
            const content = contentTextarea.value.trim();
            const hasMedia = selectedMedia !== null;
            
            if (content.length === 0 && !hasMedia) {
                e.preventDefault();
                showToast('Please enter some content or add media for your jutsu.', 'error');
                return;
            }

            if (content.length > 1000) {
                e.preventDefault();
                showToast('Jutsu content cannot exceed 1000 characters.', 'error');
                return;
            }

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Publishing Jutsu...';
        });
    });

    function handleMediaSelect(event) {
        const file = event.target.files[0];
        if (file) {
            handleMediaFile(file);
        }
    }

    function handleMediaFile(file) {
        const validImageTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        const validVideoTypes = ['video/mp4', 'video/mov', 'video/avi'];
        const maxSize = 100 * 1024 * 1024;

        if (!validImageTypes.includes(file.type) && !validVideoTypes.includes(file.type)) {
            showToast('Please select a valid image or video file.', 'error');
            return;
        }

        if (file.size > maxSize) {
            showToast('File size must be less than 100MB.', 'error');
            return;
        }

        selectedMedia = file;
        const previewUrl = URL.createObjectURL(file);
        
        document.getElementById('uploadPlaceholder').classList.add('hidden');
        document.getElementById('mediaPreview').classList.remove('hidden');
        
        document.getElementById('previewFileName').textContent = file.name;
        document.getElementById('previewFileSize').textContent = formatFileSize(file.size);
        document.getElementById('previewFileType').textContent = file.type.startsWith('image/') ? 'Summoning Scroll' : 'Forbidden Scroll';
        
        const previewThumbnail = document.getElementById('previewThumbnail');
        const mediaDisplay = document.getElementById('mediaDisplay');
        
        if (file.type.startsWith('image/')) {
            previewThumbnail.innerHTML = `<img src="${previewUrl}" alt="Preview" class="w-full h-full object-cover">`;
            mediaDisplay.innerHTML = `<img src="${previewUrl}" alt="Preview" class="w-full h-auto max-h-80 object-contain bg-gray-100 dark:bg-gray-800">`;
        } else {
            previewThumbnail.innerHTML = `<i class="fas fa-video text-orange-400 text-3xl"></i>`;
            mediaDisplay.innerHTML = `<video src="${previewUrl}" controls class="w-full h-auto max-h-80"></video>`;
        }
    }

    function removeMedia() {
        if (selectedMedia) {
            URL.revokeObjectURL(selectedMedia.preview);
            selectedMedia = null;
        }
        
        document.getElementById('media').value = '';
        document.getElementById('uploadPlaceholder').classList.remove('hidden');
        document.getElementById('mediaPreview').classList.add('hidden');
        document.getElementById('previewMedia').classList.add('hidden');
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function showToast(message, type = 'info') {
        document.querySelectorAll('.custom-toast').forEach(toast => toast.remove());
        
        const toast = document.createElement('div');
        toast.className = `custom-toast fixed top-4 right-4 px-6 py-3 rounded-xl shadow-2xl z-50 transform transition-all duration-300 backdrop-blur-sm ${
            type === 'success' ? 'bg-green-500/90 text-white' :
            type === 'error' ? 'bg-red-500/90 text-white' :
            'bg-orange-500/90 text-white'
        }`;
        toast.innerHTML = `<div class="flex items-center"><i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'} mr-2"></i>${message}</div>`;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }
</script>

<style>
    /* Custom Font Styles */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');
    
    body {
        font-family: 'Poppins', sans-serif;
    }
    
    h1, h2, h3, .font-bold {
        font-weight: 700;
        letter-spacing: -0.02em;
    }
    
    .akatsuki-btn {
        background: linear-gradient(135deg, #FF6B35 0%, #D32F2F 100%);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .akatsuki-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
    }
    
    .akatsuki-btn:hover::before {
        left: 100%;
    }
    
    .akatsuki-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -5px rgba(211, 47, 47, 0.5);
    }
    
    textarea {
        font-family: 'Poppins', monospace;
        font-size: 15px;
        line-height: 1.6;
    }
    
    .custom-toast {
        transform: translateX(100%);
        transition: all 0.3s ease-in-out;
    }
    
    .custom-toast:not([style*="transform: translateX(0)"]) {
        transform: translateX(100%);
    }
</style>
@endsection