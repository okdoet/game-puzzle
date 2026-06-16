@extends('layouts.app')

@section('title', $title)

@push('styles')
<style>
    .leaderboard-table {
        width: 100%;
        border-collapse: collapse;
    }
    .leaderboard-table th, .leaderboard-table td {
        padding: 1rem 1.25rem;
        text-align: left;
        border-bottom: 0.5px solid var(--border-color);
    }
    .leaderboard-table th {
        color: var(--text-muted);
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.08em;
        font-weight: 500;
        background: var(--card-banner-bg, #f8f8f9);
    }
    .leaderboard-table th:first-child { border-top-left-radius: 12px; }
    .leaderboard-table th:last-child { border-top-right-radius: 12px; }
    .leaderboard-table tr:last-child td {
        border-bottom: none;
    }
    .rank-col {
        width: 60px;
        text-align: center !important;
        font-weight: 600;
        color: var(--text-muted);
    }
    .rank-1 { color: #fbbf24; font-size: 1.5rem; } /* Gold */
    .rank-2 { color: #94a3b8; font-size: 1.25rem; } /* Silver */
    .rank-3 { color: #b45309; font-size: 1.1rem; } /* Bronze */
</style>
@endpush

@section('content')
<div style="padding: 2rem 0 1rem; font-family: var(--font-sans, system-ui, sans-serif); max-width: 900px; margin: 0 auto;">

    {{-- Page header --}}
    <div style="text-align: center; margin-bottom: 2.5rem;">
        <p style="font-size: 11px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text-muted); margin: 0 0 0.75rem;">Rankings</p>
        <h1 style="font-size: 2rem; font-weight: 500; color: var(--text-color, #111); margin: 0 0 0.4rem; line-height: 1.2;">{{ $title }}</h1>
        <p style="font-size: 0.9375rem; color: var(--text-muted); margin: 0;">Top 10 Players</p>
    </div>

    <div style="background: var(--card-bg, #fff); border: 0.5px solid var(--border-color); border-radius: 12px; overflow: hidden; margin-bottom: 2rem;">
        <table class="leaderboard-table">
            <thead>
                <tr>
                    <th class="rank-col">Rank</th>
                    <th>Player</th>
                    <th style="text-align: right;">{{ in_array($type, ['2048', 'snake']) ? 'Score' : 'Time' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($scores as $index => $score)
                    <tr style="{{ auth()->id() === $score->user_id ? 'background: var(--puzzle-bg, #eff6ff);' : '' }}">
                        <td class="rank-col">
                            @if($index === 0) <span class="rank-1">🥇</span>
                            @elseif($index === 1) <span class="rank-2">🥈</span>
                            @elseif($index === 2) <span class="rank-3">🥉</span>
                            @else {{ $index + 1 }}
                            @endif
                        </td>
                        <td style="font-weight: 500; color: var(--text-color, #111);">
                            {{ $score->user->username ?? $score->user->name }}
                            @if(auth()->id() === $score->user_id)
                                <span style="font-size: 0.65rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; background: var(--primary); color: white; padding: 2px 8px; border-radius: 100px; margin-left: 0.5rem; vertical-align: middle;">You</span>
                            @endif
                        </td>
                        <td style="text-align: right; font-family: monospace; font-size: 1.125rem; font-weight: 600; color: var(--text-color, #111);">
                            @if(in_array($type, ['2048', 'snake']))
                                {{ number_format($score->score) }} Pts
                            @else
                                {{ $score->time_taken ?? $score->score }}s
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 4rem 2rem; color: var(--text-muted);">
                            No scores recorded yet. Be the first to play!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="text-align: center;">
        @if($type === 'puzzle')
            <a href="{{ route('customer.game.index') }}" class="btn btn-secondary" style="font-size: 0.875rem; padding: 0.5rem 1rem;">Back to Puzzles</a>
        @else
            <a href="{{ route('customer.leaderboards.index') }}" class="btn btn-secondary" style="font-size: 0.875rem; padding: 0.5rem 1rem;">Back to Leaderboards</a>
        @endif
    </div>
</div>
@endsection
