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
