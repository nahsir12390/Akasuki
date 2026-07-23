<?php

use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

test('a password reset request sends an email and stores a hashed token', function () {
    Mail::fake();

    $user = User::factory()->create([
        'email' => 'reset@example.com',
    ]);

    $this->post(route('forgot.password'), [
        'email' => $user->email,
    ])->assertRedirect();

    $record = DB::table('forget_password')->where('email', $user->email)->first();

    expect($record)->not->toBeNull();
    expect($record->token)->not->toBe('');
    expect($record->token)->not->toContain('reset@example.com');

    Mail::assertSent(PasswordResetMail::class);
});

test('a user can reset their password with a valid token', function () {
    $plainToken = 'valid-reset-token';
    $user = User::factory()->create([
        'email' => 'hinata@example.com',
        'password' => Hash::make('old-password'),
    ]);

    DB::table('forget_password')->insert([
        'email' => $user->email,
        'token' => Hash::make($plainToken),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->post(route('password.update'), [
        'email' => $user->email,
        'token' => $plainToken,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])->assertRedirect('/login');

    $user->refresh();

    expect(Hash::check('new-password', $user->password))->toBeTrue();
    expect(DB::table('forget_password')->where('email', $user->email)->exists())->toBeFalse();
});
