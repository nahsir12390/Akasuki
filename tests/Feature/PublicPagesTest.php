<?php

use App\Mail\ContactFormMail;
use Illuminate\Support\Facades\Mail;

test('public pages render successfully', function (string $uri, string $expectedText) {
    $this->get($uri)
        ->assertOk()
        ->assertSee($expectedText, false);
})->with([
    ['/', 'Akatsuki Devs'],
    ['/about', 'Built for developers'],
    ['/contact', 'Send a message scroll'],
    ['/login', 'Welcome Back'],
    ['/register', 'Create Account'],
    ['/forgotpassword', 'Recover your village access'],
    ['/reset-password?token=test-token&email=shinobi@example.com', 'Reset Password'],
]);

test('contact form sends a subject with the message', function () {
    Mail::fake();

    $this->post(route('contact.send'), [
        'name' => 'Test Shinobi',
        'email' => 'shinobi@example.com',
        'subject' => 'Partnership',
        'message' => 'I would like to talk about the developer village.',
    ])->assertRedirect();

    Mail::assertSent(ContactFormMail::class, function (ContactFormMail $mail) {
        return $mail->data['subject'] === 'Partnership'
            && $mail->data['email'] === 'shinobi@example.com';
    });
});

test('pwa assets are available in the public directory', function (string $path, string $expectedText) {
    $absolutePath = public_path($path);

    expect(file_exists($absolutePath))->toBeTrue()
        ->and(file_get_contents($absolutePath))->toContain($expectedText);
})->with([
    ['manifest.webmanifest', 'Akatsuki Devs'],
    ['offline.html', 'You are offline'],
    ['sw.js', 'akatsuki-devs-v1'],
]);

test('pwa icons are available in the public directory', function (string $path) {
    expect(file_exists(public_path($path)))->toBeTrue()
        ->and(filesize(public_path($path)))->toBeGreaterThan(0);
})->with([
    ['icons/icon-192.png'],
    ['icons/icon-512.png'],
    ['icons/maskable-512.png'],
]);
