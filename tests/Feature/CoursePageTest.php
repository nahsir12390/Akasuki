<?php

test('all course pages render with the advanced course layout', function (string $route, string $title) {
    \Illuminate\Support\Facades\Http::fake([
        'googleapis.com/*' => \Illuminate\Support\Facades\Http::response(['items' => []]),
        'www.googleapis.com/*' => \Illuminate\Support\Facades\Http::response(['items' => []]),
    ]);

    $user = \App\Models\User::factory()->create();

    $this->actingAs($user)
        ->get(route($route))
        ->assertOk()
        ->assertSee($title)
        ->assertSee('Advanced Learning Path')
        ->assertSee('Build These Projects');
})->with([
    ['tutorial.html', 'Advanced HTML'],
    ['tutorial.css', 'Advanced CSS'],
    ['tutorial.js', 'Advanced JavaScript'],
    ['tutorial.php', 'Advanced PHP'],
    ['tutorial.laravel', 'Advanced Laravel'],
    ['tutorial.vue', 'Advanced Vue'],
    ['tutorial.react', 'Advanced React'],
    ['tutorial.python', 'Advanced Python'],
    ['tutorial.java', 'Advanced Java'],
    ['tutorial.csharp', 'Advanced C#'],
    ['tutorial.cpp', 'Advanced C++'],
    ['tutorial.ruby', 'Advanced Ruby'],
    ['tutorial.mysql', 'Advanced MySQL'],
    ['tutorial.jquery', 'Practical jQuery'],
]);

test('a learner can submit a course quiz for admin review', function () {
    \Illuminate\Support\Facades\Http::fake([
        'googleapis.com/*' => \Illuminate\Support\Facades\Http::response(['items' => []]),
        'www.googleapis.com/*' => \Illuminate\Support\Facades\Http::response(['items' => []]),
    ]);

    $user = \App\Models\User::factory()->create();
    $course = \App\Models\Course::where('slug', 'html')->firstOrFail();
    $course->update([
        'quiz_questions' => [
            [
                'question' => 'What does HTML stand for?',
                'options' => ['HyperText Markup Language', 'Home Tool Markup Language'],
                'answer' => 'HyperText Markup Language',
            ],
        ],
    ]);

    $this->actingAs($user)
        ->get(route('tutorial.show', $course))
        ->assertOk()
        ->assertSee('Quiz Challenge');

    $this->actingAs($user)
        ->post(route('tutorial.quiz.submit', $course), [
            'answers' => ['HyperText Markup Language'],
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('quiz_submissions', [
        'user_id' => $user->id,
        'course_id' => $course->id,
        'score' => 1,
        'total_questions' => 1,
        'status' => 'pending',
    ]);
});

test('an authenticated user can open the games page', function () {
    $user = \App\Models\User::factory()->create();

    $this->actingAs($user)
        ->get(route('games.index'))
        ->assertOk()
        ->assertSee('Training Games')
        ->assertSee('Code Memory')
        ->assertSee('Chakra Sequence')
        ->assertSee('Syntax Sprint')
        ->assertSee('Debug Hunt');
});
