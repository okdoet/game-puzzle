@extends('layouts.app')

@section('title', 'Select Puzzle Level')

@section('content')
<div class="header" style="text-align: center; margin-bottom: 3rem;">
    <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem; background: linear-gradient(135deg, #a5b4fc, #c084fc); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Choose a Level</h1>
    <p style="color: var(--text-muted);">Select a puzzle below to start playing</p>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem;">
    @forelse($levels as $level)
        <div class="card" style="padding: 0; overflow: hidden; position: relative; transition: transform 0.3s;">
            <div style="height: 200px; background: url('{{ asset($level->image_path) }}') center/cover;"></div>
            
            <div style="padding: 1.5rem;">
                <h3 style="margin-bottom: 0.5rem;">{{ $level->name }}</h3>
                <div style="display: flex; justify-content: space-between; color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem;">
                    <span style="text-transform: capitalize;">{{ $level->difficulty }} ({{ $level->grid_size }}x{{ $level->grid_size }})</span>
                    @if($level->completions->count() > 0)
                        <span style="color: var(--success); font-weight: bold;">✓ Completed</span>
                    @endif
                </div>
                
                <a href="{{ route('customer.game.play', $level->id) }}" class="btn" style="width: 100%; text-align: center;">Play Now</a>
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; text-align: center; color: var(--text-muted); padding: 3rem;">
            No puzzle levels available yet.
        </div>
    @endforelse
</div>
@endsection
