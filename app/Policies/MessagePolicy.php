<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;

class MessagePolicy
{
    public function create(User $user, User $receiver): bool
    {
        return $user->id !== $receiver->id
            && $user->isFriendWith($receiver->id);
    }

    public function viewConversation(User $user, User $otherUser): bool
    {
        return $user->id !== $otherUser->id
            && $user->isFriendWith($otherUser->id);
    }
}
