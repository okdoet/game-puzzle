<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PuzzleLevel;
use App\Models\PuzzleCompletion;

class GameController extends Controller
{
    public function index()
    {
        $levels = PuzzleLevel::with(['completions' => function ($query) {
            $query->where('user_id', auth()->id());
        }])->get();

        return view('customer.puzzles.index', compact('levels'));
    }

    public function play($id)
    {
        $level = PuzzleLevel::findOrFail($id);
        return view('customer.puzzles.play', compact('level'));
    }

    public function complete(Request $request, $id)
    {
        $request->validate([
            'time_taken' => 'required|integer'
        ]);

        PuzzleCompletion::create([
            'user_id' => auth()->id(),
            'puzzle_level_id' => $id,
            'time_taken' => $request->time_taken
        ]);

        return response()->json(['success' => true]);
    }
}
