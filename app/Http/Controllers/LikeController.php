<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LikeController extends Controller
{
    /**
     * Like a post
     */
    public function like(Post $post)
    {
        try {
            DB::beginTransaction();

            $like = $post->likes()->firstOrCreate([
                'user_id' => auth()->id()
            ]);

            // If like was just created (not already existed)
            if ($like->wasRecentlyCreated) {
                DB::commit();
                
                if (request()->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Post liked.',
                        'likes_count' => $post->likes()->count(),
                        'is_liked' => true
                    ]);
                }
                
                return back()->with('success', 'Post liked.');
            }

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'You already liked this post!',
                    'likes_count' => $post->likes()->count(),
                    'is_liked' => true
                ]);
            }

            return back()->with('info', 'You already liked this post!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to like post. Please try again.'
                ], 500);
            }
            
            return back()->with('error', 'Failed to like post. Please try again.');
        }
    }

    /**
     * Unlike a post
     */
    public function unlike(Post $post)
    {
        try {
            DB::beginTransaction();

            $deleted = $post->likes()->where('user_id', auth()->id())->delete();

            DB::commit();

            if ($deleted) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Post unliked.',
                        'likes_count' => $post->likes()->count(),
                        'is_liked' => false
                    ]);
                }
                
                return back()->with('success', 'Post unliked.');
            }

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Post was not liked!',
                    'likes_count' => $post->likes()->count(),
                    'is_liked' => false
                ]);
            }

            return back()->with('info', 'Post was not liked!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to unlike post. Please try again.'
                ], 500);
            }
            
            return back()->with('error', 'Failed to unlike post. Please try again.');
        }
    }

    /**
     * Toggle like/unlike
     */
    public function toggleLike(Post $post)
    {
        if ($post->isLikedBy(auth()->user())) {
            return $this->unlike($post);
        }

        return $this->like($post);
    }

    /**
     * Get post likes
     */
    public function getLikes(Post $post)
    {
        $likes = $post->likes()->with('user')->get();
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'likes' => $likes,
                'total_likes' => $post->likes()->count()
            ]);
        }

        return view('posts.likes', compact('post', 'likes'));
    }
}
