<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::view('/about', 'about')->name('about');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

use App\Http\Controllers\Admin\PuzzleController;
use App\Http\Controllers\Customer\GameController;
use App\Http\Controllers\Customer\TicTacToeController;
use App\Http\Controllers\Customer\MemoryMatchController;
use App\Http\Controllers\Customer\Game2048Controller;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            if (auth()->user()->role !== 'admin') abort(403);
            return redirect()->route('admin.puzzles.index');
        });
        
        Route::resource('puzzles', PuzzleController::class);
    });

    Route::prefix('customer')->name('customer.')->group(function () {
        Route::get('/dashboard', function () {
            if (auth()->user()->role !== 'customer') abort(403);
            return view('customer.dashboard');
        })->name('dashboard');

        Route::get('/game', [GameController::class, 'index'])->name('game.index');
        Route::get('/game/{id}/play', [GameController::class, 'play'])->name('game.play');
        Route::post('/game/{id}/complete', [GameController::class, 'complete'])->name('game.complete');

        Route::get('/tictactoe', [TicTacToeController::class, 'index'])->name('tictactoe.index');
        Route::get('/memory-match', [MemoryMatchController::class, 'index'])->name('memory-match.index');
        Route::get('/2048', [Game2048Controller::class, 'index'])->name('2048.index');
        Route::get('/snake', function() { return view('customer.snake.index'); })->name('snake.index');
        Route::get('/sudoku', function() { return view('customer.sudoku.index'); })->name('sudoku.index');
        
        Route::get('/leaderboards', [\App\Http\Controllers\Customer\LeaderboardController::class, 'index'])->name('leaderboards.index');
        Route::get('/leaderboards/puzzle/{level}', [\App\Http\Controllers\Customer\LeaderboardController::class, 'puzzle'])->name('leaderboards.puzzle');
        Route::get('/leaderboards/{game}', [\App\Http\Controllers\Customer\LeaderboardController::class, 'game'])->name('leaderboards.game');
        
        Route::post('/game-scores', [\App\Http\Controllers\Customer\GameScoreController::class, 'store'])->name('game-scores.store');
    });
});
