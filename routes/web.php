<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

use App\Http\Controllers\Admin\PuzzleController;
use App\Http\Controllers\Customer\GameController;

Route::middleware('auth')->group(function () {
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
            return redirect()->route('customer.game.index');
        });

        Route::get('/game', [GameController::class, 'index'])->name('game.index');
        Route::get('/game/{id}/play', [GameController::class, 'play'])->name('game.play');
        Route::post('/game/{id}/complete', [GameController::class, 'complete'])->name('game.complete');
    });
});
