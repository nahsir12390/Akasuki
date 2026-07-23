<?php

use App\Models\Friendship;
use App\Models\User;

test('a user can send and another user can accept a friend request', function () {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();

    $this->actingAs($sender)
        ->post(route('friends.send', $receiver))
        ->assertRedirect();

    $this->assertDatabaseHas('friendships', [
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'status' => Friendship::STATUS_PENDING,
    ]);

    $this->actingAs($receiver)
        ->post(route('friends.accept', $sender))
        ->assertRedirect();

    $this->assertDatabaseHas('friendships', [
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'status' => Friendship::STATUS_ACCEPTED,
    ]);
});

test('a user cannot send a friend request to themselves', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('friends.send', $user))
        ->assertForbidden();

    $this->assertDatabaseMissing('friendships', [
        'sender_id' => $user->id,
        'receiver_id' => $user->id,
    ]);
});

test('a user cannot accept a friend request sent to someone else', function () {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();
    $otherUser = User::factory()->create();

    Friendship::create([
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'status' => Friendship::STATUS_PENDING,
    ]);

    $this->actingAs($otherUser)
        ->post(route('friends.accept', $sender))
        ->assertNotFound();

    $this->assertDatabaseHas('friendships', [
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'status' => Friendship::STATUS_PENDING,
    ]);
});
