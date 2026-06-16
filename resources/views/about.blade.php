@extends('layouts.app')

@section('title', 'About Us')

@section('content')
<div style="padding: 2rem 0 1rem; font-family: var(--font-sans, system-ui, sans-serif); max-width: 900px; margin: 0 auto;">
    
    {{-- Page header --}}
    <div style="text-align: center; margin-bottom: 3.5rem;">
        <p style="font-size: 11px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text-muted); margin: 0 0 0.75rem;">Our Story</p>
        <h1 style="font-size: 2.5rem; font-weight: 500; color: var(--text-color, #111); margin: 0 0 1rem; line-height: 1.2;">About BrainGames</h1>
        <p style="font-size: 1.0625rem; color: var(--text-muted); margin: 0 auto; max-width: 600px; line-height: 1.6;">
            We believe that training your brain should be as fun as playing a game. That's why we created a platform where logic meets entertainment.
        </p>
    </div>

    {{-- Info Cards --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 3.5rem;">
        
        <div class="game-card" style="background: var(--card-bg, #fff); border: 0.5px solid var(--border-color); border-radius: 12px; overflow: hidden; padding: 2rem;">
            <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(99, 102, 241, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; font-size: 1.75rem;">
                🧠
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 500; color: var(--text-color, #111); margin: 0 0 0.75rem;">Our Mission</h3>
            <p style="font-size: 0.9375rem; color: var(--text-muted); margin: 0; line-height: 1.6;">
                To provide high-quality, engaging puzzles and classic games that challenge the mind, improve memory, and offer a productive escape from daily stress.
            </p>
        </div>

        <div class="game-card" style="background: var(--card-bg, #fff); border: 0.5px solid var(--border-color); border-radius: 12px; overflow: hidden; padding: 2rem;">
            <div style="width: 48px; height: 48px; border-radius: 10px; background: rgba(236, 72, 153, 0.1); display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; font-size: 1.75rem;">
                👥
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 500; color: var(--text-color, #111); margin: 0 0 0.75rem;">Community First</h3>
            <p style="font-size: 0.9375rem; color: var(--text-muted); margin: 0; line-height: 1.6;">
                With our global leaderboards, you're not just playing against yourself. You're part of a worldwide community of puzzle enthusiasts striving for the top rank.
            </p>
        </div>
        
    </div>

    {{-- CTA Banner --}}
    <div style="background: var(--card-bg, #fff); border: 0.5px solid var(--border-color); border-radius: 16px; padding: 3.5rem 2rem; text-align: center; background-image: linear-gradient(135deg, rgba(99, 102, 241, 0.03), rgba(236, 72, 153, 0.03));">
        <h2 style="font-size: 1.75rem; font-weight: 500; color: var(--text-color, #111); margin: 0 0 1rem;">Ready to challenge yourself?</h2>
        <p style="font-size: 1rem; color: var(--text-muted); margin: 0 auto 2rem; max-width: 500px; line-height: 1.6;">
            Join thousands of players already testing their skills. Whether you prefer the strategy of 2048, the memory challenges, or classic Tic Tac Toe, there's something here for you.
        </p>
        @if(auth()->check())
            <a href="{{ route('customer.dashboard') }}" class="btn" style="padding: 0.875rem 2rem; font-size: 1rem;">
                🎮 Play Now
            </a>
        @else
            <a href="{{ route('register') }}" class="btn" style="padding: 0.875rem 2rem; font-size: 1rem;">
                📝 Create an Account
            </a>
        @endif
    </div>
</div>

<style>
    .game-card {
        transition: border-color 0.2s, transform 0.2s;
    }
    .game-card:hover {
        border-color: rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }
    [data-theme="dark"] .game-card:hover {
        border-color: rgba(255, 255, 255, 0.2);
    }
</style>
@endsection
