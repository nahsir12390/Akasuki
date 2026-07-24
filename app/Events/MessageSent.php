<?php

namespace App\Events;

use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message;

        if (!$this->message->relationLoaded('sender')) {
            $this->message->load('sender');
        }
    }

    public function broadcastOn(): PrivateChannel
    {
        $user1 = min($this->message->sender_id, $this->message->receiver_id);
        $user2 = max($this->message->sender_id, $this->message->receiver_id);

        return new PrivateChannel("chat.{$user1}.{$user2}");
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        $receiver = User::find($this->message->receiver_id);

        $payload = [
            'id' => $this->message->id,
            'message' => $this->message->message,
            'sender_id' => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
            'sender' => [
                'id' => $this->message->sender->id,
                'name' => $this->message->sender->name,
                'profile_photo_url' => $this->message->sender->profile_photo_url,
            ],
            'created_at' => $this->message->created_at->format('h:i A'),
            'date' => $this->message->created_at->format('M j, Y'),
        ];

        if ($receiver) {
            $payload['receiver_name'] = $receiver->name;
            $payload['receiver_avatar'] = $receiver->profile_photo_url;
            $payload['receiver_online'] = $receiver->isOnline();
        }

        return $payload;
    }

    public function broadcastWhen(): bool
    {
        return !is_null($this->message->sender);
    }
}
