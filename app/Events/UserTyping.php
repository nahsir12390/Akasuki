<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public User $sender,
        public int $receiverId,
        public bool $typing,
    ) {
    }

    public function broadcastOn(): PrivateChannel
    {
        $user1 = min($this->sender->id, $this->receiverId);
        $user2 = max($this->sender->id, $this->receiverId);

        return new PrivateChannel("chat.{$user1}.{$user2}");
    }

    public function broadcastAs(): string
    {
        return 'typing.status';
    }

    public function broadcastWith(): array
    {
        return [
            'sender_id' => $this->sender->id,
            'receiver_id' => $this->receiverId,
            'name' => $this->sender->name,
            'typing' => $this->typing,
        ];
    }
}
