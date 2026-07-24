<?php

namespace App\Services;

use App\Models\User;

class AchievementService
{
    public static function sync(User $user): void
    {
        foreach (self::definitions() as $slug => $achievement) {
            if (($achievement['earned'])($user)) {
                $user->achievements()->firstOrCreate(
                    ['slug' => $slug],
                    [
                        'title' => $achievement['title'],
                        'description' => $achievement['description'],
                        'icon' => $achievement['icon'],
                        'unlocked_at' => now(),
                    ]
                );
            }
        }
    }

    public static function definitions(): array
    {
        return [
            'first_scroll' => [
                'title' => 'First Scroll',
                'description' => 'Published the first village post.',
                'icon' => 'fas fa-scroll',
                'earned' => fn (User $user): bool => $user->posts()->exists(),
            ],
            'first_ally' => [
                'title' => 'Squad Formed',
                'description' => 'Connected with at least one ally.',
                'icon' => 'fas fa-user-group',
                'earned' => fn (User $user): bool => $user->getFriends()->isNotEmpty(),
            ],
            'first_progress' => [
                'title' => 'Training Started',
                'description' => 'Made progress on a course scroll.',
                'icon' => 'fas fa-route',
                'earned' => fn (User $user): bool => $user->courseProgress()->where('percent', '>', 0)->exists(),
            ],
            'course_complete' => [
                'title' => 'Scroll Mastered',
                'description' => 'Completed a full course path.',
                'icon' => 'fas fa-medal',
                'earned' => fn (User $user): bool => $user->courseProgress()->where('percent', 100)->exists(),
            ],
            'quiz_submitted' => [
                'title' => 'Quiz Challenger',
                'description' => 'Submitted a course quiz for review.',
                'icon' => 'fas fa-clipboard-question',
                'earned' => fn (User $user): bool => $user->quizSubmissions()->exists(),
            ],
            'first_game' => [
                'title' => 'Arcade Shinobi',
                'description' => 'Saved the first training game score.',
                'icon' => 'fas fa-gamepad',
                'earned' => fn (User $user): bool => $user->gameScores()->exists(),
            ],
            'high_score' => [
                'title' => 'Chakra Burst',
                'description' => 'Scored 150 or more in a training game.',
                'icon' => 'fas fa-bolt',
                'earned' => fn (User $user): bool => $user->gameScores()->where('score', '>=', 150)->exists(),
            ],
            'five_scrolls' => [
                'title' => 'Village Voice',
                'description' => 'Published five progress posts.',
                'icon' => 'fas fa-fire',
                'earned' => fn (User $user): bool => $user->posts()->count() >= 5,
            ],
        ];
    }
}
