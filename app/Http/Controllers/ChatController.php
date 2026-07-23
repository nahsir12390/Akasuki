<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use App\Events\MessageSent;
use App\Models\Friendship;
use Illuminate\Http\Request;
use App\Notifications\NewMessageNotification;

class ChatController extends Controller
{
    public function index()
    {
        $friendIds = $this->friendIdsFor(auth()->id());

        // Get friends with recent conversations first
        $users = User::whereIn('id', $friendIds)
                     ->withCount(['sentMessages' => function($query) {
                         $query->where('receiver_id', auth()->id());
                     }, 'receivedMessages' => function($query) {
                         $query->where('sender_id', auth()->id());
                     }])
                     ->with(['sentMessages' => function($query) {
                         $query->where('receiver_id', auth()->id())
                               ->orderBy('created_at', 'desc')
                               ->limit(1);
                     }, 'receivedMessages' => function($query) {
                         $query->where('sender_id', auth()->id())
                               ->orderBy('created_at', 'desc')
                               ->limit(1);
                     }])
                     ->get()
                     ->sortByDesc(function($user) {
                         $lastSent = $user->sentMessages->first();
                         $lastReceived = $user->receivedMessages->first();
                         
                         if ($lastSent && $lastReceived) {
                             return $lastSent->created_at > $lastReceived->created_at 
                                 ? $lastSent->created_at 
                                 : $lastReceived->created_at;
                         } elseif ($lastSent) {
                             return $lastSent->created_at;
                         } elseif ($lastReceived) {
                             return $lastReceived->created_at;
                         }
                         
                         return now()->subYears(10);
                     })
                     ->take(20);

        $unreadMessageNotifications = $this->unreadMessageNotifications();
        $unreadCounts = [];
        foreach ($users as $user) {
            $unreadCounts[$user->id] = $unreadMessageNotifications
                ->filter(fn ($notification) => $this->notificationSenderId($notification) === (string) $user->id)
                ->count();
        }

        return view('chats.index', compact('users', 'unreadCounts'));
    }


    public function send(Request $request)
{
    $request->validate([
        'receiver_id' => 'required|exists:users,id',
        'message' => 'required|string|max:1000'
    ]);

    $receiver = User::findOrFail($request->receiver_id);

    $this->authorize('create', [Message::class, $receiver]);

    try {
        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        // Eager load the sender relationship
        $message->load('sender');

        broadcast(new MessageSent($message))->toOthers();

        // Send notification
        if ($receiver) {
            $receiver->notify(new NewMessageNotification($message));
        }

        // Update user's last_seen timestamp
        if (method_exists(auth()->user(), 'updateLastSeen')) {
            auth()->user()->updateLastSeen();
        }

        return response()->json([
            'success' => true,
            'message' => 'Message sent!',
            'data' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_id' => $message->sender_id,
                'sender' => [
                    'id' => $message->sender->id,
                    'name' => $message->sender->name,
                    'profile_photo_url' => $message->sender->profile_photo_url,
                ],
                'created_at' => $message->created_at->format('h:i A'),
                'date' => $message->created_at->format('M j, Y')
            ]
        ]);

    } catch (\Exception $e) {
        \Log::error('Message send error: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to send message. Please try again.'
        ], 500);
    }
}


    public function loadChat(User $user)
    {
        $this->authorize('viewConversation', [Message::class, $user]);

        // Mark notifications as read for this user
        $this->unreadMessageNotifications()
            ->filter(fn ($notification) => $this->notificationSenderId($notification) === (string) $user->id)
            ->each(fn ($notification) => $notification->markAsRead());

        $messages = Message::where(function ($query) use ($user) {
                $query->where('sender_id', auth()->id())
                      ->where('receiver_id', $user->id);
            })->orWhere(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                      ->where('receiver_id', auth()->id());
            })
            ->with('sender')
            ->orderBy('created_at')
            ->get();

        $messages = $messages->map(function ($msg) {
            return [
                'id' => $msg->id,
                'message' => $msg->message,
                'sender_id' => $msg->sender_id,
                'sender' => [
                    'id' => $msg->sender->id,
                    'name' => $msg->sender->name,
                    'profile_photo_url' => $msg->sender->profile_photo_url,
                ],
                'created_at' => $msg->created_at->format('h:i A'),
                'date' => $msg->created_at->format('M j, Y')
            ];
        });

        // Update user's last_seen timestamp
        auth()->user()->updateLastSeen();

        return response()->json($messages);
    }

    public function searchUsers(Request $request)
    {
        $search = $request->query('q');
        $friendIds = $this->friendIdsFor(auth()->id());

        $users = User::whereIn('id', $friendIds)
                     ->where('name', 'LIKE', "%{$search}%")
                     ->with(['sentMessages' => function($query) {
                         $query->where('receiver_id', auth()->id())
                               ->latest()
                               ->limit(1);
                     }, 'receivedMessages' => function($query) {
                         $query->where('sender_id', auth()->id())
                               ->latest()
                               ->limit(1);
                     }])
                     ->limit(10)
                     ->get()
                     ->map(function (User $user) {
                         $lastMessage = $user->sentMessages
                             ->concat($user->receivedMessages)
                             ->sortByDesc('created_at')
                             ->first();

                         return [
                             'id' => $user->id,
                             'name' => $user->name,
                             'profile_photo_url' => $user->profile_photo_url,
                             'is_online' => $user->isOnline(),
                             'last_message' => $lastMessage?->message,
                             'last_message_time' => $lastMessage?->created_at?->format('h:i A'),
                         ];
                     });

        return response()->json($users);
    }

    public function getUnreadCount()
    {
        $count = auth()->user()->unreadNotifications()
            ->where('type', 'App\Notifications\NewMessageNotification')
            ->count();

        return response()->json(['count' => $count]);
    }

    private function friendIdsFor(int $userId)
    {
        $sentFriendIds = Friendship::where('sender_id', $userId)
            ->where('status', Friendship::STATUS_ACCEPTED)
            ->pluck('receiver_id');

        $receivedFriendIds = Friendship::where('receiver_id', $userId)
            ->where('status', Friendship::STATUS_ACCEPTED)
            ->pluck('sender_id');

        return $sentFriendIds->merge($receivedFriendIds)->unique()->values();
    }

    private function unreadMessageNotifications()
    {
        return auth()->user()->unreadNotifications()
            ->where('type', NewMessageNotification::class)
            ->get(['id', 'data', 'read_at']);
    }

    private function notificationSenderId($notification): ?string
    {
        $data = $notification->data;

        if (is_string($data)) {
            $data = json_decode($data, true) ?: [];
        }

        $senderId = data_get($data, 'sender_id');

        return $senderId === null ? null : (string) $senderId;
    }
}
