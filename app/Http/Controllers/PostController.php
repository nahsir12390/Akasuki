<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with([
            'user',
            'likes' => function($query) {
                $query->where('user_id', auth()->id());
            },
            'comments.user'
        ])
        ->withCount(['likes', 'comments'])
        ->latest()
        ->paginate(10);

        $currentUser = auth()->user();

        return view('posts.post', compact('posts', 'currentUser'));
    }

    public function create()
    {
        $this->authorize('create', Post::class);

        return view('posts.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Post::class);

        \Log::info('Post creation started via user relationship');

        try {
            // Manual validation for better control
            $validator = \Validator::make($request->all(), [
                'content' => 'nullable|string|max:1000',
                'media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:102400',
            ]);

            if ($validator->fails()) {
                \Log::error('Validation failed', ['errors' => $validator->errors()->all()]);
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $validated = $validator->validated();

            // Check if we have at least content or media
            $hasContent = !empty(trim($validated['content'] ?? ''));
            $hasMedia = $request->hasFile('media');

            if (!$hasContent && !$hasMedia) {
                return redirect()->back()
                    ->with('error', 'Please add some text content or media to your post.')
                    ->withInput();
            }

            DB::beginTransaction();

            // Build post data without user_id (it will be set automatically via relationship)
            $postData = [
                'content' => $hasContent ? trim($validated['content']) : null,
            ];

            // Handle media upload
            if ($hasMedia) {
                $media = $request->file('media');
                $mediaType = str_starts_with($media->getMimeType(), 'video/') ? 'video' : 'image';
                
                // Store the media file
                $mediaPath = $media->store('posts/media', 'public');
                $postData['media_path'] = $mediaPath;
                $postData['media_type'] = $mediaType;

                // Skip thumbnail generation for videos for now
                // We'll handle thumbnails later or use a different approach
                if ($mediaType === 'video') {
                    $postData['thumbnail_path'] = null;
                }
            }

            // Create post through user relationship (this automatically sets user_id)
            $post = auth()->user()->posts()->create($postData);

            \Log::info('Post created successfully', ['post_id' => $post->id]);

            DB::commit();

            return redirect()->route('show.post')
                ->with('success', 'Post created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Post creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to create post. Please try again.')
                ->withInput();
        }
    }

    public function edit($id)
    {
        $post = Post::query()
            ->withCount(['likes', 'comments'])
            ->findOrFail($id);

        $this->authorize('update', $post);

        return view('posts.editPost', compact('post'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'content' => 'required_without:media|string|min:1|max:1000|nullable',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,mov,avi|max:102400',
            'remove_media' => 'nullable|boolean',
        ]);

        $post = Post::findOrFail($id);

        $this->authorize('update', $post);

        try {
            DB::beginTransaction();

            $updateData = ['content' => $validated['content'] ?? null];

            // Handle media removal
            if ($request->has('remove_media') && $request->remove_media) {
                if ($post->media_path) {
                    Storage::disk('public')->delete($post->media_path);
                }
                if ($post->thumbnail_path) {
                    Storage::disk('public')->delete($post->thumbnail_path);
                }
                $updateData['media_path'] = null;
                $updateData['media_type'] = null;
                $updateData['thumbnail_path'] = null;
            }

            // Handle new media upload
            if ($request->hasFile('media')) {
                // Delete old media
                if ($post->media_path) {
                    Storage::disk('public')->delete($post->media_path);
                }
                if ($post->thumbnail_path) {
                    Storage::disk('public')->delete($post->thumbnail_path);
                }

                $media = $request->file('media');
                $mediaType = str_starts_with($media->getMimeType(), 'video/') ? 'video' : 'image';
                
                $mediaPath = $media->store('posts/media', 'public');
                $updateData['media_path'] = $mediaPath;
                $updateData['media_type'] = $mediaType;
                $updateData['thumbnail_path'] = null; // Skip thumbnails for now
            }

            $post->update($updateData);

            DB::commit();

            return redirect()->route('show.post')
                ->with('success', 'Post updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to update post. Please try again.')
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);

        $this->authorize('delete', $post);

        try {
            DB::beginTransaction();

            // Delete associated media files
            if ($post->media_path) {
                Storage::disk('public')->delete($post->media_path);
            }
            if ($post->thumbnail_path) {
                Storage::disk('public')->delete($post->thumbnail_path);
            }
            
            // Delete associated likes and comments
            $post->likes()->delete();
            $post->comments()->delete();
            $post->delete();

            DB::commit();

            return redirect()->route('show.post')
                ->with('success', 'Post deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Failed to delete post. Please try again.');
        }
    }
}
