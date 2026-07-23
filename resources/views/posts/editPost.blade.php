@extends('layouts.app')
@section('title', 'Edit Post - ' . config('app.name'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-3xl mx-auto px-4">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                Edit Post
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Update your post content and media
            </p>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-xl">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <span class="text-green-800 dark:text-green-200">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Post Stats -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $post->likes_count ?? 0 }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Likes</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $post->comments_count ?? 0 }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Comments</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $post->views_count ?? 0 }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Views</div>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <form method="POST" action="{{ route('updateData', $post->id) }}" id="editPostForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Original Post Preview -->
                <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Original Post</h3>
                    @if($post->content)
                        <p class="text-gray-700 dark:text-gray-300 italic mb-3">{{ $post->content }}</p>
                    @endif
                    @if($post->has_media)
                        <div class="mt-3">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Media:</p>
                            @if($post->is_image)
                                <img src="{{ $post->media_url }}" alt="Current image" class="w-32 h-32 object-cover rounded-lg border border-gray-300 dark:border-gray-600">
                            @elseif($post->is_video)
                                <div class="w-32 h-32 bg-black rounded-lg border border-gray-300 dark:border-gray-600 flex items-center justify-center">
                                    <i class="fas fa-video text-white text-2xl"></i>
                                </div>
                            @endif
                        </div>
                    @endif
                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                        Posted {{ $post->created_at->diffForHumans() }}
                    </div>
                </div>

                <!-- Content Field -->
                <div class="mb-6">
                    <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Edit Your Post Content
                    </label>
                    <textarea
                        name="content"
                        id="content"
                        rows="6"
                        placeholder="What's on your mind?..."
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none"
                        maxlength="1000"
                    >{{ old('content', $post->content) }}</textarea>
                    
                    <!-- Character Counter -->
                    <div class="flex justify-between items-center mt-2">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <span id="charCount">0</span>/1000 characters
                        </div>
                        @error('content')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Media Management -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Manage Media
                    </label>

                    <!-- Current Media -->
                    @if($post->has_media)
                        <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400">Current Media</h4>
                                <div class="flex items-center space-x-2">
                                    <input type="checkbox" name="remove_media" id="remove_media" class="rounded border-gray-300">
                                    <label for="remove_media" class="text-sm text-red-600 dark:text-red-400 cursor-pointer">Remove Media</label>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-4">
                                @if($post->is_image)
                                    <img src="{{ $post->media_url }}" alt="Current image" class="w-20 h-20 object-cover rounded-lg border border-gray-300 dark:border-gray-600">
                                    <div>
                                        <p class="text-sm text-gray-700 dark:text-gray-300">Image</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Click to view full size</p>
                                    </div>
                                @elseif($post->is_video)
                                    <div class="w-20 h-20 bg-black rounded-lg border border-gray-300 dark:border-gray-600 flex items-center justify-center">
                                        <i class="fas fa-video text-white text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-700 dark:text-gray-300">Video</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Click to play</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- New Media Upload -->
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center transition-all duration-200 hover:border-blue-400 dark:hover:border-blue-600"
                         id="mediaUploadArea">
                        <div id="uploadPlaceholder">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-3"></i>
                            <p class="text-gray-500 dark:text-gray-400 mb-2">
                                {{ $post->has_media ? 'Replace current media or drag & drop new file' : 'Drag & drop images or videos here' }}
                            </p>
                            <p class="text-sm text-gray-400 dark:text-gray-500 mb-4">
                                Supports: JPG, PNG, GIF, MP4, MOV, AVI (Max: 100MB)
                            </p>
                            <button type="button" 
                                    onclick="document.getElementById('media').click()"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                                <i class="fas fa-plus mr-2"></i>
                                Choose File
                            </button>
                        </div>
                        
                        <!-- New Media Preview -->
                        <div id="mediaPreview" class="hidden">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <div id="previewThumbnail" class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden flex items-center justify-center">
                                        <i class="fas fa-file text-gray-400"></i>
                                    </div>
                                    <div>
                                        <p id="previewFileName" class="font-medium text-gray-900 dark:text-white"></p>
                                        <p id="previewFileSize" class="text-sm text-gray-500 dark:text-gray-400"></p>
                                        <p id="previewFileType" class="text-xs text-blue-600 dark:text-blue-400"></p>
                                    </div>
                                </div>
                                <button type="button" 
                                        onclick="removeMedia()"
                                        class="p-2 text-red-600 hover:text-red-700 transition-colors duration-200">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            
                            <!-- New Media Display -->
                            <div id="mediaDisplay" class="rounded-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                                <!-- New media will be displayed here -->
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

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-end pt-4 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('show.post') }}" 
                       class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Feed
                    </a>
                    <button type="submit" 
                            id="submitBtn"
                            class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                        <i class="fas fa-save mr-2"></i>
                        Update Post
                    </button>
                </div>
            </form>
        </div>

        <!-- Danger Zone -->
        <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-red-200 dark:border-red-800 p-6">
            <h3 class="text-lg font-semibold text-red-700 dark:text-red-400 mb-4 flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Danger Zone
            </h3>
            
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                <div class="mb-4 sm:mb-0">
                    <h4 class="font-medium text-red-900 dark:text-red-200">Delete This Post</h4>
                    <p class="text-sm text-red-700 dark:text-red-300">Once deleted, this post cannot be recovered.</p>
                </div>
                <button type="button" 
                        onclick="confirmDelete()"
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-all duration-200">
                    <i class="fas fa-trash mr-2"></i>
                    Delete Post
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl p-6 m-4 max-w-sm w-full">
        <div class="text-center">
            <div class="w-16 h-16 mx-auto mb-4 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Delete Post?</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">This action cannot be undone. All likes and comments will also be deleted.</p>
            
            <div class="flex space-x-3">
                <button type="button" 
                        onclick="closeDeleteModal()"
                        class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                    Cancel
                </button>
                <form action="{{ route('deleteData', $post->id) }}" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let selectedMedia = null;

    document.addEventListener('DOMContentLoaded', function() {
        const contentTextarea = document.getElementById('content');
        const charCount = document.getElementById('charCount');
        const form = document.getElementById('editPostForm');
        const submitBtn = document.getElementById('submitBtn');
        const mediaUploadArea = document.getElementById('mediaUploadArea');
        const removeMediaCheckbox = document.getElementById('remove_media');

        // Character counter
        function updateCharCount() {
            const length = contentTextarea.value.length;
            charCount.textContent = length;
            
            if (length > 900) {
                charCount.className = 'text-red-600 font-semibold';
            } else if (length > 750) {
                charCount.className = 'text-orange-500';
            } else {
                charCount.className = 'text-gray-500 dark:text-gray-400';
            }
        }

        contentTextarea.addEventListener('input', updateCharCount);
        updateCharCount(); // Initial count

        // Drag and drop functionality
        mediaUploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-blue-400', 'bg-blue-50', 'dark:bg-blue-900/20');
        });

        mediaUploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('border-blue-400', 'bg-blue-50', 'dark:bg-blue-900/20');
        });

        mediaUploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-blue-400', 'bg-blue-50', 'dark:bg-blue-900/20');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleMediaFile(files[0]);
            }
        });

        // Form submission
        form.addEventListener('submit', function(e) {
            const content = contentTextarea.value.trim();
            const hasMedia = selectedMedia !== null;
            const removeMedia = removeMediaCheckbox ? removeMediaCheckbox.checked : false;
            
            if (content.length === 0 && !hasMedia && !removeMedia) {
                e.preventDefault();
                showToast('Please enter some content, add media, or remove current media.', 'error');
                contentTextarea.focus();
                return;
            }

            if (content.length > 1000) {
                e.preventDefault();
                showToast('Post content cannot exceed 1000 characters.', 'error');
                return;
            }

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating...';
        });

        // Auto-resize textarea
        contentTextarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        // Initial resize
        setTimeout(() => {
            contentTextarea.style.height = 'auto';
            contentTextarea.style.height = (contentTextarea.scrollHeight) + 'px';
        }, 100);
    });

    function handleMediaSelect(event) {
        const file = event.target.files[0];
        if (file) {
            handleMediaFile(file);
        }
    }

    function handleMediaFile(file) {
        // Validate file type and size
        const validImageTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        const validVideoTypes = ['video/mp4', 'video/mov', 'video/avi'];
        const maxSize = 100 * 1024 * 1024; // 100MB

        if (!validImageTypes.includes(file.type) && !validVideoTypes.includes(file.type)) {
            showToast('Please select a valid image or video file.', 'error');
            return;
        }

        if (file.size > maxSize) {
            showToast('File size must be less than 100MB.', 'error');
            return;
        }

        selectedMedia = file;

        // Create preview
        const previewUrl = URL.createObjectURL(file);
        
        // Update UI
        document.getElementById('uploadPlaceholder').classList.add('hidden');
        document.getElementById('mediaPreview').classList.remove('hidden');
        
        document.getElementById('previewFileName').textContent = file.name;
        document.getElementById('previewFileSize').textContent = formatFileSize(file.size);
        document.getElementById('previewFileType').textContent = file.type.startsWith('image/') ? 'Image' : 'Video';
        
        const previewThumbnail = document.getElementById('previewThumbnail');
        const mediaDisplay = document.getElementById('mediaDisplay');
        
        if (file.type.startsWith('image/')) {
            previewThumbnail.innerHTML = `<img src="${previewUrl}" alt="Preview" class="w-full h-full object-cover">`;
            mediaDisplay.innerHTML = `<img src="${previewUrl}" alt="Preview" class="w-full h-auto max-h-80 object-contain bg-gray-100 dark:bg-gray-800">`;
        } else {
            previewThumbnail.innerHTML = `
                <div class="text-center">
                    <i class="fas fa-video text-gray-400 text-2xl mb-1"></i>
                    <p class="text-xs text-gray-500">Video</p>
                </div>
            `;
            mediaDisplay.innerHTML = `
                <div class="relative bg-black">
                    <video src="${previewUrl}" controls class="w-full h-auto max-h-80"></video>
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <i class="fas fa-play text-white text-4xl opacity-80"></i>
                    </div>
                </div>
            `;
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
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function confirmDelete() {
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            type === 'warning' ? 'bg-yellow-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 4000);
    }

    // Close modal when clicking outside
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });

    // Escape key to close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDeleteModal();
        }
    });
</script>

<style>
    textarea {
        min-height: 120px;
        transition: height 0.2s ease;
    }
</style>
@endsection