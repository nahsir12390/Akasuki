<?php

namespace App\Policies;

use App\Models\Friendship;
use App\Models\User;

class FriendshipPolicy
{
    public function send(User $user, User $receiver): bool
    {
        return Friendship::canSendRequest($user->id, $receiver->id);
    }

    public function accept(User $user, Friendship $friendship): bool
    {
        return $friendship->receiver_id === $user->id
            && $friendship->status === Friendship::STATUS_PENDING;
    }

    public function decline(User $user, Friendship $friendship): bool
    {
        return $this->accept($user, $friendship);
    }

    public function cancel(User $user, Friendship $friendship): bool
    {
        return $friendship->sender_id === $user->id
            && $friendship->status === Friendship::STATUS_PENDING;
    }

    public function delete(User $user, Friendship $friendship): bool
    {
        return ($friendship->sender_id === $user->id || $friendship->receiver_id === $user->id)
            && $friendship->status === Friendship::STATUS_ACCEPTED;
    }

    public function block(User $user, User $target): bool
    {
        return $user->id !== $target->id;
    }

    public function unblock(User $user, Friendship $friendship): bool
    {
        return ($friendship->sender_id === $user->id || $friendship->receiver_id === $user->id)
            && $friendship->status === Friendship::STATUS_BLOCKED;
    }
}

