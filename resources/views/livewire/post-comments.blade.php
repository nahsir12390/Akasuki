<div class="comments-section" wire:key="comments-section-{{ $post->id }}">
    <div class="flex items-center justify-between mb-4">
        <h5 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
            <i class="fas fa-comments mr-2 text-orange-500"></i>
            <span class="bg-gradient-to-r from-orange-500 to-red-500 bg-clip-text text-transparent">Comments</span>
            @if($commentsCount > 0)
                <span class="ml-2 px-2 py-1 bg-gradient-to-r from-orange-100 to-red-100 dark:from-orange-900/30 dark:to-red-900/30 text-orange-600 dark:text-orange-400 text-sm font-medium rounded-full">
                    {{ $commentsCount }}
                </span>
            @endif
        </h5>
    </div>

    <div class="space-y-3">
        @forelse ($comments as $comment)
            <div class="bg-gradient-to-r from-gray-50 to-orange-50 dark:from-gray-700 dark:to-gray-700 rounded-xl p-4 border border-orange-200 dark:border-orange-800 hover:shadow-md transition-all duration-300" wire:key="comment-{{ $comment->id }}">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('user.profile', $comment->user->id) }}" class="flex-shrink-0 group relative">
                            <div class="absolute inset-0 rounded-full bg-gradient-to-r from-orange-500 to-red-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300 blur-sm"></div>
                            <img 
                                src="{{ $comment->user->profile_photo ? asset('storage/' . $comment->user->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name) . '&background=FF6B35&color=fff&size=32' }}"
                                alt="{{ $comment->user->name }}"
                                class="w-8 h-8 rounded-full object-cover border border-orange-300 dark:border-orange-700 relative z-10">
                        </a>
                        <div>
                            <a href="{{ route('user.profile', $comment->user->id) }}"
                               class="font-medium text-gray-900 dark:text-gray-100 hover:text-orange-600 dark:hover:text-orange-400 transition-colors duration-200 capitalize">
                                {{ $comment->user->name ?? 'Unknown User' }}
                            </a>
                            <div class="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400">
                                <i class="fas fa-cloud-sun text-orange-400 text-xs"></i>
                                <span>{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    @if(auth()->id() == $comment->user_id)
                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="startEditing({{ $comment->id }})" class="text-xs text-orange-600 hover:text-orange-700 transition-colors">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>
                            <button type="button" wire:click="deleteComment({{ $comment->id }})" wire:confirm="Delete this comment?" class="text-xs text-red-600 hover:text-red-700 transition-colors">
                                <i class="fas fa-trash mr-1"></i>Delete
                            </button>
                        </div>
                    @endif
                </div>

                @if($editingCommentId === $comment->id)
                    <div class="mt-2">
                        <textarea wire:model="editingCommentBody" rows="3" class="w-full px-3 py-2 rounded-lg border-2 border-orange-300 dark:border-orange-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none"></textarea>
                        @error('editingCommentBody') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        <div class="flex gap-2 mt-2">
                            <button type="button" wire:click="cancelEditing" class="px-3 py-1 border border-orange-300 dark:border-orange-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-all">
                                Cancel
                            </button>
                            <button type="button" wire:click="updateComment" class="px-3 py-1 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-lg text-sm hover:from-orange-600 hover:to-red-700 transition-all shadow-md">
                                <i class="fas fa-save mr-1"></i>Update
                            </button>
                        </div>
                    </div>
                @else
                    <p class="text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap mt-2">{{ $comment->body }}</p>
                @endif
            </div>
        @empty
            <div class="text-center py-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-100 dark:bg-orange-900/30 rounded-full mb-3">
                    <i class="fas fa-comment-slash text-3xl text-orange-400 dark:text-orange-500"></i>
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-sm">No comments yet. Be the first to share your thoughts!</p>
                <p class="text-xs text-orange-500 dark:text-orange-400 mt-1">"Believe it!"</p>
            </div>
        @endforelse
    </div>

    @auth
        <div class="mt-6">
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <div class="relative group">
                        <div class="absolute inset-0 rounded-full bg-gradient-to-r from-orange-500 to-red-600 opacity-0 group-hover:opacity-100 transition-opacity duration-300 blur-sm"></div>
                        <img 
                            src="{{ auth()->user()->profile_photo ? asset('storage/' . auth()->user()->profile_photo) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=FF6B35&color=fff&size=32' }}"
                            alt="{{ auth()->user()->name }}"
                            class="w-8 h-8 rounded-full object-cover border border-orange-300 dark:border-orange-700 relative z-10">
                    </div>
                </div>
                <div class="flex-1">
                    <textarea 
                        wire:model="newCommentBody" 
                        name="body" 
                        rows="3" 
                        class="w-full px-4 py-3 rounded-xl border-2 border-orange-200 dark:border-orange-800 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none placeholder-gray-400 dark:placeholder-gray-500 transition-all duration-300" 
                        placeholder="Write a comment... Believe it!"
                        maxlength="1000"
                        wire:key="new-comment-textarea-{{ $post->id }}">
                    </textarea>
                    @error('newCommentBody') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <div class="flex justify-end items-center mt-2">
                        <button 
                            type="button" 
                            wire:click="addComment" 
                            wire:loading.attr="disabled" 
                            class="group relative px-4 py-2 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-xl text-sm font-medium hover:from-orange-600 hover:to-red-700 transition-all duration-300 shadow-md hover:shadow-lg disabled:opacity-60 overflow-hidden"
                            wire:key="post-comment-btn-{{ $post->id }}">
                            <div class="absolute inset-0 w-0 bg-white/20 transition-all duration-300 ease-out group-hover:w-full"></div>
                            <span wire:loading.remove wire:target="addComment"><i class="fas fa-paper-plane mr-1 relative z-10"></i>Post Comment</span>
                            <span wire:loading wire:target="addComment"><i class="fas fa-spinner fa-spin mr-1 relative z-10"></i>Posting...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="mt-6 p-4 bg-gradient-to-r from-orange-50 to-red-50 dark:from-gray-700 dark:to-gray-700 rounded-xl border border-orange-200 dark:border-orange-800 text-center">
            <i class="fas fa-cloud-sun text-orange-500 text-2xl mb-2"></i>
            <p class="text-gray-600 dark:text-gray-400 text-sm">
                <a href="{{ route('show.login') }}" class="text-orange-600 dark:text-orange-400 hover:underline font-medium">Sign in</a>
                to join the conversation and become a true shinobi
            </p>
            <p class="text-xs text-orange-500 dark:text-orange-400 mt-1">"The Ninja Way"</p>
        </div>
    @endauth
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:init', function () {
        // Listen for comment added event
        Livewire.on('comment-added', (postId) => {
            // Clear any textarea that might have been preserved
            setTimeout(() => {
                const textareas = document.querySelectorAll('textarea[wire\\:model="newCommentBody"]');
                textareas.forEach(textarea => {
                    if (textarea.value !== '') {
                        textarea.value = '';
                        // Trigger input event to update Livewire
                        textarea.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                });
            }, 50);
        });
    });
</script>
@endpush