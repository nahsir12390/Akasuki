<?php

use App\Models\Friendship;
use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessageNotification;
use Illuminate\Support\Facades\Event;

test('a user can view message notifications', function () {
    Event::fake();

    $sender = User::factory()->create(['name' => 'Kakashi']);
    $receiver = User::factory()->create(['email_messages' => false]);
    Friendship::create([
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'status' => Friendship::STATUS_ACCEPTED,
    ]);

    $this->actingAs($sender)
        ->postJson(route('chat.send'), [
            'receiver_id' => $receiver->id,
            'message' => 'Meet at the training field.',
        ])
        ->assertOk();

    $this->actingAs($receiver)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertSee('New squad message')
        ->assertSee('Kakashi');
});

test('the notification center shows device notification setup', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertSee('Notification setup')
        ->assertSee('Device Alerts')
        ->assertSee('Enable');
});

test('opening a notification marks it as read and redirects to its action', function () {
    $sender = User::factory()->create();
    $receiver = User::factory()->create(['email_messages' => false]);
    $message = Message::create([
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'message' => 'Open this.',
    ]);
    $message->load('sender');
    $receiver->notify(new NewMessageNotification($message));

    $notification = $receiver->notifications()->first();

    $this->actingAs($receiver)
        ->patch(route('notifications.read', $notification))
        ->assertRedirect(route('chat.index', ['user' => $sender->id]));

    expect($notification->fresh()->read_at)->not->toBeNull();
});

test('users cannot manage another users notification', function () {
    $owner = User::factory()->create(['email_messages' => false]);
    $other = User::factory()->create();
    $sender = User::factory()->create();
    $message = Message::create([
        'sender_id' => $sender->id,
        'receiver_id' => $owner->id,
        'message' => 'Private signal.',
    ]);
    $message->load('sender');
    $owner->notify(new NewMessageNotification($message));

    $notification = $owner->notifications()->first();

    $this->actingAs($other)
        ->patch(route('notifications.read', $notification))
        ->assertForbidden();

    $this->actingAs($other)
        ->delete(route('notifications.destroy', $notification))
        ->assertForbidden();
});

test('friendship actions create database notifications', function () {
    $sender = User::factory()->create(['name' => 'Naruto']);
    $receiver = User::factory()->create(['name' => 'Sasuke']);

    $this->actingAs($sender)
        ->post(route('friends.send', $receiver))
        ->assertRedirect();

    expect($receiver->notifications()->where('data->kind', 'friend_request')->exists())->toBeTrue();

    $this->actingAs($receiver)
        ->post(route('friends.accept', $sender))
        ->assertRedirect();

    expect($sender->notifications()->where('data->kind', 'friend_accept')->exists())->toBeTrue();
});
