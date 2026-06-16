@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')
<div class="header" style="text-align: center; margin-bottom: 2rem;">
    <h1>Profile Settings</h1>
    <p style="color: var(--text-muted);">Update your account information</p>
</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            @error('name')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control" value="{{ old('username', $user->username) }}" required>
            @error('username')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            @error('email')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group" style="margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 2rem;">
            <label for="password">New Password <span style="font-weight: normal; color: var(--text-muted); font-size: 0.85em;">(leave blank to keep current password)</span></label>
            <input type="password" id="password" name="password" class="form-control" placeholder="••••••••">
            @error('password')
                <span class="error-message">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">Confirm New Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="••••••••">
        </div>

        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <a href="{{ auth()->user()->role === 'admin' ? route('admin.puzzles.index') : route('customer.dashboard') }}" class="btn btn-secondary" style="flex: 1; text-align: center;">Cancel</a>
            <button type="submit" class="btn btn-submit" style="flex: 2;">Save Changes</button>
        </div>
    </form>
</div>
@endsection
