<?php

namespace App\Providers;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use App\Models\Friendship;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production') || env('FORCE_HTTPS', false)) {
            URL::forceScheme('https');
        }

        // Share global data with all views
        View::composer('*', function ($view) {
            $user = Auth::user();
            
            if (!$user) {
                // For non-authenticated users, provide basic data
                $view->with([
                    'otherUsersPostCount' => 0,
                    'userPostCount' => 0,
                    'pendingFriendRequestsCount' => 0,
                    'friendsCount' => 0,
                    'globalStats' => $this->getGlobalStats(),
                    'isOnline' => false
                ]);
                return;
            }

            // Cache key for user-specific data
            $cacheKey = "user_data_{$user->id}";
            $cacheDuration = 300; // 5 minutes

            $userData = Cache::remember($cacheKey, $cacheDuration, function () use ($user) {
                return [
                    'otherUsersPostCount' => Post::where('user_id', '!=', $user->id)->count(),
                    'userPostCount' => $user->posts()->count(),
                    'pendingFriendRequestsCount' => $user->pendingFriendRequests()->count(),
                    'friendsCount' => $user->getFriends()->count(),
                ];
            });

            // Add real-time data that shouldn't be cached
            $userData['isOnline'] = $user->last_seen && $user->last_seen->gt(now()->subMinutes(5));
            $userData['globalStats'] = $this->getGlobalStats();
            $userData['unreadNotificationsCount'] = $user->unreadNotifications()->count();

            $view->with($userData);
        });

        // Share specific data with navigation views
        View::composer(['layouts.navigation', 'layouts.app'], function ($view) {
            $user = Auth::user();
            
            if ($user) {
                $navData = Cache::remember("nav_data_{$user->id}", 60, function () use ($user) {
                    return [
                        'quickStats' => [
                            'posts' => $user->posts()->count(),
                            'friends' => $user->getFriends()->count(),
                            'pending_requests' => $user->pendingFriendRequests()->count(),
                        ]
                    ];
                });
                
                $view->with($navData);
            }
        });

        // Share data with post-related views
        View::composer(['posts.*', 'components.post-stats'], function ($view) {
            $globalStats = Cache::remember('global_post_stats', 3600, function () {
                return [
                    'total_posts' => Post::count(),
                    'total_users' => User::count(),
                    'total_comments' => Comment::count(),
                    'total_likes' => Like::count(),
                ];
            });
            
            $view->with('globalPostStats', $globalStats);
        });
    }

    /**
     * Get global statistics for the application
     */
    private function getGlobalStats(): array
    {
        return Cache::remember('global_stats', 3600, function () { // 1 hour cache
            return [
                'total_users' => User::count(),
                'total_posts' => Post::count(),
                'total_comments' => Comment::count(),
                'total_likes' => Like::count(),
                'active_today' => User::where('last_seen', '>=', now()->subDay())->count(),
                'new_this_week' => User::where('created_at', '>=', now()->subWeek())->count(),
            ];
        });
    }

    
}
