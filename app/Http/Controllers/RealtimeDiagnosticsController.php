<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class RealtimeDiagnosticsController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'broadcast' => [
                'connection' => config('broadcasting.default'),
                'reverb_configured' => filled(config('broadcasting.connections.reverb.key'))
                    && filled(config('broadcasting.connections.reverb.secret'))
                    && filled(config('broadcasting.connections.reverb.app_id'))
                    && filled(config('broadcasting.connections.reverb.options.host')),
                'host' => config('broadcasting.connections.reverb.options.host'),
                'port' => config('broadcasting.connections.reverb.options.port'),
                'scheme' => config('broadcasting.connections.reverb.options.scheme'),
                'key_preview' => filled(config('broadcasting.connections.reverb.key'))
                    ? substr(config('broadcasting.connections.reverb.key'), 0, 4) . '...' . substr(config('broadcasting.connections.reverb.key'), -4)
                    : null,
            ],
            'push' => [
                'configured' => filled(config('webpush.vapid.public_key')) && filled(config('webpush.vapid.private_key')),
                'public_key_present' => filled(config('webpush.vapid.public_key')),
                'private_key_present' => filled(config('webpush.vapid.private_key')),
                'subject' => config('webpush.vapid.subject'),
            ],
        ]);
    }
}
