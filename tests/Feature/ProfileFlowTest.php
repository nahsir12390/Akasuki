<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('the profile page shows the main navigation', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('user.profile', $user))
        ->assertOk()
        ->assertSee('Akatsuki Devs')
        ->assertSee('Community')
        ->assertSee('Messages');
});

test('a user can update their account profile', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->put(route('account.update'), [
            'name' => 'Kakashi Hatake',
            'email' => 'kakashi@example.com',
            'bio' => 'Copy ninja.',
            'location' => 'Konoha',
            'website' => 'https://example.com',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Kakashi Hatake',
        'email' => 'kakashi@example.com',
        'bio' => 'Copy ninja.',
        'location' => 'Konoha',
        'website' => 'https://example.com',
    ]);
});

test('a user can update their password', function () {
    $user = User::factory()->create([
        'password' => Hash::make('old-password'),
    ]);

    $this->actingAs($user)
        ->put(route('account.update-password'), [
            'current_password' => 'old-password',
            'password' => 'New-password1!',
            'password_confirmation' => 'New-password1!',
        ])
        ->assertRedirect();

    $user->refresh();

    expect(Hash::check('New-password1!', $user->password))->toBeTrue();
});

test('settings include notification setup controls', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('account.settings') . '#preferences')
        ->assertOk()
        ->assertSee('Notification setup')
        ->assertSee('Device Alerts')
        ->assertSee('Browser push is controlled per device');
});
