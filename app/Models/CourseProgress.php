<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseProgress extends Model
{
    use HasFactory;

    protected $table = 'course_progress';

    protected $fillable = [
        'user_id',
        'course_id',
        'completed_modules',
        'completed_checklist',
        'completed_projects',
        'percent',
        'completed_at',
    ];

    protected $casts = [
        'completed_modules' => 'array',
        'completed_checklist' => 'array',
        'completed_projects' => 'array',
        'percent' => 'integer',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function rank(): string
    {
        return match (true) {
            $this->percent >= 100 => 'Jonin',
            $this->percent >= 60 => 'Chunin',
            $this->percent >= 25 => 'Genin',
            default => 'Academy',
        };
    }
}
