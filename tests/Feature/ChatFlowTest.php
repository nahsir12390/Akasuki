<?php

use App\Models\Message;
use App\Models\User;
use App\Models\Friendship;
use App\Notifications\NewMessageNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

function makeFriends(User $user, User $friend): void
{
    Friendship::create([
        'sender_id' => $user->id,
        'receiver_id' => $friend->id,
        'status' => Friendship::STATUS_ACCEPTED,
    ]);
}

test('a user can send a chat message', function () {
    Event::fake();
    Notification::fake();

    $sender = User::factory()->create();
    $receiver = User::factory()->create();
    makeFriends($sender, $receiver);

    $this->actingAs($sender)
        ->postJson(route('chat.send'), [
            'receiver_id' => $receiver->id,
            'message' => 'Meet me at the training field.',
        ])
        ->assertOk()
        ->assertJsonPath('success', true);

    $this->assertDatabaseHas('messages', [
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'message' => 'Meet me at the training field.',
    ]);
});

test('the chat page only lists friends', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create(['name' => 'Visible Friend']);
    $nonFriend = User::factory()->create(['name' => 'Hidden Stranger']);
    makeFriends($user, $friend);

    $response = $this->actingAs($user)
        ->get(route('chat.index'))
        ->assertOk();

    $response->assertSee('Visible Friend');
    $response->assertDontSee('Hidden Stranger');
});

test('chat search only returns friends', function () {
    $user = User::factory()->create();
    $friend = User::factory()->create(['name' => 'Sakura Friend']);
    $nonFriend = User::factory()->create(['name' => 'Sakura Stranger']);
    makeFriends($user, $friend);

    $this->actingAs($user)
        ->getJson(route('chat.search', ['q' => 'Sakura']))
        ->assertOk()
        ->assertJsonFragment(['name' => 'Sakura Friend'])
        ->assertJsonMissing(['name' => 'Sakura Stranger']);
});

test('a user cannot send a chat message to a non friend', function () {
    Event::fake();
    Notification::fake();

    $sender = User::factory()->create();
    $receiver = User::factory()->create();

    $this->actingAs($sender)
        ->postJson(route('chat.send'), [
            'receiver_id' => $receiver->id,
            'message' => 'This should not send.',
        ])
        ->assertForbidden();

    $this->assertDatabaseMissing('messages', [
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
    ]);
});

test('a user cannot send a chat message to themselves', function () {
    Event::fake();
    Notification::fake();

    $user = User::factory()->create();

    $this->actingAs($user)
        ->postJson(route('chat.send'), [
            'receiver_id' => $user->id,
            'message' => 'Talking to myself.',
        ])
        ->assertForbidden();

    $this->assertDatabaseMissing('messages', [
        'sender_id' => $user->id,
        'receiver_id' => $user->id,
    ]);
});

test('a user can load messages from a conversation', function () {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();
    makeFriends($sender, $receiver);

    Message::create([
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'message' => 'First message.',
    ]);

    $this->actingAs($receiver)
        ->getJson(route('chat.load', $sender))
        ->assertOk()
        ->assertJsonFragment([
            'message' => 'First message.',
        ]);
});

test('the chat page counts unread message notifications by sender', function () {
    $sender = User::factory()->create(['name' => 'Kakashi']);
    $receiver = User::factory()->create(['email_messages' => false]);
    makeFriends($sender, $receiver);

    $message = Message::create([
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'message' => 'Unread signal.',
    ]);
    $message->load('sender');
    $receiver->notify(new NewMessageNotification($message));

    $response = $this->actingAs($receiver)
        ->get(route('chat.index'))
        ->assertOk();

    expect($response->viewData('unreadCounts')[$sender->id])->toBe(1);
});

test('loading a conversation marks only that senders message notifications as read', function () {
    $sender = User::factory()->create();
    $otherSender = User::factory()->create();
    $receiver = User::factory()->create(['email_messages' => false]);
    makeFriends($sender, $receiver);
    makeFriends($otherSender, $receiver);

    $message = Message::create([
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'message' => 'Open this conversation.',
    ]);
    $message->load('sender');
    $receiver->notify(new NewMessageNotification($message));

    $otherMessage = Message::create([
        'sender_id' => $otherSender->id,
        'receiver_id' => $receiver->id,
        'message' => 'Keep this unread.',
    ]);
    $otherMessage->load('sender');
    $receiver->notify(new NewMessageNotification($otherMessage));

    $this->actingAs($receiver)
        ->getJson(route('chat.load', $sender))
        ->assertOk();

    $notifications = $receiver->notifications()->get();

    expect($notifications->firstWhere('data.sender_id', $sender->id)->read_at)->not->toBeNull();
    expect($notifications->firstWhere('data.sender_id', $otherSender->id)->read_at)->toBeNull();
});

test('a user cannot load a non friend conversation', function () {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();

    Message::create([
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'message' => 'Hidden message.',
    ]);

    $this->actingAs($receiver)
        ->getJson(route('chat.load', $sender))
        ->assertForbidden();
});

test('message email notifications respect receiver preferences', function () {
    $sender = User::factory()->create();
    $receiver = User::factory()->create(['email_messages' => true]);
    makeFriends($sender, $receiver);

    $message = Message::create([
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'message' => 'Meet me at the training field.',
    ]);
    $message->load('sender');

    expect((new NewMessageNotification($message))->via($receiver))->toContain('mail');

    $receiver->forceFill(['email_messages' => false]);

    expect((new NewMessageNotification($message))->via($receiver))->not->toContain('mail');
});
