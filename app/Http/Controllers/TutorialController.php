<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseProgress;
use App\Models\QuizSubmission;
use App\Services\AchievementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TutorialController extends Controller
{
    public function html() { return $this->showBySlug('html'); }
    public function css() { return $this->showBySlug('css'); }
    public function js() { return $this->showBySlug('js'); }
    public function php() { return $this->showBySlug('php'); }
    public function laravel() { return $this->showBySlug('laravel'); }
    public function vue() { return $this->showBySlug('vue'); }
    public function react() { return $this->showBySlug('react'); }
    public function python() { return $this->showBySlug('python'); }
    public function java() { return $this->showBySlug('java'); }
    public function csharp() { return $this->showBySlug('csharp'); }
    public function cpp() { return $this->showBySlug('cpp'); }
    public function ruby() { return $this->showBySlug('ruby'); }
    public function mysql() { return $this->showBySlug('mysql'); }
    public function jquery() { return $this->showBySlug('jquery'); }

    public function show(Course $course)
    {
        abort_unless($course->is_active, 404);

        return $this->renderCourse($course);
    }

    public function submitQuiz(Request $request, Course $course): RedirectResponse
    {
        abort_unless($course->is_active, 404);

        $questions = collect($course->quiz_questions ?? []);

        if ($questions->isEmpty()) {
            return back()->with('error', 'This course does not have a quiz yet.');
        }

        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'nullable|string|max:500',
        ]);

        $answers = collect($validated['answers'])
            ->map(fn ($answer) => trim((string) $answer))
            ->all();

        $score = $questions->filter(function ($question, $index) use ($answers) {
            $correct = strtolower(trim((string) ($question['answer'] ?? '')));
            $given = strtolower(trim((string) ($answers[$index] ?? '')));

            return $correct !== '' && $given === $correct;
        })->count();

        QuizSubmission::create([
            'user_id' => $request->user()->id,
            'course_id' => $course->id,
            'answers' => $answers,
            'score' => $score,
            'total_questions' => $questions->count(),
            'status' => 'pending',
        ]);

        AchievementService::sync($request->user());

        return back()->with('success', 'Quiz submitted for admin review.');
    }

    public function updateProgress(Request $request, Course $course): JsonResponse
    {
        abort_unless($course->is_active, 404);

        $validated = $request->validate([
            'section' => ['required', 'string', 'in:modules,checklist,projects'],
            'index' => ['required', 'integer', 'min:0'],
            'completed' => ['required', 'boolean'],
        ]);

        $sectionCount = count($course->{$validated['section']} ?? []);
        abort_if((int) $validated['index'] >= $sectionCount, 422, 'Progress item does not exist.');

        $progress = CourseProgress::firstOrCreate([
            'user_id' => $request->user()->id,
            'course_id' => $course->id,
        ]);

        $column = match ($validated['section']) {
            'modules' => 'completed_modules',
            'checklist' => 'completed_checklist',
            'projects' => 'completed_projects',
        };

        $items = $progress->{$column} ?? [];
        $index = (int) $validated['index'];

        if ($validated['completed']) {
            $items[] = $index;
        } else {
            $items = array_diff($items, [$index]);
        }

        $progress->{$column} = collect($items)
            ->map(fn ($item) => (int) $item)
            ->unique()
            ->sort()
            ->values()
            ->all();
        $progress->percent = $this->progressPercent($course, $progress);
        $progress->completed_at = $progress->percent >= 100 ? ($progress->completed_at ?? now()) : null;
        $progress->save();

        AchievementService::sync($request->user());

        return response()->json([
            'percent' => $progress->percent,
            'rank' => $progress->rank(),
            'completed' => (bool) $validated['completed'],
            'completed_at' => $progress->completed_at?->toIso8601String(),
        ]);
    }

    private function showBySlug(string $slug)
    {
        $course = Course::where('slug', $slug)->where('is_active', true)->firstOrFail();

        return $this->renderCourse($course);
    }

    private function renderCourse(Course $course)
    {
        $videos = $this->fetchVideos($course->query ?: $course->title . ' tutorial full course');
        $allCourses = Course::where('is_active', true)->orderBy('sort_order')->orderBy('title')->get();
        $nextCourse = $allCourses->skipUntil(fn (Course $item) => $item->is($course))->skip(1)->first()
            ?: $allCourses->first();
        $progress = CourseProgress::firstOrCreate([
            'user_id' => request()->user()->id,
            'course_id' => $course->id,
        ]);

        $progress->percent = $this->progressPercent($course, $progress);
        $progress->save();

        return view('tutorials.show', compact('course', 'videos', 'allCourses', 'nextCourse', 'progress'));
    }

    private function progressPercent(Course $course, CourseProgress $progress): int
    {
        $total = count($course->modules ?? []) + count($course->checklist ?? []) + count($course->projects ?? []);

        if ($total === 0) {
            return 0;
        }

        $completed = count($progress->completed_modules ?? [])
            + count($progress->completed_checklist ?? [])
            + count($progress->completed_projects ?? []);

        return (int) min(100, round(($completed / $total) * 100));
    }

    private function fetchVideos(string $query): array
    {
        $apiKey = config('services.youtube.key');

        if (!$apiKey) {
            return [];
        }

        try {
            $response = Http::timeout(5)->get('https://www.googleapis.com/youtube/v3/search', [
                'part' => 'snippet',
                'q' => $query,
                'key' => $apiKey,
                'maxResults' => 8,
                'type' => 'video',
                'videoEmbeddable' => 'true',
            ]);

            return $response->successful() ? ($response->json()['items'] ?? []) : [];
        } catch (\Throwable $e) {
            report($e);

            return [];
        }
    }
}
