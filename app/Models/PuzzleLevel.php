<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PuzzleLevel extends Model
{
    protected $fillable = ['name', 'image_path', 'difficulty', 'grid_size'];

    public function completions()
    {
        return $this->hasMany(PuzzleCompletion::class);
    }
}
