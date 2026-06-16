@extends('layouts.app')

@section('title', 'Customer Dashboard')

@section('content')
<div class="header" style="text-align: center; margin-bottom: 3rem;">
    <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem; background: linear-gradient(135deg, #a5b4fc, #c084fc); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Choose Your Game</h1>
    <p style="color: var(--text-muted);">Select what you want to play today</p>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; max-width: 800px; margin: 0 auto;">
    
    <!-- Puzzle Card -->
    <div class="card" style="padding: 0; overflow: hidden; position: relative; transition: transform 0.3s; border: 1px solid var(--border-color);">
        <div style="height: 200px; background: linear-gradient(135deg, #3b82f6, #8b5cf6); display: flex; align-items: center; justify-content: center; font-size: 4rem;">
            🧩
        </div>
        <div style="padding: 1.5rem; text-align: center;">
            <h3 style="margin-bottom: 0.5rem; font-size: 1.5rem;">Puzzle</h3>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Solve grid puzzles and challenge your mind.</p>
            <a href="{{ route('customer.game.index') }}" class="btn" style="width: 100%; display: block;">Play Puzzle</a>
        </div>
    </div>

    <!-- Tic Tac Toe Card -->
    <div class="card" style="padding: 0; overflow: hidden; position: relative; transition: transform 0.3s; border: 1px solid var(--border-color);">
        <div style="height: 200px; background: linear-gradient(135deg, #6366f1, #ec4899); display: flex; align-items: center; justify-content: center; font-size: 4rem;">
            ⭕❌
        </div>
        <div style="padding: 1.5rem; text-align: center;">
            <h3 style="margin-bottom: 0.5rem; font-size: 1.5rem;">Tic Tac Toe</h3>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Play against a friend or challenge the AI.</p>
            <a href="{{ route('customer.tictactoe.index') }}" class="btn" style="width: 100%; display: block;">Play Tic Tac Toe</a>
        </div>
    </div>

</div>
@endsection
