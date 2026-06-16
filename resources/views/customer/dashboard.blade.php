@extends('layouts.app')

@section('title', 'Customer Dashboard')

@section('content')
<div style="padding: 2rem 0 1rem; font-family: var(--font-sans, system-ui, sans-serif);">

    {{-- Page header --}}
    <div style="text-align: center; margin-bottom: 2.5rem;">
        <p style="font-size: 11px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text-muted); margin: 0 0 0.75rem;">Game center</p>
        <h1 style="font-size: 2rem; font-weight: 500; color: var(--text-color, #111); margin: 0 0 0.4rem; line-height: 1.2;">Choose your game</h1>
        <p style="font-size: 0.9375rem; color: var(--text-muted); margin: 0;">Pick something and start playing</p>
    </div>

    {{-- Game cards grid --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem; margin-bottom: 1rem; max-width: 900px; margin-left: auto; margin-right: auto;">

        {{-- Puzzle --}}
        <div class="game-card" style="background: var(--card-bg, #fff); border: 0.5px solid var(--border-color); border-radius: 12px; overflow: hidden; transition: border-color 0.2s;">
            <div style="height: 120px; background: var(--card-banner-bg, #f8f8f9); border-bottom: 0.5px solid var(--border-color); display: flex; align-items: center; justify-content: center;">
                <span style="font-size: 2.5rem; line-height: 1;">🧩</span>
            </div>
            <div style="padding: 1rem 1.25rem 1.25rem;">
                <p style="font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase; color: var(--text-muted); margin: 0 0 4px; font-weight: 500;">Logic</p>
                <p style="font-size: 1.0625rem; font-weight: 500; color: var(--text-color, #111); margin: 0 0 6px;">Puzzle</p>
                <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0 0 1rem; line-height: 1.55;">Solve grid puzzles and sharpen your problem-solving skills one move at a time.</p>
                <div style="display: flex; gap: 8px; margin-bottom: 14px; flex-wrap: wrap;">
                    <span class="stat-chip">⭐ Popular</span>
                </div>
                <a href="{{ route('customer.game.index') }}" class="card-action-link">
                    Play puzzle <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>

        {{-- Tic Tac Toe --}}
        <div class="game-card" style="background: var(--card-bg, #fff); border: 0.5px solid var(--border-color); border-radius: 12px; overflow: hidden; transition: border-color 0.2s;">
            <div style="height: 120px; background: var(--card-banner-bg, #f8f8f9); border-bottom: 0.5px solid var(--border-color); display: flex; align-items: center; justify-content: center; gap: 8px;">
                <span style="font-size: 2rem; line-height: 1;">⭕</span>
                <span style="font-size: 0.875rem; color: var(--text-muted); font-weight: 500;">vs</span>
                <span style="font-size: 2rem; line-height: 1;">❌</span>
            </div>
            <div style="padding: 1rem 1.25rem 1.25rem;">
                <p style="font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase; color: var(--text-muted); margin: 0 0 4px; font-weight: 500;">Strategy</p>
                <p style="font-size: 1.0625rem; font-weight: 500; color: var(--text-color, #111); margin: 0 0 6px;">Tic Tac Toe</p>
                <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0 0 1rem; line-height: 1.55;">Challenge a friend or go head-to-head against an AI opponent.</p>
                <div style="display: flex; gap: 8px; margin-bottom: 14px; flex-wrap: wrap;">
                    <span class="stat-chip">🤖 AI opponent</span>
                    <span class="stat-chip">👫 2-player</span>
                </div>
                <a href="{{ route('customer.tictactoe.index') }}" class="card-action-link">
                    Play Tic Tac Toe <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>

        {{-- Memory Match --}}
        <div class="game-card" style="background: var(--card-bg, #fff); border: 0.5px solid var(--border-color); border-radius: 12px; overflow: hidden; transition: border-color 0.2s;">
            <div style="height: 120px; background: var(--card-banner-bg, #f8f8f9); border-bottom: 0.5px solid var(--border-color); display: flex; align-items: center; justify-content: center;">
                <span style="font-size: 2.5rem; line-height: 1;">🃏</span>
            </div>
            <div style="padding: 1rem 1.25rem 1.25rem;">
                <p style="font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase; color: var(--text-muted); margin: 0 0 4px; font-weight: 500;">Memory</p>
                <p style="font-size: 1.0625rem; font-weight: 500; color: var(--text-color, #111); margin: 0 0 6px;">Memory Match</p>
                <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0 0 1rem; line-height: 1.55;">Flip cards and find all matching pairs. A classic test of focus and recall.</p>
                <div style="display: flex; gap: 8px; margin-bottom: 14px; flex-wrap: wrap;">
                    <span class="stat-chip">🧠 Brain training</span>
                    <span class="stat-chip">⏱ Quick rounds</span>
                </div>
                <a href="{{ route('customer.memory-match.index') }}" class="card-action-link">
                    Play Memory Match <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>

        {{-- 2048 --}}
        <div class="game-card" style="background: var(--card-bg, #fff); border: 0.5px solid var(--border-color); border-radius: 12px; overflow: hidden; transition: border-color 0.2s;">
            <div style="height: 120px; background: var(--card-banner-bg, #f8f8f9); border-bottom: 0.5px solid var(--border-color); display: flex; align-items: center; justify-content: center;">
                <div style="width: 72px; height: 72px; background: var(--2048-bg, #fef3c7); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <span style="font-size: 1.375rem; font-weight: 600; color: var(--2048-text, #92400e); font-family: monospace;">2048</span>
                </div>
            </div>
            <div style="padding: 1rem 1.25rem 1.25rem;">
                <p style="font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase; color: var(--text-muted); margin: 0 0 4px; font-weight: 500;">Numbers</p>
                <p style="font-size: 1.0625rem; font-weight: 500; color: var(--text-color, #111); margin: 0 0 6px;">2048</p>
                <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0 0 1rem; line-height: 1.55;">Slide and merge tiles to reach the elusive 2048 tile. Satisfying and addictive.</p>
                <div style="display: flex; gap: 8px; margin-bottom: 14px; flex-wrap: wrap;">
                    <span class="stat-chip">📈 High scores</span>
                    <span class="stat-chip">👆 Swipe to play</span>
                </div>
                <a href="{{ route('customer.2048.index') }}" class="card-action-link">
                    Play 2048 <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>

        {{-- Snake --}}
        <div class="game-card" style="background: var(--card-bg, #fff); border: 0.5px solid var(--border-color); border-radius: 12px; overflow: hidden; transition: border-color 0.2s;">
            <div style="height: 120px; background: var(--card-banner-bg, #f8f8f9); border-bottom: 0.5px solid var(--border-color); display: flex; align-items: center; justify-content: center;">
                <span style="font-size: 2.5rem; line-height: 1;">🐍</span>
            </div>
            <div style="padding: 1rem 1.25rem 1.25rem;">
                <p style="font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase; color: var(--text-muted); margin: 0 0 4px; font-weight: 500;">Arcade</p>
                <p style="font-size: 1.0625rem; font-weight: 500; color: var(--text-color, #111); margin: 0 0 6px;">Snake</p>
                <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0 0 1rem; line-height: 1.55;">Grow your snake by eating apples, but avoid hitting the walls or yourself.</p>
                <div style="display: flex; gap: 8px; margin-bottom: 14px; flex-wrap: wrap;">
                    <span class="stat-chip">⏱ Reflexes</span>
                    <span class="stat-chip">🍎 Classic</span>
                </div>
                <a href="{{ route('customer.snake.index') }}" class="card-action-link">
                    Play Snake <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>

        {{-- Sudoku --}}
        <div class="game-card" style="background: var(--card-bg, #fff); border: 0.5px solid var(--border-color); border-radius: 12px; overflow: hidden; transition: border-color 0.2s;">
            <div style="height: 120px; background: var(--card-banner-bg, #f8f8f9); border-bottom: 0.5px solid var(--border-color); display: flex; align-items: center; justify-content: center;">
                <span style="font-size: 2.5rem; line-height: 1;">🔢</span>
            </div>
            <div style="padding: 1rem 1.25rem 1.25rem;">
                <p style="font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase; color: var(--text-muted); margin: 0 0 4px; font-weight: 500;">Puzzle</p>
                <p style="font-size: 1.0625rem; font-weight: 500; color: var(--text-color, #111); margin: 0 0 6px;">Sudoku</p>
                <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0 0 1rem; line-height: 1.55;">Fill the 9x9 grid with numbers based on logic.</p>
                <div style="display: flex; gap: 8px; margin-bottom: 14px; flex-wrap: wrap;">
                    <span class="stat-chip">🧩 Logic</span>
                    <span class="stat-chip">✍️ Pencil marks</span>
                </div>
                <a href="{{ route('customer.sudoku.index') }}" class="card-action-link">
                    Play Sudoku <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>

    </div>

    {{-- Leaderboard banner --}}
    <div style="max-width: 900px; margin: 0 auto; background: var(--card-bg, #fff); border: 0.5px solid var(--border-color); border-radius: 12px; padding: 1.5rem 1.75rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
        <div>
            <p style="font-size: 1.0625rem; font-weight: 500; color: var(--text-color, #111); margin: 0 0 4px;">🏆 Global leaderboards</p>
            <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0;">See the top players across all games and difficulty levels.</p>
        </div>
        <a href="{{ route('customer.leaderboards.index') }}" class="btn" style="white-space: nowrap;">
            View rankings
        </a>
    </div>

</div>

<style>
    .game-card:hover {
        border-color: rgba(0, 0, 0, 0.2);
    }

    .card-action-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--primary);
        text-decoration: none;
        transition: transform 0.2s, color 0.2s;
    }

    .game-card:hover .card-action-link {
        transform: translateX(4px);
    }

    .stat-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--chip-bg, #f3f4f6);
        border-radius: 100px;
        padding: 2px 10px;
        font-size: 0.75rem;
        color: var(--text-muted);
    }
</style>
@endsection