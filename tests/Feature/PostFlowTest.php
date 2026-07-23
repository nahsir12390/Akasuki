<?php

use App\Models\Post;
use App\Models\User;

test('an authenticated user can create a text post', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('storeData'), [
            'content' => 'I will become Hokage.',
        ])
        ->assertRedirect(route('show.post'));

    $this->assertDatabaseHas('posts', [
        'user_id' => $user->id,
        'content' => 'I will become Hokage.',
    ]);
});

test('a user can update their own post', function () {
    $user = User::factory()->create();
    $post = Post::create([
        'user_id' => $user->id,
        'content' => 'Old mission report.',
    ]);

    $this->actingAs($user)
        ->put(route('updateData', $post), [
            'content' => 'Updated mission report.',
        ])
        ->assertRedirect(route('show.post'));

    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'content' => 'Updated mission report.',
    ]);
});

test('a user cannot edit another users post', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::create([
        'user_id' => $owner->id,
        'content' => 'Private training notes.',
    ]);

    $this->actingAs($otherUser)
        ->put(route('updateData', $post), [
            'content' => 'Tampered content.',
        ])
        ->assertForbidden();

    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'content' => 'Private training notes.',
    ]);
});

test('a user cannot delete another users post', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $post = Post::create([
        'user_id' => $owner->id,
        'content' => 'Do not delete this.',
    ]);

    $this->actingAs($otherUser)
        ->delete(route('deleteData', $post))
        ->assertForbidden();

    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
    ]);
});

test('a user can delete their own post', function () {
    $user = User::factory()->create();
    $post = Post::create([
        'user_id' => $user->id,
        'content' => 'Temporary post.',
    ]);

    $this->actingAs($user)
        ->delete(route('deleteData', $post))
        ->assertRedirect(route('show.post'));

    $this->assertDatabaseMissing('posts', [
        'id' => $post->id,
    ]);
});
