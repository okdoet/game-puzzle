<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PuzzleLevel;

class PuzzleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $puzzles = [
            [
                'name' => 'Home Puzzle',
                'image_path' => 'puzzles/home.png',
                'difficulty' => 'easy',
                'grid_size' => 3,
            ],
            [
                'name' => 'Paint Puzzle',
                'image_path' => 'puzzles/paint.png',
                'difficulty' => 'easy',
                'grid_size' => 3,
            ],
            [
                'name' => 'Profile Puzzle',
                'image_path' => 'puzzles/profil.png',
                'difficulty' => 'medium',
                'grid_size' => 6,
            ],
            [
                'name' => 'Screenshot 19',
                'image_path' => 'puzzles/Screenshot (19).png',
                'difficulty' => 'hard',
                'grid_size' => 10,
            ],
            [
                'name' => 'Screenshot 20',
                'image_path' => 'puzzles/hantu.png',
                'difficulty' => 'hard',
                'grid_size' => 10,
            ],
        ];

        foreach ($puzzles as $puzzle) {
            PuzzleLevel::create($puzzle);
        }
    }
}
