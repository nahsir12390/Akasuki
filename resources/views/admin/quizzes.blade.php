@extends('layouts.admin')

@section('title', 'Quiz Reviews - ' . config('app.name'))
@section('admin-heading', 'Quiz Reviews')

@section('content')
<x-ui.page width="max-w-7xl">
    @if (session('success'))
        <x-ui.alert class="mb-5">{{ session('success') }}</x-ui.alert>
    @endif

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <span class="rank-badge"><i class="fas fa-clipboard-check"></i> Learner Review</span>
            <h1 class="mt-3 text-3xl font-black tracking-normal text-slate-950 dark:text-white">Quiz Submissions</h1>
            <p class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400">Review learner answers, approve progress, or request revision.</p>
        </div>
        <x-ui.button :href="route('admin.courses')" variant="secondary"><i class="fas fa-graduation-cap"></i> Courses</x-ui.button>
    </div>

    <div class="space-y-4">
        @forelse($submissions as $submission)
            <article class="ui-card overflow-hidden">
                <div class="flex flex-col gap-3 border-b border-slate-200 p-4 dark:border-slate-800 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex min-w-0 items-center gap-3">
                        <x-ui.avatar :user="$submission->user" size="md" />
                        <div class="min-w-0">
                            <h2 class="truncate text-sm font-black text-slate-950 dark:text-white">{{ $submission->user->name }}</h2>
                            <p class="truncate text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $submission->course->title }} &bull; {{ $submission->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-lg bg-orange-50 px-3 py-2 text-xs font-black text-orange-700 ring-1 ring-orange-100 dark:bg-orange-950/35 dark:text-orange-300 dark:ring-orange-900">
                            Score {{ $submission->score }}/{{ $submission->total_questions }}
                        </span>
                        <span class="rounded-lg px-3 py-2 text-xs font-black ring-1 {{ $submission->status === 'approved' ? 'bg-green-50 text-green-700 ring-green-100 dark:bg-green-950/35 dark:text-green-300 dark:ring-green-900' : ($submission->status === 'needs_revision' ? 'bg-red-50 text-red-700 ring-red-100 dark:bg-red-950/35 dark:text-red-300 dark:ring-red-900' : 'bg-slate-50 text-slate-600 ring-slate-200 dark:bg-slate-900 dark:text-slate-300 dark:ring-slate-800') }}">
                            {{ str_replace('_', ' ', ucfirst($submission->status)) }}
                        </span>
                    </div>
                </div>

                <div class="grid gap-5 p-4 lg:grid-cols-[1fr_360px]">
                    <div class="space-y-3">
                        @foreach($submission->course->quiz_questions ?? [] as $index => $question)
                            <div class="rounded-lg border border-slate-200 p-3 dark:border-slate-800">
                                <p class="text-sm font-black text-slate-950 dark:text-white">{{ $index + 1 }}. {{ $question['question'] }}</p>
                                <p class="mt-2 text-sm font-semibold text-slate-600 dark:text-slate-400">Answer: {{ $submission->answers[$index] ?? 'No answer' }}</p>
                                <p class="mt-1 text-xs font-bold text-green-600 dark:text-green-400">Correct: {{ $question['answer'] ?? 'Not set' }}</p>
                            </div>
                        @endforeach
                    </div>

                    <form method="POST" action="{{ route('admin.quizzes.review', $submission) }}" class="rounded-lg border border-slate-200 p-4 dark:border-slate-800">
                        @csrf
                        @method('PATCH')
                        <label for="status-{{ $submission->id }}" class="ui-label mb-2"><i class="fas fa-check text-orange-500"></i><span>Review Status</span></label>
                        <select id="status-{{ $submission->id }}" name="status" class="ui-input">
                            <option value="pending" @selected($submission->status === 'pending')>Pending</option>
                            <option value="approved" @selected($submission->status === 'approved')>Approved</option>
                            <option value="needs_revision" @selected($submission->status === 'needs_revision')>Needs revision</option>
                        </select>

                        <label for="notes-{{ $submission->id }}" class="ui-label mb-2 mt-4"><i class="fas fa-note-sticky text-orange-500"></i><span>Admin Notes</span></label>
                        <textarea id="notes-{{ $submission->id }}" name="admin_notes" rows="4" class="ui-input resize-y" placeholder="Feedback for this learner">{{ $submission->admin_notes }}</textarea>

                        <x-ui.button type="submit" class="mt-4 w-full"><i class="fas fa-save"></i> Save Review</x-ui.button>
                    </form>
                </div>
            </article>
        @empty
            <x-ui.card class="p-10 text-center">
                <div class="mx-auto grid h-16 w-16 place-items-center rounded-lg bg-orange-50 text-orange-600 dark:bg-orange-950/35 dark:text-orange-300">
                    <i class="fas fa-clipboard-question text-2xl"></i>
                </div>
                <h2 class="mt-5 text-2xl font-black text-slate-950 dark:text-white">No quiz submissions yet</h2>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">When learners submit course quizzes, they will appear here.</p>
            </x-ui.card>
        @endforelse
    </div>

    @if($submissions->hasPages())
        <x-ui.card class="mt-6 p-4">{{ $submissions->links() }}</x-ui.card>
    @endif
</x-ui.page>
@endsection
