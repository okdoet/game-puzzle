<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GameScore;

class GameScoreController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'game_type' => 'required|in:2048,memory_match,snake,sudoku',
            'score' => 'required|integer',
        ]);

        $score = GameScore::where('user_id', auth()->id())
            ->where('game_type', $request->game_type)
            ->first();

        if ($score) {
            // Update if better
            if (in_array($request->game_type, ['2048', 'snake']) && $request->score > $score->score) {
                $score->update(['score' => $request->score]);
            } elseif (in_array($request->game_type, ['memory_match', 'sudoku']) && $request->score < $score->score) {
                $score->update(['score' => $request->score]);
            }
        } else {
            GameScore::create([
                'user_id' => auth()->id(),
                'game_type' => $request->game_type,
                'score' => $request->score,
            ]);
        }

        return response()->json(['status' => 'success']);
    }
}
