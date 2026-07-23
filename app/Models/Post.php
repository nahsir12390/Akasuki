<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['user_id', 'content', 'media_path', 'media_type', 'thumbnail_path'];

    use HasFactory;

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function likes() {
        return $this->hasMany(Like::class);
    }

    public function isLikedBy($user) {
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function views() {
        // Your views logic here
    }

    // Accessor for media URL
    public function getMediaUrlAttribute()
    {
        return $this->media_path ? asset('storage/' . $this->media_path) : null;
    }

    // Accessor for thumbnail URL
    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail_path ? asset('storage/' . $this->thumbnail_path) : null;
    }

    // Check if post has media
    public function getHasMediaAttribute()
    {
        return !is_null($this->media_path);
    }

    // Check if post is video
    public function getIsVideoAttribute()
    {
        return $this->media_type === 'video';
    }

    // Check if post is image
    public function getIsImageAttribute()
    {
        return $this->media_type === 'image';
    }
}