@extends('layouts.app')

@section('title', 'Leaderboards')

@section('content')
<div style="padding: 2rem 0 1rem; font-family: var(--font-sans, system-ui, sans-serif); max-width: 1100px; margin: 0 auto;">
    
    {{-- Page header --}}
    <div style="text-align: center; margin-bottom: 2.5rem;">
        <p style="font-size: 11px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text-muted); margin: 0 0 0.75rem;">Rankings</p>
        <h1 style="font-size: 2rem; font-weight: 500; color: var(--text-color, #111); margin: 0 0 0.4rem; line-height: 1.2;">Global Leaderboards</h1>
        <p style="font-size: 0.9375rem; color: var(--text-muted); margin: 0;">See who the best players are across all games!</p>
    </div>

    <div class="leaderboards-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 2rem;">
        
        {{-- 2048 Leaderboard --}}
        <div class="game-card" style="background: var(--card-bg, #fff); border: 0.5px solid var(--border-color); border-radius: 12px; overflow: hidden; transition: border-color 0.2s;">
            <div style="height: 120px; background: var(--card-banner-bg, #f8f8f9); border-bottom: 0.5px solid var(--border-color); display: flex; align-items: center; justify-content: center;">
                <div style="width: 72px; height: 72px; background: var(--2048-bg, #fef3c7); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                    <span style="font-size: 1.375rem; font-weight: 600; color: var(--2048-text, #92400e); font-family: monospace;">2048</span>
                </div>
            </div>
            <div style="padding: 1rem 1.25rem 1.25rem;">
                <p style="font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase; color: var(--text-muted); margin: 0 0 4px; font-weight: 500;">Numbers</p>
                <p style="font-size: 1.0625rem; font-weight: 500; color: var(--text-color, #111); margin: 0 0 6px;">2048 Game</p>
                <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0 0 1rem; line-height: 1.55;">Ranked by Highest Score</p>
                <a href="{{ route('customer.leaderboards.game', '2048') }}" class="card-action-link">
                    View Top 10 <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>

        {{-- Memory Match Leaderboard --}}
        <div class="game-card" style="background: var(--card-bg, #fff); border: 0.5px solid var(--border-color); border-radius: 12px; overflow: hidden; transition: border-color 0.2s;">
            <div style="height: 120px; background: var(--card-banner-bg, #f8f8f9); border-bottom: 0.5px solid var(--border-color); display: flex; align-items: center; justify-content: center;">
                <span style="font-size: 2.5rem; line-height: 1;">🃏</span>
            </div>
            <div style="padding: 1rem 1.25rem 1.25rem;">
                <p style="font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase; color: var(--text-muted); margin: 0 0 4px; font-weight: 500;">Memory</p>
                <p style="font-size: 1.0625rem; font-weight: 500; color: var(--text-color, #111); margin: 0 0 6px;">Memory Match</p>
                <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0 0 1rem; line-height: 1.55;">Ranked by Fastest Time</p>
                <a href="{{ route('customer.leaderboards.game', 'memory_match') }}" class="card-action-link">
                    View Top 10 <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>

        {{-- Snake Leaderboard --}}
        <div class="game-card" style="background: var(--card-bg, #fff); border: 0.5px solid var(--border-color); border-radius: 12px; overflow: hidden; transition: border-color 0.2s;">
            <div style="height: 120px; background: var(--card-banner-bg, #f8f8f9); border-bottom: 0.5px solid var(--border-color); display: flex; align-items: center; justify-content: center;">
                <span style="font-size: 2.5rem; line-height: 1;">🐍</span>
            </div>
            <div style="padding: 1rem 1.25rem 1.25rem;">
                <p style="font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase; color: var(--text-muted); margin: 0 0 4px; font-weight: 500;">Arcade</p>
                <p style="font-size: 1.0625rem; font-weight: 500; color: var(--text-color, #111); margin: 0 0 6px;">Snake Game</p>
                <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0 0 1rem; line-height: 1.55;">Ranked by Highest Score</p>
                <a href="{{ route('customer.leaderboards.game', 'snake') }}" class="card-action-link">
                    View Top 10 <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>

        {{-- Sudoku Leaderboard --}}
        <div class="game-card" style="background: var(--card-bg, #fff); border: 0.5px solid var(--border-color); border-radius: 12px; overflow: hidden; transition: border-color 0.2s;">
            <div style="height: 120px; background: var(--card-banner-bg, #f8f8f9); border-bottom: 0.5px solid var(--border-color); display: flex; align-items: center; justify-content: center;">
                <span style="font-size: 2.5rem; line-height: 1;">🔢</span>
            </div>
            <div style="padding: 1rem 1.25rem 1.25rem;">
                <p style="font-size: 11px; letter-spacing: 0.08em; text-transform: uppercase; color: var(--text-muted); margin: 0 0 4px; font-weight: 500;">Puzzle</p>
                <p style="font-size: 1.0625rem; font-weight: 500; color: var(--text-color, #111); margin: 0 0 6px;">Sudoku</p>
                <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0 0 1rem; line-height: 1.55;">Ranked by Fastest Time</p>
                <a href="{{ route('customer.leaderboards.game', 'sudoku') }}" class="card-action-link">
                    View Top 10 <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>

    </div>

    <div style="text-align: center; margin-top: 2rem;">
        <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1rem;">Want to see Puzzle Leaderboards?</p>
        <a href="{{ route('customer.game.index') }}" class="btn btn-secondary" style="font-size: 0.875rem; padding: 0.5rem 1rem;">Go to Puzzle Levels</a>
    </div>

</div>

<style>
    @media (max-width: 1024px) {
        .leaderboards-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
    @media (max-width: 600px) {
        .leaderboards-grid {
            grid-template-columns: 1fr !important;
        }
    }

    .game-card:hover {
        border-color: rgba(0, 0, 0, 0.2);
    }
    [data-theme="dark"] .game-card:hover {
        border-color: rgba(255, 255, 255, 0.3);
    }

    .card-action-link {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.8125rem;
        font-weight: 500;
        color: var(--text-color, #111);
        text-decoration: none;
        opacity: 0.5;
        transition: opacity 0.2s;
    }

    .game-card:hover .card-action-link {
        opacity: 1;
    }
</style>
@endsection
