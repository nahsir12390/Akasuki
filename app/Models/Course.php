<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'icon',
        'level',
        'duration',
        'query',
        'youtube_url',
        'modules',
        'projects',
        'checklist',
        'resources',
        'quiz_questions',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'modules' => 'array',
        'projects' => 'array',
        'checklist' => 'array',
        'resources' => 'array',
        'quiz_questions' => 'array',
        'is_active' => 'boolean',
    ];

    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        if (!$this->youtube_url) {
            return null;
        }

        if (preg_match('/youtu\.be\/([A-Za-z0-9_-]+)/', $this->youtube_url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        if (preg_match('/youtube\.com\/watch\?v=([A-Za-z0-9_-]+)/', $this->youtube_url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        if (preg_match('/youtube\.com\/embed\/([A-Za-z0-9_-]+)/', $this->youtube_url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }

        return null;
    }
}
