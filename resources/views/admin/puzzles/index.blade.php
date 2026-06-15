@extends('layouts.app')

@section('title', 'Manage Puzzles')

@section('content')
<div class="header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Manage Puzzle Levels</h2>
    <a href="{{ route('admin.puzzles.create') }}" class="btn">Add New Level</a>
</div>

<div class="card">
    <table style="width: 100%; border-collapse: collapse; text-align: left;">
        <thead>
            <tr style="border-bottom: 1px solid var(--border-color);">
                <th style="padding: 1rem;">ID</th>
                <th style="padding: 1rem;">Image</th>
                <th style="padding: 1rem;">Name</th>
                <th style="padding: 1rem;">Difficulty</th>
                <th style="padding: 1rem;">Grid</th>
                <th style="padding: 1rem;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($levels as $level)
            <tr style="border-bottom: 1px solid var(--border-color);">
                <td style="padding: 1rem;">{{ $level->id }}</td>
                <td style="padding: 1rem;">
                    <img src="{{ asset('storage/' . $level->image_path) }}" alt="{{ $level->name }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                </td>
                <td style="padding: 1rem;">{{ $level->name }}</td>
                <td style="padding: 1rem; text-transform: capitalize;">{{ $level->difficulty }}</td>
                <td style="padding: 1rem;">{{ $level->grid_size }}x{{ $level->grid_size }}</td>
                <td style="padding: 1rem;">
                    <form action="{{ route('admin.puzzles.destroy', $level->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="padding: 2rem; text-align: center; color: var(--text-muted);">No puzzle levels found. Add one!</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
