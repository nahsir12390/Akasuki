<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\QuizSubmission;
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

        return back()->with('success', 'Quiz submitted for admin review.');
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

        return view('tutorials.show', compact('course', 'videos', 'allCourses', 'nextCourse'));
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
