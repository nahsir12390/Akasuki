<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{user1}.{user2}', function (User $user, int $user1, int $user2) {
    $isParticipant = $user->id === $user1 || $user->id === $user2;

    if (!$isParticipant) {
        return false;
    }

    $otherUserId = $user->id === $user1 ? $user2 : $user1;

    return $user->isFriendWith($otherUserId)
        ? ['id' => $user->id, 'name' => $user->name]
        : false;
});

