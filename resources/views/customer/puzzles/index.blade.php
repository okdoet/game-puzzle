@extends('layouts.app')

@section('title', 'Select Puzzle Level')

@section('content')
<div style="padding: 2rem 0 1rem; font-family: var(--font-sans, system-ui, sans-serif); max-width: 900px; margin: 0 auto;">
    
    {{-- Page header --}}
    <div style="text-align: center; margin-bottom: 2.5rem;">
        <p style="font-size: 11px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text-muted); margin: 0 0 0.75rem;">Puzzle levels</p>
        <h1 style="font-size: 2rem; font-weight: 500; color: var(--text-color, #111); margin: 0 0 0.4rem; line-height: 1.2;">Choose a Level</h1>
        <p style="font-size: 0.9375rem; color: var(--text-muted); margin: 0;">Select a puzzle below to start playing</p>
    </div>

    @php
        $difficulties = ['easy', 'medium', 'hard'];
        $hasAnyLevel = false;
    @endphp

    @foreach($difficulties as $difficulty)
        @php
            $difficultyLevels = $levels->where('difficulty', $difficulty);
        @endphp

        @if($difficultyLevels->count() > 0)
            @php $hasAnyLevel = true; @endphp
            <div style="margin-bottom: 3.5rem;">
                <h2 style="font-size: 1.25rem; font-weight: 500; text-transform: capitalize; margin-bottom: 1rem; color: var(--text-color, #111); border-bottom: 0.5px solid var(--border-color); padding-bottom: 0.75rem;">
                    {{ $difficulty }} Levels
                </h2>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 1.5rem;">
                    @foreach($difficultyLevels as $level)
                        <div class="game-card" style="background: var(--card-bg, #fff); border: 0.5px solid var(--border-color); border-radius: 12px; overflow: hidden; transition: border-color 0.2s, transform 0.2s;">
                            <div style="height: 160px; background: url('{{ asset($level->image_path) }}') center/cover; border-bottom: 0.5px solid var(--border-color);"></div>
                            
                            <div style="padding: 1.25rem;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                    <h3 style="font-size: 1.0625rem; font-weight: 500; color: var(--text-color, #111); margin: 0;">{{ $level->name }}</h3>
                                    @if($level->completions->count() > 0)
                                        <span class="stat-chip" style="background: rgba(16, 185, 129, 0.1); color: #059669; border: 0.5px solid rgba(16, 185, 129, 0.2);">✓ Solved</span>
                                    @endif
                                </div>
                                <p style="font-size: 0.8125rem; color: var(--text-muted); margin: 0 0 1rem;">Grid: {{ $level->grid_size }}x{{ $level->grid_size }}</p>
                                
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="{{ route('customer.game.play', $level->id) }}" class="btn" style="flex: 1; padding: 0.6rem; font-size: 0.875rem;">Play Now</a>
                                    <a href="{{ route('customer.leaderboards.puzzle', $level->id) }}" class="btn btn-secondary" style="padding: 0.6rem; font-size: 0.875rem;">🏆 Rank</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach

    @if(!$hasAnyLevel)
        <div style="text-align: center; color: var(--text-muted); padding: 3rem; background: var(--card-bg, #fff); border: 0.5px solid var(--border-color); border-radius: 12px;">
            No puzzle levels available yet.
        </div>
    @endif
</div>

<style>
    .game-card:hover {
        border-color: rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }
    [data-theme="dark"] .game-card:hover {
        border-color: rgba(255, 255, 255, 0.2);
    }
    
    .stat-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--chip-bg, #f3f4f6);
        border-radius: 100px;
        padding: 2px 8px;
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
</style>
@endsection
