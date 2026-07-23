<?php

use App\Models\Post;
use App\Models\Course;
use App\Models\QuizSubmission;
use App\Models\User;

test('regular users cannot access the admin panel', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

test('admins can access the admin panel', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Admin Panel');
});

test('admins can view admin user and post screens', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $post = Post::create([
        'user_id' => User::factory()->create()->id,
        'content' => 'Review this post.',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.users'))
        ->assertOk()
        ->assertSee($admin->email);

    $this->actingAs($admin)
        ->get(route('admin.posts'))
        ->assertOk()
        ->assertSee($post->content);
});

test('admins can create and publish courses', function () {
    \Illuminate\Support\Facades\Http::fake([
        'www.googleapis.com/*' => \Illuminate\Support\Facades\Http::response(['items' => []]),
    ]);

    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->post(route('admin.courses.store'), [
            'title' => 'Advanced TypeScript',
            'slug' => 'advanced-typescript',
            'subtitle' => 'Build typed JavaScript applications with confidence.',
            'icon' => 'fas fa-code',
            'level' => 'Advanced',
            'duration' => '4 weeks',
            'query' => 'advanced typescript full course',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'modules' => "Types\nGenerics\nArchitecture",
            'projects' => "Typed dashboard\nAPI client",
            'checklist' => "Types\nGenerics\nTesting",
            'resources' => "TypeScript Docs | https://www.typescriptlang.org/docs/",
            'quiz_questions' => "What is TypeScript? | A typed JavaScript superset; A database; A CSS framework | A typed JavaScript superset",
            'sort_order' => 50,
            'is_active' => '1',
        ])
        ->assertRedirect(route('admin.courses'));

    $course = Course::where('slug', 'advanced-typescript')->first();

    expect($course)->not->toBeNull()
        ->and($course->modules)->toContain('Generics')
        ->and($course->resources[0]['label'])->toBe('TypeScript Docs')
        ->and($course->youtube_url)->toBe('https://www.youtube.com/watch?v=dQw4w9WgXcQ')
        ->and($course->quiz_questions[0]['answer'])->toBe('A typed JavaScript superset');

    $this->actingAs($admin)
        ->get(route('tutorial.show', $course))
        ->assertOk()
        ->assertSee('Advanced TypeScript');
});

test('admins can grant admin access to another user', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($admin)
        ->patch(route('admin.users.toggle-admin', $user))
        ->assertRedirect();

    expect($user->fresh()->is_admin)->toBeTrue();
});

test('admins cannot remove their own admin access', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->patch(route('admin.users.toggle-admin', $admin))
        ->assertStatus(422);

    expect($admin->fresh()->is_admin)->toBeTrue();
});

test('admins can remove posts from the admin panel', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $post = Post::create([
        'user_id' => User::factory()->create()->id,
        'content' => 'Remove this post.',
    ]);

    $this->actingAs($admin)
        ->delete(route('admin.posts.destroy', $post))
        ->assertRedirect();

    expect(Post::whereKey($post->id)->exists())->toBeFalse();
});

test('admins can review quiz submissions', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $user = User::factory()->create();
    $course = Course::where('slug', 'html')->firstOrFail();
    $submission = QuizSubmission::create([
        'user_id' => $user->id,
        'course_id' => $course->id,
        'answers' => ['HyperText Markup Language'],
        'score' => 1,
        'total_questions' => 1,
        'status' => 'pending',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.quizzes'))
        ->assertOk()
        ->assertSee($user->name);

    $this->actingAs($admin)
        ->patch(route('admin.quizzes.review', $submission), [
            'status' => 'approved',
            'admin_notes' => 'Good work.',
        ])
        ->assertRedirect();

    expect($submission->fresh()->status)->toBe('approved')
        ->and($submission->fresh()->admin_notes)->toBe('Good work.');
});
