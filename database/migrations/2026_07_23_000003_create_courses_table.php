<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle', 500);
            $table->string('icon')->default('fas fa-code');
            $table->string('level')->default('Intermediate');
            $table->string('duration')->default('4 weeks');
            $table->string('query')->nullable();
            $table->json('modules')->nullable();
            $table->json('projects')->nullable();
            $table->json('checklist')->nullable();
            $table->json('resources')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        $courses = [
            ['Advanced HTML', 'html', 'Build semantic, accessible, SEO-ready interfaces with production-grade markup.', 'fab fa-html5', 'Foundation to Advanced', '3 weeks', 'advanced html semantic accessibility full course'],
            ['Advanced CSS', 'css', 'Design responsive, polished interfaces with layout systems, tokens, and motion.', 'fab fa-css3-alt', 'Intermediate', '4 weeks', 'advanced css responsive design grid flexbox animations course'],
            ['Advanced JavaScript', 'js', 'Move from scripts to maintainable frontend architecture and async applications.', 'fab fa-js', 'Intermediate to Advanced', '5 weeks', 'advanced javascript async modules dom architecture course'],
            ['Advanced PHP', 'php', 'Write clean backend code with OOP, validation, security, and database workflows.', 'fab fa-php', 'Intermediate', '5 weeks', 'advanced php oop security database full course'],
            ['Advanced Laravel', 'laravel', 'Build deployable products with policies, queues, notifications, events, and clean architecture.', 'fab fa-laravel', 'Advanced', '6 weeks', 'advanced laravel policies queues notifications testing course'],
            ['Advanced Vue', 'vue', 'Create reactive interfaces with Composition API, routing, stores, and component discipline.', 'fab fa-vuejs', 'Intermediate', '5 weeks', 'advanced vue composition api pinia router full course'],
            ['Advanced React', 'react', 'Build stateful, accessible, API-driven interfaces with modern React patterns.', 'fab fa-react', 'Intermediate', '5 weeks', 'advanced react hooks state management performance course'],
            ['Advanced Python', 'python', 'Level up with OOP, APIs, automation, testing, data handling, and clean scripts.', 'fab fa-python', 'Intermediate', '5 weeks', 'advanced python oop api automation testing course'],
            ['Advanced Java', 'java', 'Build robust applications with OOP, collections, streams, testing, and backend patterns.', 'fab fa-java', 'Intermediate', '6 weeks', 'advanced java oop collections streams spring course'],
            ['Advanced C#', 'csharp', 'Create maintainable .NET applications with LINQ, async, APIs, and testing.', 'fas fa-code', 'Intermediate', '6 weeks', 'advanced c# dotnet linq async web api course'],
            ['Advanced C++', 'cpp', 'Understand performance, memory, modern C++, algorithms, and systems thinking.', 'fas fa-code', 'Advanced', '7 weeks', 'advanced c++ modern cpp memory algorithms course'],
            ['Advanced Ruby', 'ruby', 'Write expressive Ruby with objects, blocks, testing, gems, and Rails-ready patterns.', 'fas fa-gem', 'Intermediate', '4 weeks', 'advanced ruby oop blocks metaprogramming rails course'],
            ['Advanced MySQL', 'mysql', 'Design reliable databases with indexing, joins, transactions, constraints, and optimization.', 'fas fa-database', 'Intermediate', '5 weeks', 'advanced mysql indexing joins transactions optimization course'],
            ['Practical jQuery', 'jquery', 'Maintain legacy interfaces safely while knowing when to move to modern JavaScript.', 'fas fa-dollar-sign', 'Maintenance', '2 weeks', 'jquery advanced ajax plugins legacy maintenance course'],
        ];

        foreach ($courses as $index => [$title, $slug, $subtitle, $icon, $level, $duration, $query]) {
            DB::table('courses')->insert([
                'title' => $title,
                'slug' => $slug,
                'subtitle' => $subtitle,
                'icon' => $icon,
                'level' => $level,
                'duration' => $duration,
                'query' => $query,
                'modules' => json_encode([
                    'Core concepts and production vocabulary',
                    'Architecture patterns and reusable structure',
                    'Forms, state, data flow, and validation',
                    'Testing, debugging, performance, and security',
                    'Deployment-ready project polish',
                ]),
                'projects' => json_encode([
                    'Premium dashboard module',
                    'Searchable data-driven interface',
                    'Portfolio-ready capstone feature',
                ]),
                'checklist' => json_encode(['Core syntax', 'Architecture', 'Testing', 'Performance', 'Deployment']),
                'resources' => json_encode([
                    ['label' => 'Official Documentation', 'url' => 'https://developer.mozilla.org/'],
                ]),
                'is_active' => true,
                'sort_order' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
