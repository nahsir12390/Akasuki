<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Friendship;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function profile(User $user)
    {
        // Keep authenticated user's presence fresh on any profile visit (including own profile).
        if (auth()->check()) {
            $now = now();
            auth()->user()->update(['last_seen' => $now]);

            // When viewing own profile, reflect the fresh timestamp in this request too.
            if (auth()->id() === $user->id) {
                $user->last_seen = $now;
            }
        }

        // Use paginate() instead of get()
        $posts = $user->posts()->withCount(['likes', 'comments'])->latest()->paginate(10);
        
        // Get friendship status
        $friendshipStatus = 'none';
        if (Auth::check()) {
            $friendshipStatus = Auth::user()->getFriendshipStatus($user->id);
        }

        // Calculate engagement stats
        $engagementStats = $this->calculateUserEngagement($user);

        return view('users.profile', compact('user', 'posts', 'friendshipStatus', 'engagementStats'));
    }

    /**
     * Calculate user engagement statistics
     */
    private function calculateUserEngagement(User $user)
    {
        // Load posts with counts if not already loaded
        if (!$user->relationLoaded('posts')) {
            $user->load(['posts' => function($query) {
                $query->withCount(['likes', 'comments']);
            }]);
        }

        $totalLikes = $user->posts->sum('likes_count');
        $totalComments = $user->posts->sum('comments_count');
        $engagementScore = $totalLikes + $totalComments;

        return [
            'total_likes' => $totalLikes,
            'total_comments' => $totalComments,
            'engagement_score' => $engagementScore,
            'post_count' => $user->posts->count(),
        ];
    }

    public function searchUsers(Request $request)
    {
        $search = $request->query('q', '');
        
        $users = User::where('id', '!=', auth()->id()) // Exclude current user
            ->when($search, function($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            })
            ->withCount(['posts', 'sentFriendRequests', 'receivedFriendRequests'])
            ->orderBy('name')
            ->paginate(20);

        // Get friendship status for each user
        $friendshipStatuses = [];
        foreach ($users as $user) {
            $friendshipStatuses[$user->id] = auth()->user()->getFriendshipStatus($user->id);
        }

        if ($request->ajax()) {
            return response()->json([
                'users' => $users,
                'friendshipStatuses' => $friendshipStatuses
            ]);
        }

        return view('users.search', compact('users', 'search', 'friendshipStatuses'));
    }

    // Quick User Search (for AJAX)
    public function quickSearch(Request $request)
    {
        $request->validate([
            'q' => 'required|string|min:2|max:50'
        ]);

        $searchTerm = $request->q;
        
        $users = User::where('id', '!=', auth()->id())
            ->where(function($query) use ($searchTerm) {
                $query->where('name', 'LIKE', "%{$searchTerm}%")
                      ->orWhere('email', 'LIKE', "%{$searchTerm}%");
            })
            ->withCount('posts')
            ->select('id', 'name', 'email', 'profile_photo', 'last_seen')
            ->limit(10)
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile_photo_url' => $user->profile_photo_url,
                    'is_online' => $user->isOnline(),
                    'posts_count' => $user->posts_count,
                    'friendship_status' => auth()->user()->getFriendshipStatus($user->id)
                ];
            });

        return response()->json($users);
    }

    public function updateProfilePhoto(Request $request)
    {
        $this->authorize('update', auth()->user());

        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();
        
        // Delete old profile photo if exists
        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        // Store new profile photo
        $path = $request->file('profile_photo')->store('profile-photos', 'public');
        
        $user->profile_photo = $path;
        $user->save();

        return back()->with('success', 'Profile photo updated successfully!');
    }

    public function removeProfilePhoto()
    {
        $user = auth()->user();

        $this->authorize('update', $user);

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
            $user->profile_photo = null;
            $user->save();
        }

        return back()->with('success', 'Profile photo removed successfully!');
    }

    public function editAccount()
    {
        $user = auth()->user();

        $this->authorize('update', $user);

        return view('account.edit', compact('user'));
    }

    public function updateAccount(Request $request)
    {
        $user = auth()->user();

        $this->authorize('update', $user);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:users,name,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'bio' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old profile photo if exists
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            
            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $validated['profile_photo'] = $path;
        }

        $user->update($validated);

        return back()->with('success', 'Account updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $this->authorize('update', auth()->user());

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
        ]);

        auth()->user()->update([
            'password' => Hash::make($validated['password'])
        ]);

        return back()->with('success', 'Password updated successfully!');
    }

    public function updatePreferences(Request $request)
    {
        $user = auth()->user();

        $this->authorize('update', $user);
        
        $validated = $request->validate([
            'email_course_updates' => 'boolean',
            'email_messages' => 'boolean',
            'public_profile' => 'boolean',
            'show_online_status' => 'boolean',
        ]);

        // Convert checkbox values to boolean
        $validated['email_course_updates'] = $request->has('email_course_updates');
        $validated['email_messages'] = $request->has('email_messages');
        $validated['public_profile'] = $request->has('public_profile');
        $validated['show_online_status'] = $request->has('show_online_status');

        $user->update($validated);

        return back()->with('success', 'Preferences updated successfully!');
    }

    // Add this method to handle the account settings page
    public function accountSettings()
    {
        $user = auth()->user();

        $this->authorize('update', $user);

        return view('account.settings', compact('user'));
    }

    public function updateSkills(Request $request)
    {
        // If GET request, redirect to settings page with hash
        if ($request->getMethod() === 'GET') {
            return redirect()->route('account.settings')->with('success', 'Redirected to settings');
        }

        $user = auth()->user();

        $this->authorize('update', $user);
        
        $validated = $request->validate([
            'skills' => 'nullable|string|max:1000',
            'interests' => 'nullable|string|max:1000',
        ]);

        // Convert comma-separated strings to arrays
        $skills = $validated['skills'] 
            ? array_map('trim', explode(',', $validated['skills'])) 
            : null;
        $interests = $validated['interests'] 
            ? array_map('trim', explode(',', $validated['interests'])) 
            : null;

        $user->update([
            'skills' => $skills,
            'interests' => $interests,
        ]);

        return back()->with('success', 'Skills and interests updated successfully!');
    }

    /**
     * Get user statistics for API or internal use
     */
    public function getUserStats(User $user)
    {
        $engagementStats = $this->calculateUserEngagement($user);
        
        return response()->json([
            'success' => true,
            'stats' => [
                'posts_count' => $engagementStats['post_count'],
                'total_likes' => $engagementStats['total_likes'],
                'total_comments' => $engagementStats['total_comments'],
                'engagement_score' => $engagementStats['engagement_score'],
                'friends_count' => $user->getFriends()->count(),
            ]
        ]);
    }

    /**
     * Get user's friends with pagination
     */
    public function getUserFriends(User $user)
    {
        $friends = $user->getFriends();
        
        return view('users.friends', compact('user', 'friends'));
    }

    /**
     * Get user's mutual friends with current user
     */
    public function getMutualFriends(User $user)
    {
        if (!auth()->check()) {
            return response()->json(['mutual_friends' => []]);
        }

        $mutualFriends = auth()->user()->mutualFriends($user->id);
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'mutual_friends' => $mutualFriends,
                'count' => $mutualFriends->count()
            ]);
        }

        return view('users.mutual-friends', compact('user', 'mutualFriends'));
    }
}
