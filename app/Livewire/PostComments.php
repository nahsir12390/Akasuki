<?php

namespace App\Livewire;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PostComments extends Component
{
    public Post $post;
    public string $newCommentBody = '';
    public ?int $editingCommentId = null;
    public string $editingCommentBody = '';

    protected $listeners = ['refreshComments' => '$refresh', 'commentAdded' => 'handleCommentAdded'];

    public function mount(Post $post): void
    {
        $this->post = $post;
    }

    public function addComment(): void
    {
        abort_unless(auth()->check(), 403);

        $validated = $this->validate([
            'newCommentBody' => 'required|string|min:1|max:1000',
        ]);

        DB::transaction(function () use ($validated): void {
            $this->post->comments()->create([
                'user_id' => auth()->id(),
                'body' => trim($validated['newCommentBody']),
            ]);
        });

        // Clear the comment body using reset
        $this->reset('newCommentBody');
        
        // Force a fresh state
        $this->dispatch('post-comments-updated', postId: $this->post->id);
        
        // Emit event for JavaScript to clear the textarea if needed
        $this->dispatch('comment-added', postId: $this->post->id);
    }

    public function startEditing(int $commentId): void
    {
        $comment = Comment::where('post_id', $this->post->id)->findOrFail($commentId);
        abort_unless(auth()->id() === $comment->user_id, 403);

        $this->editingCommentId = $comment->id;
        $this->editingCommentBody = $comment->body;
    }

    public function cancelEditing(): void
    {
        $this->reset('editingCommentId', 'editingCommentBody');
    }

    public function updateComment(): void
    {
        abort_unless(auth()->check(), 403);
        abort_unless($this->editingCommentId !== null, 422);

        $validated = $this->validate([
            'editingCommentBody' => 'required|string|min:1|max:1000',
        ]);

        DB::transaction(function () use ($validated): void {
            $comment = Comment::where('post_id', $this->post->id)->findOrFail($this->editingCommentId);
            abort_unless(auth()->id() === $comment->user_id, 403);

            $comment->update([
                'body' => trim($validated['editingCommentBody']),
            ]);
        });

        $this->cancelEditing();

        $this->dispatch('post-comments-updated', postId: $this->post->id);
    }

    public function deleteComment(int $commentId): void
    {
        abort_unless(auth()->check(), 403);

        DB::transaction(function () use ($commentId): void {
            $comment = Comment::where('post_id', $this->post->id)->findOrFail($commentId);
            abort_unless(auth()->id() === $comment->user_id, 403);
            $comment->delete();
        });

        if ($this->editingCommentId === $commentId) {
            $this->cancelEditing();
        }

        $this->dispatch('post-comments-updated', postId: $this->post->id);
    }

    public function handleCommentAdded()
    {
        $this->reset('newCommentBody');
    }

    public function render()
    {
        return view('livewire.post-comments', [
            'comments' => $this->post->comments()->with('user')->latest()->get(),
            'commentsCount' => $this->post->comments()->count(),
        ]);
    }
}