@php
    use App\Models\Friendship;

    if (!isset($user)) {
        $user = null;
    }

    $friendshipStatus = auth()->check() && $user ? auth()->user()->getFriendshipStatus($user->id) : 'none';
@endphp

@if(auth()->check() && $user && auth()->id() != $user->id)
    <div class="flex flex-wrap gap-2 mt-4">
        @switch($friendshipStatus)
            @case('none')
                <!-- Send Friend Request -->
                <form action="{{ route('friends.send', $user->id) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition font-medium">
                        <i class="fas fa-user-plus mr-2"></i>
                        Add Friend
                    </button>
                </form>
                @break

            @case('pending_sent')
                <!-- Cancel Friend Request -->
                <form action="{{ route('friends.cancel', $user->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition font-medium">
                        <i class="fas fa-clock mr-2"></i>
                        Request Sent
                    </button>
                </form>
                @break

            @case('pending_received')
                <!-- Accept/Reject Friend Request -->
                <form action="{{ route('friends.accept', $user->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition font-medium mr-2">
                        <i class="fas fa-check mr-2"></i>
                        Accept
                    </button>
                </form>
                <form action="{{ route('friends.reject', $user->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition font-medium">
                        <i class="fas fa-times mr-2"></i>
                        Reject
                    </button>
                </form>
                @break

            @case('accepted')
                <!-- Friends - Remove Friend -->
                <div class="flex items-center space-x-2">
                    <span class="bg-green-600 text-white px-3 py-2 rounded-lg font-medium">
                        <i class="fas fa-user-friends mr-2"></i>
                        Friends
                    </span>
                    <form action="{{ route('friends.remove', $user->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Are you sure you want to remove this friend?')"
                                class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg transition font-medium">
                            <i class="fas fa-user-times mr-2"></i>
                            Remove
                        </button>
                    </form>
                    <a href="{{ route('chat.index') }}?user={{ $user->id }}"
                       class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg transition font-medium">
                        <i class="fas fa-comment mr-2"></i>
                        Message
                    </a>
                </div>
                @break
        @endswitch
    </div>
@endif