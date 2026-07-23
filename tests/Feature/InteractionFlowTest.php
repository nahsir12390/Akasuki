<?php

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use App\Livewire\PostLikeButton;
use Livewire\Livewire;

test('a user can comment on a post', function () {
    $user = User::factory()->create();
    $post = Post::create([
        'user_id' => User::factory()->create()->id,
        'content' => 'What is your ninja way?',
    ]);

    $this->actingAs($user)
        ->post(route('comments.store', $post), [
            'body' => 'Never giving up.',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('comments', [
        'user_id' => $user->id,
        'post_id' => $post->id,
        'body' => 'Never giving up.',
    ]);
});

test('only the comment owner can update a comment', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::create([
        'user_id' => $owner->id,
        'content' => 'Original post.',
    ]);
    $comment = Comment::create([
        'user_id' => $owner->id,
        'post_id' => $post->id,
        'body' => 'Original comment.',
    ]);

    $this->actingAs($otherUser)
        ->put(route('comments.update', $comment), [
            'body' => 'Changed by someone else.',
        ])
        ->assertForbidden();

    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
        'body' => 'Original comment.',
    ]);
});

test('a user can like and unlike a post', function () {
    $user = User::factory()->create();
    $post = Post::create([
        'user_id' => User::factory()->create()->id,
        'content' => 'Likeable post.',
    ]);

    $this->actingAs($user)
        ->post(route('posts.like', $post), [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'])
        ->assertOk()
        ->assertJsonPath('success', true);

    expect(Like::where('user_id', $user->id)->where('post_id', $post->id)->exists())->toBeTrue();

    $this->actingAs($user)
        ->delete(route('posts.unlike', $post), [], ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'])
        ->assertOk()
        ->assertJsonPath('success', true);

    expect(Like::where('user_id', $user->id)->where('post_id', $post->id)->exists())->toBeFalse();
});

test('post like button keeps repeated like requests liked', function () {
    $user = User::factory()->create();
    $post = Post::create([
        'user_id' => User::factory()->create()->id,
        'content' => 'Likeable post.',
    ]);

    Livewire::actingAs($user)
        ->test(PostLikeButton::class, ['post' => $post])
        ->call('setLike', true)
        ->call('setLike', true)
        ->assertSet('isLiked', true)
        ->assertSet('likesCount', 1);

    expect(Like::where('user_id', $user->id)->where('post_id', $post->id)->count())->toBe(1);
});

test('post like button keeps repeated unlike requests unliked', function () {
    $user = User::factory()->create();
    $post = Post::create([
        'user_id' => User::factory()->create()->id,
        'content' => 'Likeable post.',
    ]);

    Like::create([
        'user_id' => $user->id,
        'post_id' => $post->id,
    ]);

    Livewire::actingAs($user)
        ->test(PostLikeButton::class, ['post' => $post])
        ->call('setLike', false)
        ->call('setLike', false)
        ->assertSet('isLiked', false)
        ->assertSet('likesCount', 0);

    expect(Like::where('user_id', $user->id)->where('post_id', $post->id)->exists())->toBeFalse();
});
