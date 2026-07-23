<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Friendship extends Model
{
    protected $fillable = ['sender_id', 'receiver_id', 'status'];

    protected $attributes = [
        'status' => 'pending',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_DECLINED = 'declined';
    const STATUS_BLOCKED = 'blocked';

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // Scope for accepted friendships
    public function scopeAccepted($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }

    // Scope for pending friendships
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    // Scope for declined friendships
    public function scopeDeclined($query)
    {
        return $query->where('status', self::STATUS_DECLINED);
    }

    // Scope for blocked friendships
    public function scopeBlocked($query)
    {
        return $query->where('status', self::STATUS_BLOCKED);
    }

    // Scope for active friendships (accepted only)
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }

    // Scope for friendships between two users
    public function scopeBetweenUsers($query, $user1Id, $user2Id)
    {
        return $query->where(function ($q) use ($user1Id, $user2Id) {
            $q->where('sender_id', $user1Id)
              ->where('receiver_id', $user2Id);
        })->orWhere(function ($q) use ($user1Id, $user2Id) {
            $q->where('sender_id', $user2Id)
              ->where('receiver_id', $user1Id);
        });
    }

    // Scope for friendships involving a specific user
    public function scopeInvolving($query, $userId)
    {
        return $query->where('sender_id', $userId)
                    ->orWhere('receiver_id', $userId);
    }

    // Check if friendship is pending
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    // Check if friendship is accepted
    public function isAccepted()
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    // Check if friendship is declined
    public function isDeclined()
    {
        return $this->status === self::STATUS_DECLINED;
    }

    // Check if friendship is blocked
    public function isBlocked()
    {
        return $this->status === self::STATUS_BLOCKED;
    }

    // Accept the friendship request
    public function accept()
    {
        $this->update(['status' => self::STATUS_ACCEPTED]);
        return $this;
    }

    // Decline the friendship request
    public function decline()
    {
        $this->update(['status' => self::STATUS_DECLINED]);
        return $this;
    }

    // Block the user
    public function block()
    {
        $this->update(['status' => self::STATUS_BLOCKED]);
        return $this;
    }

    // Get the other user in the friendship
    public function getOtherUser($userId)
    {
        if ($this->sender_id == $userId) {
            return $this->receiver;
        } elseif ($this->receiver_id == $userId) {
            return $this->sender;
        }
        
        return null;
    }

    // Check if a specific user is the sender
    public function isSender($userId)
    {
        return $this->sender_id == $userId;
    }

    // Check if a specific user is the receiver
    public function isReceiver($userId)
    {
        return $this->receiver_id == $userId;
    }

    // Get the direction of the friendship relative to a user
    public function getDirection($userId)
    {
        if ($this->sender_id == $userId) {
            return 'sent';
        } elseif ($this->receiver_id == $userId) {
            return 'received';
        }
        
        return null;
    }

    // Find friendship between two users
    public static function findFriendship($user1Id, $user2Id)
    {
        return static::betweenUsers($user1Id, $user2Id)->first();
    }

    // Check if two users are friends
    public static function areFriends($user1Id, $user2Id)
    {
        return static::betweenUsers($user1Id, $user2Id)
                    ->where('status', self::STATUS_ACCEPTED)
                    ->exists();
    }

    // Check if there's a pending request between two users
    public static function hasPendingRequest($user1Id, $user2Id)
    {
        return static::betweenUsers($user1Id, $user2Id)
                    ->where('status', self::STATUS_PENDING)
                    ->exists();
    }

    // Get all friends for a user (accepted friendships only)
    public static function getFriends($userId)
    {
        $sentFriendships = static::where('sender_id', $userId)
                                ->accepted()
                                ->with('receiver')
                                ->get()
                                ->pluck('receiver');

        $receivedFriendships = static::where('receiver_id', $userId)
                                    ->accepted()
                                    ->with('sender')
                                    ->get()
                                    ->pluck('sender');

        return $sentFriendships->merge($receivedFriendships);
    }

    // Get friends with pagination
    public static function getFriendsPaginated($userId, $perPage = 15)
    {
        $sentFriendships = static::where('sender_id', $userId)
                                ->accepted()
                                ->with('receiver');

        $receivedFriendships = static::where('receiver_id', $userId)
                                    ->accepted()
                                    ->with('sender');

        // Combine both queries using union (more efficient for pagination)
        $friendsQuery = User::whereIn('id', function($query) use ($userId) {
            $query->select('receiver_id')
                  ->from('friendships')
                  ->where('sender_id', $userId)
                  ->where('status', self::STATUS_ACCEPTED);
        })->orWhereIn('id', function($query) use ($userId) {
            $query->select('sender_id')
                  ->from('friendships')
                  ->where('receiver_id', $userId)
                  ->where('status', self::STATUS_ACCEPTED);
        });

        return $friendsQuery->paginate($perPage);
    }

    // Get pending received requests for a user
    public static function getPendingRequests($userId)
    {
        return static::where('receiver_id', $userId)
                    ->pending()
                    ->with('sender')
                    ->get();
    }

    // Get pending sent requests for a user
    public static function getSentRequests($userId)
    {
        return static::where('sender_id', $userId)
                    ->pending()
                    ->with('receiver')
                    ->get();
    }

    // Get friendship status between two users
    public static function getStatus($user1Id, $user2Id)
    {
        $friendship = static::findFriendship($user1Id, $user2Id);

        if (!$friendship) {
            return 'none';
        }

        if ($friendship->isPending()) {
            return $friendship->sender_id == $user1Id ? 'pending_sent' : 'pending_received';
        }

        return $friendship->status;
    }

    // Create a new friendship request
    public static function createRequest($senderId, $receiverId)
    {
        // Check if friendship already exists
        $existingFriendship = static::findFriendship($senderId, $receiverId);
        
        if ($existingFriendship) {
            // If it's declined, update to pending
            if ($existingFriendship->isDeclined()) {
                $existingFriendship->update(['status' => self::STATUS_PENDING]);
                return $existingFriendship;
            }
            return $existingFriendship;
        }

        return static::create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'status' => self::STATUS_PENDING,
        ]);
    }

    // Remove friendship (unfriend)
    public static function removeFriendship($user1Id, $user2Id)
    {
        return static::betweenUsers($user1Id, $user2Id)->delete();
    }

    // Get mutual friends between two users
    public static function getMutualFriends($user1Id, $user2Id)
    {
        $user1Friends = static::getFriends($user1Id)->pluck('id');
        $user2Friends = static::getFriends($user2Id)->pluck('id');
        
        $mutualFriendIds = $user1Friends->intersect($user2Friends);
        
        return User::whereIn('id', $mutualFriendIds)->get();
    }

    // Get mutual friends count
    public static function getMutualFriendsCount($user1Id, $user2Id)
    {
        return static::getMutualFriends($user1Id, $user2Id)->count();
    }

    // Check if user can send friend request to another user
    public static function canSendRequest($senderId, $receiverId)
    {
        // Can't send to yourself
        if ($senderId == $receiverId) {
            return false;
        }

        $existingFriendship = static::findFriendship($senderId, $receiverId);
        
        if (!$existingFriendship) {
            return true;
        }

        // Can resend if previously declined
        return $existingFriendship->isDeclined();
    }

    // Get friendship statistics for a user
    public static function getUserStats($userId)
    {
        return [
            'friends_count' => static::where(function($query) use ($userId) {
                $query->where('sender_id', $userId)
                      ->orWhere('receiver_id', $userId);
            })->where('status', self::STATUS_ACCEPTED)->count(),
            
            'pending_received_count' => static::where('receiver_id', $userId)
                                            ->where('status', self::STATUS_PENDING)
                                            ->count(),
            
            'pending_sent_count' => static::where('sender_id', $userId)
                                        ->where('status', self::STATUS_PENDING)
                                        ->count(),
        ];
    }

    // Get recent friendships
    public static function getRecentFriendships($userId, $limit = 10)
    {
        return static::where(function($query) use ($userId) {
            $query->where('sender_id', $userId)
                  ->orWhere('receiver_id', $userId);
        })->where('status', self::STATUS_ACCEPTED)
          ->with(['sender', 'receiver'])
          ->latest()
          ->limit($limit)
          ->get()
          ->map(function($friendship) use ($userId) {
              return $friendship->getOtherUser($userId);
          });
    }
}