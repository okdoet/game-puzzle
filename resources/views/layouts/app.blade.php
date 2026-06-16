<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BrainGames')</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="{{ asset('js/theme.js') }}?v=1.2"></script>
    <style>
        /* Page Load & Scroll Animations */
        .fade-in-up {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }
        .fade-in-up.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
        .page-transition {
            animation: pageFade 0.5s ease-out forwards;
        }
        @keyframes pageFade {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Navbar Icon Buttons */
        .navbar-right {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }
        .icon-btn {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0, 0, 0, 0.05);
            color: var(--text-color, #111);
            transition: all 0.2s;
            cursor: pointer;
            text-decoration: none;
            position: relative;
            padding: 0;
        }
        .icon-btn:hover {
            background: rgba(0, 0, 0, 0.08);
        }
        .icon-btn svg {
            width: 20px;
            height: 20px;
            stroke-width: 2;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        [data-theme="dark"] .icon-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: rgba(255, 255, 255, 0.9);
        }
        [data-theme="dark"] .icon-btn:hover {
            background: rgba(255, 255, 255, 0.12);
        }
        /* Rotate animation for the icon when theme switches */
        #theme-toggle svg {
            transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s;
        }
        #theme-toggle.animating svg {
            transform: rotate(360deg) scale(0.8);
            opacity: 0.5;
        }
        .icon-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #b91c1c;
            color: white;
            font-size: 10px;
            font-weight: 700;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--bg-color, #fff);
        }
        [data-theme="dark"] .icon-badge {
            border-color: #111;
        }

        /* Global Theme Transition */
        body, .navbar, .game-card, .btn, .card-banner-bg, .puzzle-bg, .2048-bg {
            transition: background-color 0.4s ease, border-color 0.4s ease, color 0.4s ease, box-shadow 0.4s ease;
        }
        
        .stat-chip {
            transition: background-color 0.4s ease, color 0.4s ease, border-color 0.4s ease;
        }

        /* View Transition API for Dark Mode Toggle */
        ::view-transition-old(root),
        ::view-transition-new(root) {
            animation: none;
            mix-blend-mode: normal;
        }
        ::view-transition-old(root) { z-index: 1; }
        ::view-transition-new(root) { z-index: 9999; }
        [data-theme="light"]::view-transition-old(root) { z-index: 9999; }
        [data-theme="light"]::view-transition-new(root) { z-index: 1; }

        /* Organic Irregular Wave Background */
        .bg-waves-container {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: -1;
            overflow: hidden;
            background: var(--bg-color);
            pointer-events: none;
        }

        .organic-wave {
            position: absolute;
            transform-origin: 50% 50%;
            filter: blur(4px); /* Slight blur makes the edges feel softer like gradients */
        }

        .ow-1 {
            width: 150vw; height: 160vw;
            border-radius: 45% 55% 40% 60% / 55% 45% 60% 40%;
            background: linear-gradient(135deg, rgba(139,92,246,0.25), rgba(236,72,153,0.15));
            top: -60vw; left: -20vw;
            animation: drift 45s infinite linear;
        }
        .ow-2 {
            width: 180vw; height: 150vw;
            border-radius: 60% 40% 55% 45% / 40% 60% 45% 55%;
            background: linear-gradient(45deg, rgba(245,158,11,0.2), rgba(236,72,153,0.25));
            bottom: -60vw; right: -30vw;
            animation: drift 60s infinite linear reverse;
        }
        .ow-3 {
            width: 140vw; height: 170vw;
            border-radius: 40% 60% 50% 50% / 50% 50% 60% 40%;
            background: linear-gradient(90deg, rgba(59,130,246,0.15), rgba(139,92,246,0.2));
            top: 10vh; left: 20vw;
            animation: drift 50s infinite linear;
        }
        .ow-4 {
            width: 160vw; height: 140vw;
            border-radius: 50% 50% 60% 40% / 45% 55% 40% 60%;
            background: linear-gradient(180deg, rgba(236,72,153,0.15), rgba(245,158,11,0.15));
            top: -40vw; right: -10vw;
            animation: drift 55s infinite linear reverse;
        }
        .ow-5 {
            width: 150vw; height: 150vw;
            border-radius: 45% 55% 55% 45% / 55% 45% 45% 55%;
            background: linear-gradient(to right, rgba(139,92,246,0.15), rgba(59,130,246,0.15));
            bottom: -20vw; left: -20vw;
            animation: drift 40s infinite linear;
        }

        [data-theme="dark"] .ow-1 { background: linear-gradient(135deg, rgba(139,92,246,0.15), rgba(236,72,153,0.1)); }
        [data-theme="dark"] .ow-2 { background: linear-gradient(45deg, rgba(245,158,11,0.12), rgba(236,72,153,0.15)); }
        [data-theme="dark"] .ow-3 { background: linear-gradient(90deg, rgba(59,130,246,0.1), rgba(139,92,246,0.12)); }
        [data-theme="dark"] .ow-4 { background: linear-gradient(180deg, rgba(236,72,153,0.1), rgba(245,158,11,0.1)); }
        [data-theme="dark"] .ow-5 { background: linear-gradient(to right, rgba(139,92,246,0.1), rgba(59,130,246,0.1)); }

        @keyframes drift {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="bg-waves-container">
        <div class="organic-wave ow-1"></div>
        <div class="organic-wave ow-2"></div>
        <div class="organic-wave ow-3"></div>
        <div class="organic-wave ow-4"></div>
        <div class="organic-wave ow-5"></div>
    </div>

    <nav class="navbar">
        <div class="navbar-left">
            <a href="{{ route('customer.dashboard') }}" class="navbar-brand">
                🧠 BrainGames
            </a>
        </div>
        
        <div class="navbar-center">
            @if(auth()->check() && auth()->user()->role === 'customer')
                <a href="{{ route('customer.dashboard') }}" class="{{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">Home</a>
                <a href="{{ route('customer.leaderboards.index') }}" class="{{ request()->routeIs('customer.leaderboards.*') ? 'active' : '' }}">Leaderboards</a>
            @elseif(auth()->check() && auth()->user()->role === 'admin')
                <a href="{{ route('admin.puzzles.index') }}">Manage Puzzles</a>
            @endif
            <a href="{{ url('/about') }}" class="{{ request()->is('about') ? 'active' : '' }}">About Us</a>
        </div>

        <div class="navbar-right">
            <button id="theme-toggle" class="icon-btn" title="Toggle Theme">
                <i data-lucide="sun" id="theme-icon"></i>
            </button>

            @if(auth()->check())
                <div style="position: relative; display: inline-flex; gap: 0.75rem;">
                    <a href="{{ route('profile.edit') }}" class="icon-btn" title="Profile Settings ({{ auth()->user()->username ?? auth()->user()->name }})">
                        <i data-lucide="user"></i>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline; margin: 0;">
                        @csrf
                        <button type="submit" class="icon-btn" title="Logout">
                            <i data-lucide="log-out"></i>
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </nav>

    <div class="container page-transition">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </div>

    @stack('scripts')
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Apply fade-in-up to common animated elements
            const animatedElements = document.querySelectorAll('.card, .game-card, .header, .leaderboard-table, .ttt-wrapper, .game-container');
            animatedElements.forEach((el, index) => {
                el.classList.add('fade-in-up');
                // Optional: add stagger delay based on DOM order for immediate children
                el.style.transitionDelay = `${(index % 5) * 0.1}s`;
            });

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        // Optional: Unobserve after animating once
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: "0px 0px -50px 0px"
            });

            document.querySelectorAll('.fade-in-up').forEach(el => {
                observer.observe(el);
            });
            
            // Initialize Lucide Icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>
</body>
</html>
