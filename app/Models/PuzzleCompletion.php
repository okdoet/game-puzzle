<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PuzzleCompletion extends Model
{
    protected $fillable = ['user_id', 'puzzle_level_id', 'time_taken'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function puzzleLevel()
    {
        return $this->belongsTo(PuzzleLevel::class);
    }
}
