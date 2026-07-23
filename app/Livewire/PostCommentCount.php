<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Attributes\On;
use Livewire\Component;

class PostCommentCount extends Component
{
    public Post $post;
    public int $commentsCount = 0;

    public function mount(Post $post): void
    {
        $this->post = $post;
        $this->syncCount();
    }

    #[On('post-comments-updated')]
    public function handleCommentsUpdated(int $postId): void
    {
        if ($postId !== $this->post->id) {
            return;
        }

        $this->syncCount();
    }

    private function syncCount(): void
    {
        $this->commentsCount = $this->post->comments()->count();
    }

    public function render()
    {
        return view('livewire.post-comment-count');
    }
}