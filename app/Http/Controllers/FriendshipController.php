<?php

namespace App\Http\Controllers;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\FriendRequestNotification;
use App\Notifications\FriendRequestAcceptedNotification;

class FriendshipController extends Controller
{
    // Send a friend request
    public function sendRequest($receiver_id)
    {
        $receiver = User::findOrFail($receiver_id);

        $this->authorize('send', [Friendship::class, $receiver]);

        // Use the enhanced method from Friendship model
        if (!Friendship::canSendRequest(Auth::id(), $receiver_id)) {
            $existingFriendship = Friendship::findFriendship(Auth::id(), $receiver_id);
            if ($existingFriendship) {
                $status = $existingFriendship->status;
                $message = match($status) {
                    'pending' => 'Friend request already pending.',
                    'accepted' => 'You are already friends with this user.',
                    'declined' => 'Friend request was previously declined.',
                    'blocked' => 'This friendship has been blocked.',
                    default => 'Friend request already exists.'
                };
                return back()->with('error', $message);
            }
            return back()->with('error', 'Cannot send friend request.');
        }

        // Create the friend request using the enhanced method
        Friendship::createRequest(Auth::id(), $receiver_id);
        $receiver->notify(new FriendRequestNotification(Auth::user()));

        return back()->with('success', 'Friend request sent successfully.');
    }

    // Accept a friend request
    public function acceptRequest($sender_id)
    {
        $friendRequest = Friendship::where('sender_id', $sender_id)
            ->where('receiver_id', Auth::id())
            ->where('status', Friendship::STATUS_PENDING)
            ->firstOrFail();

        $this->authorize('accept', $friendRequest);

        $friendRequest->accept();
        $friendRequest->sender->notify(new FriendRequestAcceptedNotification(Auth::user()));

        return back()->with('success', 'Friend request accepted.');
    }

    // Reject a friend request
    public function rejectRequest($sender_id)
    {
        $friendRequest = Friendship::where('sender_id', $sender_id)
            ->where('receiver_id', Auth::id())
            ->where('status', Friendship::STATUS_PENDING)
            ->firstOrFail();

        $this->authorize('decline', $friendRequest);

        $friendRequest->decline();

        return back()->with('success', 'Friend request declined.');
    }

    // Cancel a sent friend request
    public function cancelRequest($receiver_id)
    {
        $friendship = Friendship::where('sender_id', Auth::id())
            ->where('receiver_id', $receiver_id)
            ->where('status', Friendship::STATUS_PENDING)
            ->firstOrFail();

        $this->authorize('cancel', $friendship);

        $friendship->delete();

        return back()->with('success', 'Friend request cancelled.');
    }

    // Remove a friend (unfriend)
    public function removeFriend($friend_id)
    {
        $friendship = Friendship::where(function($query) use ($friend_id) {
            $query->where('sender_id', Auth::id())
                  ->where('receiver_id', $friend_id);
        })->orWhere(function($query) use ($friend_id) {
            $query->where('sender_id', $friend_id)
                  ->where('receiver_id', Auth::id());
        })->where('status', Friendship::STATUS_ACCEPTED)
        ->firstOrFail();

        $this->authorize('delete', $friendship);

        $friendship->delete();

        return back()->with('success', 'Friend removed successfully.');
    }

    // Block a user
    public function blockUser($user_id)
    {
        $target = User::findOrFail($user_id);

        $this->authorize('block', [Friendship::class, $target]);

        // Find existing friendship or create a new one
        $friendship = Friendship::findFriendship(Auth::id(), $user_id);
        
        if (!$friendship) {
            $friendship = Friendship::create([
                'sender_id' => Auth::id(),
                'receiver_id' => $user_id,
                'status' => Friendship::STATUS_BLOCKED
            ]);
        } else {
            $friendship->block();
        }

        return back()->with('success', 'User has been blocked.');
    }

    // Unblock a user
    public function unblockUser($user_id)
    {
        $friendship = Friendship::where(function($query) use ($user_id) {
            $query->where('sender_id', Auth::id())
                  ->where('receiver_id', $user_id);
        })->orWhere(function($query) use ($user_id) {
            $query->where('sender_id', $user_id)
                  ->where('receiver_id', Auth::id());
        })->where('status', Friendship::STATUS_BLOCKED)
        ->firstOrFail();

        $this->authorize('unblock', $friendship);

        $friendship->delete();

        return back()->with('success', 'User has been unblocked.');
    }

    // View pending requests with pagination
    public function pendingRequests()
    {
        $pendingRequests = Friendship::with(['sender' => function($query) {
            $query->withCount('posts');
        }])
        ->where('receiver_id', Auth::id())
        ->where('status', Friendship::STATUS_PENDING)
        ->latest()
        ->paginate(10, ['*'], 'pending_page');

        $sentRequests = Friendship::with(['receiver' => function($query) {
            $query->withCount('posts');
        }])
        ->where('sender_id', Auth::id())
        ->where('status', Friendship::STATUS_PENDING)
        ->latest()
        ->paginate(10, ['*'], 'sent_page');

        $stats = Friendship::getUserStats(Auth::id());

        return view('friends.pending', compact('pendingRequests', 'sentRequests', 'stats'));
    }

