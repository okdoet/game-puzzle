<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PuzzleLevel;
use Illuminate\Support\Facades\Storage;

class PuzzleController extends Controller
{
    public function index()
    {
        $levels = PuzzleLevel::latest()->get();
        return view('admin.puzzles.index', compact('levels'));
    }

    public function create()
    {
        return view('admin.puzzles.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|max:5120',
            'difficulty' => 'required|in:easy,medium,hard',
        ]);

        $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
        $request->file('image')->move(public_path('puzzles'), $imageName);
        $imagePath = 'puzzles/' . $imageName;

        $gridSize = 3;
        if ($request->difficulty === 'medium') {
            $gridSize = 6;
        } elseif ($request->difficulty === 'hard') {
            $gridSize = 10;
        }

        PuzzleLevel::create([
            'name' => $request->name,
            'image_path' => $imagePath,
            'difficulty' => $request->difficulty,
            'grid_size' => $gridSize,
        ]);

        return redirect()->route('admin.puzzles.index')->with('success', 'Puzzle level created successfully.');
    }

    public function destroy($id)
    {
        $level = PuzzleLevel::findOrFail($id);
        if ($level->image_path) {
            $fullPath = public_path($level->image_path);
            if (file_exists($fullPath) && is_file($fullPath)) {
                unlink($fullPath);
            }
        }
        $level->delete();

        return redirect()->route('admin.puzzles.index')->with('success', 'Puzzle level deleted.');
    }
}
