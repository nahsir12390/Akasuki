<?php

namespace App\Livewire;

use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class PostLikeButton extends Component
{
    public Post $post;
    public int $likesCount = 0;
    public bool $isLiked = false;
    public bool $fullWidth = true;
    public bool $showCount = true;
    public bool $showButton = true;

    public function mount(Post $post, bool $fullWidth = true, bool $showCount = true, bool $showButton = true): void
    {
        $this->post = $post;
        $this->fullWidth = $fullWidth;
        $this->showCount = $showCount;
        $this->showButton = $showButton;
        $this->syncState();
    }

    public function setLike(bool $shouldLike): void
    {
        abort_unless(auth()->check(), 403);

        DB::transaction(function () use ($shouldLike): void {
            if ($shouldLike) {
                $this->post->likes()->firstOrCreate([
                    'user_id' => auth()->id(),
                ]);

                return;
            }

            $this->post->likes()
                ->where('user_id', auth()->id())
                ->delete();
        });

        $this->syncState();

        $this->dispatch('post-like-updated', postId: $this->post->id);
    }

    public function toggleLike(): void
    {
        $this->setLike(!$this->isLiked);
    }

    #[On('post-like-updated')]
    public function handleLikeUpdated(int $postId): void
    {
        if ($postId !== $this->post->id) {
            return;
        }

        $this->syncState();
    }

    private function syncState(): void
    {
        $this->likesCount = $this->post->likes()->count();
        $this->isLiked = $this->post->likes()
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function render()
    {
        return view('livewire.post-like-button');
    }
}
