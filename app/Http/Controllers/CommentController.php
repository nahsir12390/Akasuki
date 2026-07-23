<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    /**
     * Store a new comment
     */
    public function store(Request $request, Post $post)
    {
        $this->authorize('create', [Comment::class, $post]);

        $request->validate([
            'body' => 'required|string|min:1|max:2000',
        ]);

        try {
            DB::beginTransaction();

            $comment = $post->comments()->create([
                'user_id' => auth()->id(),
                'body' => $request->body,
            ]);

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Comment added successfully! 💬',
                    'comment' => $comment->load('user'),
                    'comments_count' => $post->fresh()->comments_count
                ]);
            }

            return back()->with('success', 'Comment added successfully! 💬');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add comment. Please try again.'
                ], 500);
            }
            
            return back()->with('error', 'Failed to add comment. Please try again.');
        }
    }

    /**
     * Show edit form for a comment
     */
    public function edit(Comment $comment)
    {
        $this->authorize('update', $comment);

        return view('commentsFolder.editComment', compact('comment'));
    }

    /**
     * Update the comment
     */
    public function update(Request $request, Comment $comment)
    {
        $this->authorize('update', $comment);

        $request->validate([
            'body' => 'required|string|min:1|max:2000',
        ]);

        try {
            DB::beginTransaction();

            $comment->update([
                'body' => $request->body,
            ]);

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Comment updated successfully! ✏️',
                    'comment' => $comment->fresh()
                ]);
            }

            return redirect()->route('show.post')
                ->with('success', 'Comment updated successfully! ✏️');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update comment. Please try again.'
                ], 500);
            }
            
            return back()->with('error', 'Failed to update comment. Please try again.')
                ->withInput();
        }
    }

    /**
     * Delete a comment
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        try {
            DB::beginTransaction();

            $postId = $comment->post_id;
            $comment->delete();

            DB::commit();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Comment deleted successfully! 🗑️',
                    'comments_count' => Post::find($postId)->comments_count
                ]);
            }

            return redirect()->route('show.post')
                ->with('success', 'Comment deleted successfully! 🗑️');

        } catch (\Exception $e) {
            DB::rollBack();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete comment. Please try again.'
                ], 500);
            }
            
            return back()->with('error', 'Failed to delete comment. Please try again.');
        }
    }

    /**
     * Get comments for a post (AJAX)
     */
    public function getComments(Post $post)
    {
        $comments = $post->comments()
            ->with('user')
            ->latest()
            ->paginate(10);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'comments' => $comments,
                'comments_count' => $post->comments_count
            ]);
        }

        return view('commentsFolder.comments-list', compact('post', 'comments'));
    }

    /**
     * Like a comment
     */
    public function like(Comment $comment)
    {
        try {
            // You can implement comment likes if needed
            // This is a placeholder for future implementation
            
            return response()->json([
                'success' => false,
                'message' => 'Comment liking not implemented yet.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to like comment.'
            ], 500);
        }
    }
}
