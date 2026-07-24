<?php

namespace App\Http\Controllers;

use App\Models\GameScore;
use App\Services\AchievementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GameController extends Controller
{
    private const GAME_MODES = [
        'memory' => 'Code Memory',
        'sequence' => 'Chakra Sequence',
        'syntax' => 'Syntax Sprint',
        'debug' => 'Debug Hunt',
    ];

    public function index(): View
    {
        $user = request()->user();
        $gameModes = self::GAME_MODES;

        $personalBests = $user->gameScores()
            ->selectRaw('game_slug, MAX(score) as best_score')
            ->groupBy('game_slug')
            ->pluck('best_score', 'game_slug');

        $leaderboards = collect($gameModes)
            ->keys()
            ->mapWithKeys(fn (string $slug) => [
                $slug => GameScore::with('user:id,name,profile_photo,avatar')
                    ->where('game_slug', $slug)
                    ->orderByDesc('score')
                    ->orderBy('completed_at')
                    ->limit(5)
                    ->get(),
            ]);

        return view('games.index', compact('gameModes', 'personalBests', 'leaderboards'));
    }

    public function storeScore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'game_slug' => ['required', 'string', 'in:' . implode(',', array_keys(self::GAME_MODES))],
            'score' => ['required', 'integer', 'min:0', 'max:100000'],
            'meta' => ['nullable', 'array'],
        ]);

        $score = $request->user()->gameScores()->create([
            'game_slug' => $validated['game_slug'],
            'score' => $validated['score'],
            'meta' => $validated['meta'] ?? [],
            'completed_at' => now(),
        ]);

        AchievementService::sync($request->user());

        $personalBest = $request->user()
            ->gameScores()
            ->where('game_slug', $score->game_slug)
            ->max('score');

        $leaderboard = GameScore::with('user:id,name,profile_photo,avatar')
            ->where('game_slug', $score->game_slug)
            ->orderByDesc('score')
            ->orderBy('completed_at')
            ->limit(5)
            ->get()
            ->map(fn (GameScore $entry) => [
                'name' => $entry->user?->name ?? 'Shinobi',
                'score' => $entry->score,
            ]);

        return response()->json([
            'message' => 'Score saved',
            'personal_best' => (int) $personalBest,
            'leaderboard' => $leaderboard,
        ]);
    }
}
