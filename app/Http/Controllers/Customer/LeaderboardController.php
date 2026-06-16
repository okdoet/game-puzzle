<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GameScore;
use App\Models\PuzzleCompletion;
use App\Models\PuzzleLevel;

class LeaderboardController extends Controller
{
    public function index()
    {
        return view('customer.leaderboards.index');
    }

    public function game($game)
    {
        $validGames = [
            '2048' => '2048 Game', 
            'memory_match' => 'Memory Match',
            'snake' => 'Snake Game',
            'sudoku' => 'Sudoku'
        ];
        if (!array_key_exists($game, $validGames)) abort(404);

        // 2048 and snake sort desc (highest score)
        // memory_match and sudoku sort asc (lowest time)
        $order = in_array($game, ['2048', 'snake']) ? 'desc' : 'asc';
        $scores = GameScore::with('user')
            ->where('game_type', $game)
            ->orderBy('score', $order)
            ->limit(10)
            ->get();

        return view('customer.leaderboards.show', [
            'title' => 'Top 10 - ' . $validGames[$game],
            'type' => $game,
            'scores' => $scores
        ]);
    }

    public function puzzle($levelId)
    {
        $level = PuzzleLevel::findOrFail($levelId);
        
        $scores = PuzzleCompletion::with('user')
            ->where('puzzle_level_id', $level->id)
            ->orderBy('time_taken', 'asc')
            ->limit(10)
            ->get();

        return view('customer.leaderboards.show', [
            'title' => 'Top 10 - ' . $level->name,
            'type' => 'puzzle',
            'scores' => $scores
        ]);
    }
}