    // View friends list with pagination
    public function friendsList()
    {
        $search = trim((string) request('q', ''));

        $friends = User::where(function($query) {
            $query->whereIn('id', function($inner) {
                $inner->select('receiver_id')
                    ->from('friendships')
                    ->where('sender_id', Auth::id())
                    ->where('status', Friendship::STATUS_ACCEPTED);
            })->orWhereIn('id', function($inner) {
                $inner->select('sender_id')
                    ->from('friendships')
                    ->where('receiver_id', Auth::id())
                    ->where('status', Friendship::STATUS_ACCEPTED);
            });
        })
        ->when($search, function ($query) use ($search) {
            $query->where(function ($inner) use ($search) {
                $inner->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        })
        ->withCount(['posts', 'comments'])
        ->orderByDesc('last_seen')
        ->paginate(12)
        ->withQueryString();

        $stats = Friendship::getUserStats(Auth::id());

        return view('friends.list', compact('friends', 'stats', 'search'));
    }

    // Get mutual friends between current user and another user
    public function mutualFriends($user_id)
    {
        $user = User::findOrFail($user_id);
        $mutualFriends = Friendship::getMutualFriends(Auth::id(), $user_id);
        $mutualCount = $mutualFriends->count();

        return view('friends.mutual', compact('user', 'mutualFriends', 'mutualCount'));
    }

    // Search friends
    public function searchFriends(Request $request)
    {
        $search = $request->query('q', '');
        
        $friendsQuery = User::whereIn('id', function($query) {
            $query->select('receiver_id')
                  ->from('friendships')
                  ->where('sender_id', Auth::id())
                  ->where('status', Friendship::STATUS_ACCEPTED);
        })->orWhereIn('id', function($query) {
            $query->select('sender_id')
                  ->from('friendships')
                  ->where('receiver_id', Auth::id())
                  ->where('status', Friendship::STATUS_ACCEPTED);
        });

        if ($search) {
            $friendsQuery->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $friends = $friendsQuery->withCount('posts')
                               ->orderBy('name')
                               ->paginate(12);

        return view('friends.search', compact('friends', 'search'));
    }

    // Get friendship status (API endpoint)
    public function getFriendshipStatus($user_id)
    {
        $status = Friendship::getStatus(Auth::id(), $user_id);
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $status,
                'user_id' => $user_id
            ]);
        }

        return $status;
    }

    // Get friendship statistics (API endpoint)
    public function getFriendshipStats()
    {
        $stats = Friendship::getUserStats(Auth::id());
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        }

        return $stats;
    }

    // Get recent friends (API endpoint)
    public function getRecentFriends()
    {
        $recentFriends = Friendship::getRecentFriendships(Auth::id(), 5);
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'friends' => $recentFriends
            ]);
        }

        return $recentFriends;
    }

    // Toggle friend request (accept/decline based on action)
    public function toggleRequest(Request $request, $sender_id)
    {
        $request->validate([
            'action' => 'required|in:accept,decline'
        ]);

        $friendRequest = Friendship::where('sender_id', $sender_id)
            ->where('receiver_id', Auth::id())
            ->where('status', Friendship::STATUS_PENDING)
            ->firstOrFail();

        $this->authorize($request->action === 'accept' ? 'accept' : 'decline', $friendRequest);

        if ($request->action === 'accept') {
            $friendRequest->accept();
            $friendRequest->sender->notify(new FriendRequestAcceptedNotification(Auth::user()));
            $message = 'Friend request accepted.';
        } else {
            $friendRequest->decline();
            $message = 'Friend request declined.';
        }

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'action' => $request->action
            ]);
        }

        return back()->with('success', $message);
    }

    // Bulk actions for friend requests
    public function bulkActions(Request $request)
    {
        $request->validate([
            'action' => 'required|in:accept,decline,delete',
            'request_ids' => 'required|array',
            'request_ids.*' => 'exists:friendships,id'
        ]);

        $friendships = Friendship::whereIn('id', $request->request_ids)
                                ->where('receiver_id', Auth::id())
                                ->where('status', Friendship::STATUS_PENDING)
                                ->get();

        $processed = 0;
        $action = $request->action;

        foreach ($friendships as $friendship) {
            $this->authorize($action === 'accept' ? 'accept' : 'decline', $friendship);

            if ($action === 'accept') {
                $friendship->accept();
                $friendship->sender->notify(new FriendRequestAcceptedNotification(Auth::user()));
                $processed++;
            } elseif ($action === 'decline') {
                $friendship->decline();
                $processed++;
            } elseif ($action === 'delete') {
                $friendship->delete();
                $processed++;
            }
        }

        $message = match($action) {
            'accept' => "Accepted {$processed} friend request(s).",
            'decline' => "Declined {$processed} friend request(s).",
            'delete' => "Deleted {$processed} friend request(s).",
            default => "Processed {$processed} request(s)."
        };

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'processed' => $processed
            ]);
        }

        return back()->with('success', $message);
    }

    // Check if can send friend request (API endpoint)
    public function canSendRequest($user_id)
    {
        $canSend = Friendship::canSendRequest(Auth::id(), $user_id);
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'can_send' => $canSend,
                'user_id' => $user_id
            ]);
        }

        return $canSend;
    }
}
