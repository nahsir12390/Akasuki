<?php

use App\Models\User;

test('an authenticated user can read push public key configuration', function () {
    config([
        'webpush.vapid.public_key' => 'public-test-key',
        'webpush.vapid.private_key' => 'private-test-key',
    ]);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->getJson(route('push.public-key'))
        ->assertOk()
        ->assertJson([
            'publicKey' => 'public-test-key',
            'configured' => true,
        ]);
});

test('an authenticated user can save and delete a push subscription', function () {
    $user = User::factory()->create();

    $payload = [
        'endpoint' => 'https://push.example.com/subscription/123',
        'keys' => [
            'p256dh' => 'public-key',
            'auth' => 'auth-token',
        ],
        'contentEncoding' => 'aes128gcm',
    ];

    $this->actingAs($user)
        ->postJson(route('push.subscriptions.store'), $payload)
        ->assertOk()
        ->assertJsonPath('success', true);

    expect($user->pushSubscriptions()->where('endpoint', $payload['endpoint'])->exists())->toBeTrue();

    $this->actingAs($user)
        ->deleteJson(route('push.subscriptions.destroy'), [
            'endpoint' => $payload['endpoint'],
        ])
        ->assertOk()
        ->assertJsonPath('success', true);

    expect($user->pushSubscriptions()->where('endpoint', $payload['endpoint'])->exists())->toBeFalse();
});
