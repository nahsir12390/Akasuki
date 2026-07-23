<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable
{
    use HasFactory, HasPushSubscriptions, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo',
        'bio',
        'location',
        'website',
        'last_seen',
        'email_course_updates',
        'email_messages',
        'public_profile',
        'show_online_status',
        'provider',
        'provider_id',
        'avatar',
        'skills',
        'interests',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
        'last_seen' => 'datetime',
        'email_course_updates' => 'boolean',
        'email_messages' => 'boolean',
        'public_profile' => 'boolean',
        'show_online_status' => 'boolean',
        'skills' => 'array',
        'interests' => 'array',
    ];



    // Add this method to check if user is a social login user
    public function isSocialUser()
    {
        return !is_null($this->provider);
    }


    
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

        public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function sentMessages() {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages() {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function likes() {
        return $this->hasMany(Like::class);
    }

    public function sentFriendRequests()
    {
        return $this->hasMany(Friendship::class, 'sender_id');
    }

    public function receivedFriendRequests()
    {
        return $this->hasMany(Friendship::class, 'receiver_id');
    }

    // FIXED: Renamed to getFriends() to avoid conflict
    public function getFriends()
    {
        $acceptedSenders = $this->belongsToMany(User::class, 'friendships', 'receiver_id', 'sender_id')
            ->wherePivot('status', 'accepted')
            ->withTimestamps();

        $acceptedReceivers = $this->belongsToMany(User::class, 'friendships', 'sender_id', 'receiver_id')
            ->wherePivot('status', 'accepted')
            ->withTimestamps();

        return $acceptedSenders->get()->merge($acceptedReceivers->get());
    }

    // Get friendship status with another user
    public function getFriendshipStatus($otherUserId)
    {
        $friendship = Friendship::where(function($query) use ($otherUserId) {
            $query->where('sender_id', $this->id)
                  ->where('receiver_id', $otherUserId);
        })->orWhere(function($query) use ($otherUserId) {
            $query->where('sender_id', $otherUserId)
                  ->where('receiver_id', $this->id);
        })->first();

        if (!$friendship) {
            return 'none';
        }

        if ($friendship->status === 'pending') {
            if ($friendship->sender_id == $this->id) {
                return 'pending_sent';
            } else {
                return 'pending_received';
            }
        }

        return $friendship->status;
    }

    // Check if user is friends with another user
    public function isFriendWith($otherUserId)
    {
        return Friendship::where(function($query) use ($otherUserId) {
            $query->where('sender_id', $this->id)
                  ->where('receiver_id', $otherUserId)
                  ->where('status', 'accepted');
        })->orWhere(function($query) use ($otherUserId) {
            $query->where('sender_id', $otherUserId)
                  ->where('receiver_id', $this->id)
                  ->where('status', 'accepted');
        })->exists();
    }

    // Get pending received requests
    public function pendingFriendRequests()
    {
        return $this->hasMany(Friendship::class, 'receiver_id')
            ->where('status', 'pending');
    }

    // Get pending sent requests
    public function sentPendingRequests()
    {
        return $this->hasMany(Friendship::class, 'sender_id')
            ->where('status', 'pending');
    }

    // Accessor for profile photo URL
 public function getProfilePhotoUrlAttribute()
    {
        // Use social avatar if available
        if ($this->avatar) {
            return $this->avatar;
        }
        
        if ($this->profile_photo) {
            return asset('storage/' . $this->profile_photo);
        }
        
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=0D8ABC&color=fff&size=200';
    }

    // Check if user is online (last activity within 5 minutes)
    public function isOnline()
    {
        if (!$this->last_seen) {
            return false;
        }
        
        return $this->last_seen->gt(now()->subMinutes(5));
    }

    // Update last seen timestamp
    public function updateLastSeen()
    {
        $this->update(['last_seen' => now()]);
    }

    // Get user's initial (for avatar fallback)
    public function getInitialAttribute()
    {
        return Str::upper(Str::substr($this->name, 0, 1));
    }

    // Check if profile is public
    public function isProfilePublic()
    {
        return $this->public_profile ?? true;
    }

    // Get mutual friends count with another user
    public function mutualFriendsCount($otherUserId)
    {
        $userFriends = $this->getFriends()->pluck('id');
        $otherUserFriends = User::find($otherUserId)->getFriends()->pluck('id');
        
        return $userFriends->intersect($otherUserFriends)->count();
    }

    // Get mutual friends with another user
    public function mutualFriends($otherUserId)
    {
        $userFriends = $this->getFriends()->pluck('id');
        $otherUserFriends = User::find($otherUserId)->getFriends()->pluck('id');
        $mutualFriendIds = $userFriends->intersect($otherUserFriends);
        
        return User::whereIn('id', $mutualFriendIds)->get();
    }

public function getTotalLikesAttribute()
{
    if (!$this->relationLoaded('posts')) {
        return $this->posts()->withCount('likes')->get()->sum('likes_count');
    }
    
    return $this->posts->sum('likes_count');
}

/**
 * Calculate total comments across all user posts
 */
public function getTotalCommentsAttribute()
{
    if (!$this->relationLoaded('posts')) {
        return $this->posts()->withCount('comments')->get()->sum('comments_count');
    }
    
    return $this->posts->sum('comments_count');
}

/**
 * Calculate engagement score (likes + comments)
 */
public function getEngagementScoreAttribute()
{
    return $this->total_likes + $this->total_comments;
}

/**
 * Get engagement statistics
 */
public function getEngagementStatsAttribute()
{
    return [
        'total_likes' => $this->total_likes,
        'total_comments' => $this->total_comments,
        'engagement_score' => $this->engagement_score,
        'post_count' => $this->posts->count(),
    ];
}
}
