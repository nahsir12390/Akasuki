<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('a visitor can register and is logged in', function () {
    $response = $this->post(route('register'), [
        'name' => 'Naruto Uzumaki',
        'email' => 'naruto@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ]);

    $user = User::where('email', 'naruto@example.com')->first();

    $response->assertRedirect(route('user.profile', $user));
    $this->assertAuthenticatedAs($user);
    expect(Hash::check('secret123', $user->password))->toBeTrue();
});

test('a user can log in and log out', function () {
    $user = User::factory()->create([
        'email' => 'sasuke@example.com',
        'password' => Hash::make('password123'),
    ]);

    $this->post(route('login'), [
        'email' => 'sasuke@example.com',
        'password' => 'password123',
    ])->assertRedirect(route('user.profile', $user));

    $this->assertAuthenticatedAs($user);

    $this->post(route('logout'))->assertRedirect(route('home'));

    $this->assertGuest();
});

test('an admin is redirected to the admin dashboard after login', function () {
    $admin = User::factory()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
        'is_admin' => true,
    ]);

    $this->post(route('login'), [
        'email' => 'admin@example.com',
        'password' => 'password123',
    ])->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($admin);
});
