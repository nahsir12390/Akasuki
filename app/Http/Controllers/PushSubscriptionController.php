<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function publicKey(): JsonResponse
    {
        return response()->json([
            'publicKey' => config('webpush.vapid.public_key'),
            'configured' => filled(config('webpush.vapid.public_key')) && filled(config('webpush.vapid.private_key')),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string', 'max:500'],
            'keys.p256dh' => ['required', 'string'],
            'keys.auth' => ['required', 'string'],
            'contentEncoding' => ['nullable', 'string', 'in:aesgcm,aes128gcm'],
        ]);

        $request->user()->updatePushSubscription(
            $validated['endpoint'],
            data_get($validated, 'keys.p256dh'),
            data_get($validated, 'keys.auth'),
            $validated['contentEncoding'] ?? 'aes128gcm'
        );

        return response()->json([
            'success' => true,
            'message' => 'Device subscribed to push notifications.',
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string', 'max:500'],
        ]);

        $request->user()->deletePushSubscription($validated['endpoint']);

        return response()->json([
            'success' => true,
            'message' => 'Device removed from push notifications.',
        ]);
    }
}
