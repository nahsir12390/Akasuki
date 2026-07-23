@extends('layouts.admin')

@section('title', 'Admin Courses - ' . config('app.name'))
@section('admin-heading', 'Course Management')

@section('content')
<x-ui.page width="max-w-7xl">
    @if (session('success'))
        <x-ui.alert class="mb-5">{{ session('success') }}</x-ui.alert>
    @endif

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <span class="rank-badge"><i class="fas fa-graduation-cap"></i> Course Management</span>
            <h1 class="mt-3 text-3xl font-black tracking-normal text-slate-950 dark:text-white">Courses</h1>
            <p class="mt-1 text-sm font-medium text-slate-500 dark:text-slate-400">Add, edit, hide, and order learning paths dynamically.</p>
        </div>
        <div class="flex gap-2">
            <x-ui.button :href="route('admin.dashboard')" variant="secondary"><i class="fas fa-arrow-left"></i> Dashboard</x-ui.button>
            <x-ui.button :href="route('admin.courses.create')"><i class="fas fa-plus"></i> Add Course</x-ui.button>
        </div>
    </div>

    <x-ui.card padding="p-0" class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                <thead class="bg-slate-50 text-left text-xs font-black uppercase text-slate-500 dark:bg-slate-900 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3">Course</th>
                        <th class="px-4 py-3">Level</th>
                        <th class="px-4 py-3">Learning Tools</th>
                        <th class="px-4 py-3">Order</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($courses as $course)
                        <tr class="bg-white dark:bg-slate-950">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="grid h-11 w-11 place-items-center rounded-lg bg-orange-50 text-orange-600 dark:bg-orange-950/35 dark:text-orange-300"><i class="{{ $course->icon }}"></i></span>
                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-black text-slate-950 dark:text-white">{{ $course->title }}</span>
                                        <span class="block truncate text-xs font-semibold text-slate-500 dark:text-slate-400">/tutorial/{{ $course->slug }}</span>
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm font-semibold text-slate-600 dark:text-slate-400">{{ $course->level }} &bull; {{ $course->duration }}</td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap gap-2 text-xs font-black">
                                    <span class="rounded-lg px-2.5 py-1.5 ring-1 {{ $course->youtube_url ? 'bg-red-50 text-red-700 ring-red-100 dark:bg-red-950/35 dark:text-red-300 dark:ring-red-900' : 'bg-slate-50 text-slate-500 ring-slate-200 dark:bg-slate-900 dark:text-slate-400 dark:ring-slate-800' }}">
                                        <i class="fab fa-youtube mr-1"></i>{{ $course->youtube_url ? 'Video' : 'No video' }}
                                    </span>
                                    <span class="rounded-lg px-2.5 py-1.5 ring-1 {{ count($course->quiz_questions ?? []) > 0 ? 'bg-green-50 text-green-700 ring-green-100 dark:bg-green-950/35 dark:text-green-300 dark:ring-green-900' : 'bg-slate-50 text-slate-500 ring-slate-200 dark:bg-slate-900 dark:text-slate-400 dark:ring-slate-800' }}">
                                        <i class="fas fa-clipboard-question mr-1"></i>{{ count($course->quiz_questions ?? []) }} quiz
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm font-black text-slate-600 dark:text-slate-400">{{ $course->sort_order }}</td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap justify-end gap-2">
                                    @if($course->is_active)
                                        <a href="{{ route('tutorial.show', $course) }}" class="ui-btn ui-btn-ghost min-h-10 px-3"><i class="fas fa-eye"></i></a>
                                    @endif
                                    <x-ui.button :href="route('admin.courses.edit', $course)" variant="secondary" class="min-h-10 px-3"><i class="fas fa-pen"></i> Edit</x-ui.button>
                                    <form method="POST" action="{{ route('admin.courses.toggle', $course) }}">
                                        @csrf
                                        @method('PATCH')
                                        <x-ui.button type="submit" variant="secondary" class="min-h-10 px-3">
                                            <i class="fas {{ $course->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                            {{ $course->is_active ? 'Hide' : 'Show' }}
                                        </x-ui.button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" onsubmit="return confirm('Delete this course?')">
                                        @csrf
                                        @method('DELETE')
                                        <x-ui.button type="submit" variant="danger" class="min-h-10 px-3"><i class="fas fa-trash"></i></x-ui.button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-sm font-semibold text-slate-500">No courses found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>

    @if($courses->hasPages())
        <x-ui.card class="mt-6 p-4">{{ $courses->links() }}</x-ui.card>
    @endif
</x-ui.page>
@endsection
