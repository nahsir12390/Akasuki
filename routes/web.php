<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\RealtimeDiagnosticsController;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\TutorialController;
use App\Http\Controllers\FriendshipController;
use App\Http\Controllers\SocialLoginController;
use App\Http\Controllers\ForgetpasswordController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

// Authentication routes
Route::middleware('guest')->controller(AuthController::class)->group(function(){
    Route::get('/login', 'showLogin')->name('show.login');
    Route::post('/login', 'login')->name('login');
    Route::get('/register', 'showRegister')->name('show.register');
    Route::post('/register', 'register')->name('register');
});

Route::middleware('guest')->controller(ForgetpasswordController::class)->group(function () {
    Route::get('/forgotpassword', 'showForgotPassword')->name('show.forgot.password');
    Route::post('/forgotpassword', 'forgotPassword')->name('forgot.password');
    Route::get('/reset-password', 'showResetPassword')->name('password.reset');
    Route::post('/reset-password', 'resetPassword')->name('password.update');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Chat routes
    Route::controller(ChatController::class)->group(function () {
        Route::get('/chat', 'index')->name('chat.index');
        Route::post('/chat/send', 'send')->name('chat.send');
        Route::get('/chat/user/{user}', 'loadChat')->name('chat.load');
        Route::get('/chat/search-users', 'searchUsers')->name('chat.search');
        Route::get('/chat/unread-count', 'getUnreadCount')->name('chat.unread-count');
    });

    Route::controller(NotificationController::class)->group(function () {
        Route::get('/notifications', 'index')->name('notifications.index');
        Route::patch('/notifications/read-all', 'markAllAsRead')->name('notifications.read-all');
        Route::patch('/notifications/{notification}/read', 'markAsRead')->name('notifications.read');
        Route::delete('/notifications/{notification}', 'destroy')->name('notifications.destroy');
    });

    Route::controller(PushSubscriptionController::class)->group(function () {
        Route::get('/push/public-key', 'publicKey')->name('push.public-key');
        Route::post('/push/subscriptions', 'store')->name('push.subscriptions.store');
        Route::delete('/push/subscriptions', 'destroy')->name('push.subscriptions.destroy');
    });

    Route::get('/realtime/diagnostics', RealtimeDiagnosticsController::class)->name('realtime.diagnostics');

    // Tutorial routes
    Route::controller(TutorialController::class)->group(function() {
        Route::get('/tutorial/html', 'html')->name('tutorial.html');
        Route::get('/tutorial/css', 'css')->name('tutorial.css');
        Route::get('/tutorial/js', 'js')->name('tutorial.js');
        Route::get('/tutorial/php', 'php')->name('tutorial.php');
        Route::get('/tutorial/laravel', 'laravel')->name('tutorial.laravel');
        Route::get('/tutorial/vue', 'vue')->name('tutorial.vue');
        Route::get('/tutorial/react', 'react')->name('tutorial.react');
        Route::get('/tutorial/python', 'python')->name('tutorial.python');
        Route::get('/tutorial/java', 'java')->name('tutorial.java');
        Route::get('/tutorial/csharp', 'csharp')->name('tutorial.csharp');
        Route::get('/tutorial/ruby', 'ruby')->name('tutorial.ruby');
        Route::get('/tutorial/mysql', 'mysql')->name('tutorial.mysql');
        Route::get('/tutorial/jquery', 'jquery')->name('tutorial.jquery');
        Route::get('/tutorial/cpp', 'cpp')->name('tutorial.cpp');
        Route::get('/tutorial/{course:slug}', 'show')->name('tutorial.show');
        Route::post('/tutorial/{course:slug}/quiz', 'submitQuiz')->name('tutorial.quiz.submit');
    });

    Route::get('/games', [GameController::class, 'index'])->name('games.index');

    // Friendship routes
    Route::prefix('friends')->controller(FriendshipController::class)->group(function () {
        Route::get('/', 'friendsList')->name('friends.list');
        Route::get('/requests', 'pendingRequests')->name('friends.requests');
        Route::get('/mutual/{user}', 'mutualFriends')->name('friends.mutual');
        Route::get('/search', 'searchFriends')->name('friends.search');
        
        // Actions
        Route::post('/send/{user}', 'sendRequest')->name('friends.send');
        Route::post('/accept/{user}', 'acceptRequest')->name('friends.accept');
        Route::post('/reject/{user}', 'rejectRequest')->name('friends.reject');
        Route::delete('/cancel/{user}', 'cancelRequest')->name('friends.cancel');
        Route::delete('/remove/{user}', 'removeFriend')->name('friends.remove');
        Route::post('/block/{user}', 'blockUser')->name('friends.block');
        Route::post('/unblock/{user}', 'unblockUser')->name('friends.unblock');
        Route::post('/bulk-actions', 'bulkActions')->name('friends.bulk-actions');
        Route::post('/toggle/{user}', 'toggleRequest')->name('friends.toggle');
        
        // API endpoints
        Route::get('/status/{user}', 'getFriendshipStatus')->name('friends.status');
        Route::get('/stats', 'getFriendshipStats')->name('friends.stats');
        Route::get('/recent', 'getRecentFriends')->name('friends.recent');
        Route::get('/can-send/{user}', 'canSendRequest')->name('friends.can-send');
    });

    // Post routes
    Route::controller(PostController::class)->group(function(){
        Route::get('/post', 'index')->name('show.post'); 
        Route::get('/post/create', 'create')->name('createData');
        Route::post('/store','store')->name('storeData');
        Route::get('/post/{id}/edit', 'edit')->name('editData');
        Route::put('/post/{id}', 'update')->name('updateData');
        Route::delete('/post/{id}', 'destroy')->name('deleteData');
    });

    // Like routes
    Route::controller(LikeController::class)->group(function(){
        Route::post('/posts/{post}/like', 'like')->name('posts.like');
        Route::delete('/posts/{post}/unlike', 'unlike')->name('posts.unlike');
    });

    // Comment routes
    Route::controller(CommentController::class)->group(function () {
        Route::post('/posts/{post}/comments', 'store')->name('comments.store');
        Route::get('/comments/{comment}/edit', 'edit')->name('comments.edit');
        Route::put('/comments/{comment}', 'update')->name('comments.update');
        Route::delete('/comments/{comment}', 'destroy')->name('comments.destroy');
        Route::get('/posts/{post}/comments', 'getComments')->name('comments.get');
    });

    // User profile routes
    Route::controller(UserController::class)->group(function () {
        Route::get('/user/{user}', 'profile')->name('user.profile');
        Route::get('/my-profile', function () {
            return redirect()->route('user.profile', auth()->id());
        })->name('user.myprofile');
        Route::post('/profile/photo', 'updateProfilePhoto')->name('profile.photo');
        Route::delete('/profile/photo', 'removeProfilePhoto')->name('profile.photo.remove');
        Route::get('/users/search', 'searchUsers')->name('users.search');
        Route::get('/users/quick-search', 'quickSearch')->name('users.quick-search');
        
        // Account settings routes
        Route::get('/account/settings', 'accountSettings')->name('account.settings');
        Route::get('/account/edit', 'editAccount')->name('account.edit');
        Route::put('/account/update', 'updateAccount')->name('account.update');
        Route::put('/account/update-password', 'updatePassword')->name('account.update-password');
        Route::put('/account/update-preferences', 'updatePreferences')->name('account.update-preferences');
        Route::match(['get', 'patch'], '/account/update-skills', 'updateSkills')->name('account.update-skills');
    });

    Route::prefix('admin')->name('admin.')->middleware('admin')->controller(AdminController::class)->group(function () {
        Route::get('/', 'dashboard')->name('dashboard');
        Route::get('/users', 'users')->name('users');
        Route::patch('/users/{user}/admin', 'toggleAdmin')->name('users.toggle-admin');
        Route::get('/posts', 'posts')->name('posts');
        Route::delete('/posts/{post}', 'destroyPost')->name('posts.destroy');
        Route::get('/quizzes', 'quizzes')->name('quizzes');
        Route::patch('/quizzes/{submission}', 'reviewQuiz')->name('quizzes.review');
        Route::get('/courses', 'courses')->name('courses');
        Route::get('/courses/create', 'createCourse')->name('courses.create');
        Route::post('/courses', 'storeCourse')->name('courses.store');
        Route::get('/courses/{course}/edit', 'editCourse')->name('courses.edit');
        Route::put('/courses/{course}', 'updateCourse')->name('courses.update');
        Route::patch('/courses/{course}/toggle', 'toggleCourse')->name('courses.toggle');
        Route::delete('/courses/{course}', 'destroyCourse')->name('courses.destroy');
    });
});

// Social Login Routes (accessible without auth)
Route::controller(SocialLoginController::class)->group(function () {
    Route::get('/auth/google', 'redirectToGoogle')->name('auth.google');
    Route::get('/auth/google/callback', 'handleGoogleCallback');
    
    Route::get('/auth/github', 'redirectToGithub')->name('auth.github');
    Route::get('/auth/github/callback', 'handleGithubCallback');
});

// Contact route (accessible without auth)
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

// Logout route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Broadcast authentication route (important for WebSocket authentication)
Route::post('/broadcasting/auth', function () {
    return Broadcast::auth(request());
})->middleware(['auth']);
