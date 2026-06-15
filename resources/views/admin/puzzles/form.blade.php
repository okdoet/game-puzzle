@extends('layouts.app')

@section('title', 'Add Puzzle Level')

@section('content')
<div class="header" style="margin-bottom: 2rem;">
    <h2>Add New Puzzle Level</h2>
</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <form action="{{ route('admin.puzzles.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted);">Level Name</label>
            <input type="text" name="name" required style="width: 100%; padding: 0.75rem; background: rgba(15,23,42,0.6); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
            @error('name') <span style="color: var(--danger); font-size: 0.85rem;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted);">Puzzle Image (Square recommended)</label>
            <input type="file" name="image" required accept="image/*" style="width: 100%; padding: 0.75rem; background: rgba(15,23,42,0.6); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
            @error('image') <span style="color: var(--danger); font-size: 0.85rem;">{{ $message }}</span> @enderror
        </div>

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted);">Difficulty</label>
            <select name="difficulty" required style="width: 100%; padding: 0.75rem; background: rgba(15,23,42,0.6); border: 1px solid var(--border-color); border-radius: 8px; color: white;">
                <option value="easy">Easy (3x3)</option>
                <option value="medium">Medium (6x6)</option>
                <option value="hard">Hard (10x10)</option>
            </select>
            @error('difficulty') <span style="color: var(--danger); font-size: 0.85rem;">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="btn" style="width: 100%;">Save Level</button>
    </form>
</div>
@endsection
