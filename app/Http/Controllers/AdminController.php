<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Course;
use App\Models\Friendship;
use App\Models\Like;
use App\Models\Message;
use App\Models\Post;
use App\Models\QuizSubmission;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $stats = [
            'users' => User::count(),
            'posts' => Post::count(),
            'comments' => Comment::count(),
            'messages' => Message::count(),
            'likes' => Like::count(),
            'friendships' => Friendship::where('status', 'accepted')->count(),
            'courses' => Course::count(),
            'active_courses' => Course::where('is_active', true)->count(),
            'quiz_submissions' => QuizSubmission::count(),
            'pending_quizzes' => QuizSubmission::where('status', 'pending')->count(),
            'new_users' => User::where('created_at', '>=', now()->subDays(7))->count(),
            'new_posts' => Post::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        $recentUsers = User::latest()->limit(6)->get();
        $recentPosts = Post::with('user')->withCount(['likes', 'comments'])->latest()->limit(6)->get();
        $recentCourses = Course::latest()->limit(6)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentPosts', 'recentCourses'));
    }

    public function users(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $users = User::query()
            ->withCount(['posts', 'comments', 'likes'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users', compact('users', 'search'));
    }

    public function posts(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));

        $posts = Post::query()
            ->with('user')
            ->withCount(['likes', 'comments'])
            ->when($search, function ($query) use ($search) {
                $query->where('content', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.posts', compact('posts', 'search'));
    }

    public function quizzes(): View
    {
        $submissions = QuizSubmission::with(['user', 'course', 'reviewer'])
            ->latest()
            ->paginate(15);

        return view('admin.quizzes', compact('submissions'));
    }

    public function reviewQuiz(Request $request, QuizSubmission $submission): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,needs_revision',
            'admin_notes' => 'nullable|string|max:1500',
        ]);

        $submission->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? null,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Quiz submission reviewed.');
    }

    public function toggleAdmin(User $user): RedirectResponse
    {
        abort_if($user->is(auth()->user()), 422, 'You cannot change your own admin access.');

        $user->forceFill(['is_admin' => !$user->is_admin])->save();

        return back()->with('success', $user->name . ' admin access updated.');
    }

    public function destroyPost(Post $post): RedirectResponse
    {
        if ($post->media_path) {
            Storage::disk('public')->delete($post->media_path);
        }

        if ($post->thumbnail_path) {
            Storage::disk('public')->delete($post->thumbnail_path);
        }

        $post->likes()->delete();
        $post->comments()->delete();
        $post->delete();

        return back()->with('success', 'Post removed from the village.');
    }

    public function courses(): View
    {
        $courses = Course::orderBy('sort_order')->orderBy('title')->paginate(15);

        return view('admin.courses.index', compact('courses'));
    }

    public function createCourse(): View
    {
        return view('admin.courses.form', ['course' => new Course()]);
    }

    public function storeCourse(Request $request): RedirectResponse
    {
        Course::create($this->courseData($request));

        return redirect()->route('admin.courses')->with('success', 'Course created.');
    }

    public function editCourse(Course $course): View
    {
        return view('admin.courses.form', compact('course'));
    }

    public function updateCourse(Request $request, Course $course): RedirectResponse
    {
        $course->update($this->courseData($request, $course));

        return redirect()->route('admin.courses')->with('success', 'Course updated.');
    }

    public function toggleCourse(Course $course): RedirectResponse
    {
        $course->update(['is_active' => !$course->is_active]);

        return back()->with('success', 'Course visibility updated.');
    }

    public function destroyCourse(Course $course): RedirectResponse
    {
        $course->delete();

        return back()->with('success', 'Course deleted.');
    }

    private function courseData(Request $request, ?Course $course = null): array
    {
        $validated = $request->validate([
            'title' => 'required|string|max:120',
            'slug' => 'nullable|string|max:140|alpha_dash|unique:courses,slug,' . ($course?->id ?? 'NULL'),
            'subtitle' => 'required|string|max:500',
            'icon' => 'required|string|max:80',
            'level' => 'required|string|max:80',
            'duration' => 'required|string|max:80',
            'query' => 'nullable|string|max:180',
            'youtube_url' => 'nullable|url|max:500',
            'modules' => 'nullable|string|max:3000',
            'projects' => 'nullable|string|max:2000',
            'checklist' => 'nullable|string|max:2000',
            'resources' => 'nullable|string|max:3000',
            'quiz_questions' => 'nullable|string|max:5000',
            'sort_order' => 'nullable|integer|min:0|max:10000',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title']);

        $slugExists = Course::where('slug', $validated['slug'])
            ->when($course, fn ($query) => $query->whereKeyNot($course->id))
            ->exists();

        if ($slugExists) {
            throw ValidationException::withMessages([
                'slug' => 'This course slug is already in use.',
            ]);
        }

        $validated['query'] = $validated['query'] ?: $validated['title'] . ' tutorial full course';
        $validated['modules'] = $this->lines($validated['modules'] ?? '');
        $validated['projects'] = $this->lines($validated['projects'] ?? '');
        $validated['checklist'] = $this->lines($validated['checklist'] ?? '');
        $validated['resources'] = $this->resources($validated['resources'] ?? '');
        $validated['quiz_questions'] = $this->quizQuestions($validated['quiz_questions'] ?? '');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }

    private function lines(string $value): array
    {
        return collect(preg_split('/\r\n|\r|\n/', $value))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    private function resources(string $value): array
    {
        return collect($this->lines($value))
            ->map(function (string $line) {
                [$label, $url] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');

                return ['label' => $label, 'url' => $url];
            })
            ->filter(fn ($resource) => $resource['label'] && filter_var($resource['url'], FILTER_VALIDATE_URL))
            ->values()
            ->all();
    }

    private function quizQuestions(string $value): array
    {
        return collect($this->lines($value))
            ->map(function (string $line) {
                [$question, $options, $answer] = array_pad(array_map('trim', explode('|', $line, 3)), 3, '');

                return [
                    'question' => $question,
                    'options' => collect(explode(';', $options))->map(fn ($option) => trim($option))->filter()->values()->all(),
                    'answer' => $answer,
                ];
            })
            ->filter(fn ($item) => $item['question'] && count($item['options']) >= 2 && $item['answer'])
            ->values()
            ->all();
    }
}
