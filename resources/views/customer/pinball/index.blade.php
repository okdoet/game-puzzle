@extends('layouts.app')

@section('title', 'Pinball / Dimension Rally')

@push('styles')
<style>
    .game-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 80px);
        padding: 2rem 0;
        /* Scoped styles to prevent breaking Laravel's layout */
    }
    
    .pinball-dimension-rally {
        --bg-color: #050510;
        --neon-blue: #00f3ff;
        --neon-pink: #ff007f;
        --neon-green: #39ff14;
        --neon-purple: #b000ff;
        --neon-yellow: #fefe33;
    }

    #game-container {
        position: relative;
        width: 600px;
        height: 800px;
        border: 2px solid var(--neon-blue);
        box-shadow: 0 0 20px var(--neon-blue);
        background: radial-gradient(circle at center, #0a0a1a 0%, #000 100%);
        border-radius: 12px;
        overflow: hidden;
        font-family: 'Courier New', Courier, monospace;
        color: white;
    }

    #ui-layer {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        pointer-events: none;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 20px;
    }

    #score-board {
        display: flex;
        justify-content: space-between;
        font-size: 1.2rem;
        font-weight: bold;
        text-shadow: 0 0 10px var(--neon-blue);
        color: var(--neon-blue);
    }

    #dimension-text {
        color: var(--neon-purple);
        text-shadow: 0 0 10px var(--neon-purple);
        font-size: 1.5rem;
    }

    /* Overlays */
    #game-container .overlay {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(5, 5, 16, 0.9);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 100;
        pointer-events: auto;
        font-family: 'Courier New', Courier, monospace;
    }

    #game-container .overlay.hidden {
        display: none;
    }

    #game-container h1 {
        font-size: 3rem;
        color: var(--neon-pink);
        text-shadow: 0 0 20px var(--neon-pink);
        margin-bottom: 20px;
        text-align: center;
        margin-top: 0;
    }

    #game-container p {
        margin-bottom: 10px;
        font-size: 1.2rem;
        color: var(--neon-blue);
        text-align: center;
        padding: 0 20px;
    }

    #game-container button {
        margin-top: 30px;
        padding: 15px 40px;
        font-size: 1.5rem;
        background: transparent;
        border: 2px solid var(--neon-green);
        color: var(--neon-green);
        cursor: pointer;
        text-transform: uppercase;
        font-family: inherit;
        transition: all 0.2s;
        box-shadow: 0 0 15px rgba(57, 255, 20, 0.3);
    }

    #game-container button:hover {
        background: rgba(57, 255, 20, 0.2);
        box-shadow: 0 0 25px rgba(57, 255, 20, 0.8);
    }

    #hit-feedback {
        position: absolute;
        top: 65%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 2rem;
        font-weight: bold;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .feedback-perfect { color: var(--neon-yellow); text-shadow: 0 0 20px var(--neon-yellow); }
    .feedback-good { color: var(--neon-green); text-shadow: 0 0 15px var(--neon-green); }
    .feedback-miss { color: red; text-shadow: 0 0 15px red; }

    #game-container canvas {
        display: block;
        width: 100%;
        height: 100%;
    }

    .glitch {
        animation: glitch 0.2s linear infinite;
    }

    @keyframes glitch {
        0% { transform: translate(0) }
        20% { transform: translate(-5px, 5px) }
        40% { transform: translate(-5px, -5px) }
        60% { transform: translate(5px, 5px) }
        80% { transform: translate(5px, -5px) }
        100% { transform: translate(0) }
    }

    .leaderboard-panel {
        display: none; /* Hide internal leaderboard, we use global */
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4">
    <div class="mb-4">
        <a href="{{ route('customer.dashboard') }}" class="text-sm" style="color: var(--text-muted); display: inline-flex; align-items: center; gap: 0.25rem;">
            <span>←</span> Back to Dashboard
        </a>
    </div>

    <div class="game-wrapper pinball-dimension-rally">
        <div id="game-container">
            <div id="ui-layer">
                <div id="score-board">
                    <div class="score" id="player-score">SCORE: 0</div>
                    <div id="dimension-text">LEVEL: NORMAL</div>
                    <div class="score" id="player-lives">LIVES: 3</div>
                </div>
            </div>

            <canvas id="gameCanvas"></canvas>

            <div id="start-screen" class="overlay">
                <h1>DIMENSION RALLY</h1>
                <p>Move mouse to control paddle. CLICK/SPACE to swing when ball is near.</p>
                <p>Time your click when the ball is right in front of your paddle for a PERFECT strike!</p>
                <button id="start-btn">START GAME</button>
            </div>
            
            <div id="game-over-screen" class="overlay hidden">
                <h1>YOU LOSE</h1>
                <p id="final-score">Score: 0</p>
                <button id="restart-btn">REBOOT</button>
            </div>
            
            <div id="hit-feedback"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const canvas = document.getElementById('gameCanvas');
    const ctx = canvas.getContext('2d');
    canvas.width = 600;
    canvas.height = 800;

    // UI Elements
    const dimensionText = document.getElementById('dimension-text');
    const livesText = document.getElementById('player-lives');
    const hitFeedback = document.getElementById('hit-feedback');
    const startScreen = document.getElementById('start-screen');
    const gameOverScreen = document.getElementById('game-over-screen');

    // Game State
    let gameState = 'START'; // START, PLAYING, GAMEOVER
    let totalHits = 0;
    let playerScore = 0;
    let lives = 3;
    let dimensionLevel = 0;

    const DIMENSIONS = [
        { name: 'NORMAL', bg: '#000000', ballSpeed: 1, gravityX: 0, gravityY: 0 },
        { name: 'ZERO-G', bg: '#050520', ballSpeed: 0.8, gravityX: 0, gravityY: 0, unpredictableWall: true },
        { name: 'HIGH-TEMPO', bg: '#200505', ballSpeed: 1.5, gravityX: 0, gravityY: 0 },
        { name: 'WINDY', bg: '#052005', ballSpeed: 1, gravityX: 3, gravityY: 0 },
        { name: 'HYPER-GRAVITY', bg: '#100010', ballSpeed: 1.2, gravityX: 0, gravityY: 2 }
    ];

    let currentDim = DIMENSIONS[0];

    // Entities
    class Ball {
        constructor() {
            this.reset();
            this.radius = 8;
        }
        
        reset() {
            this.x = canvas.width / 2;
            this.y = canvas.height / 2;
            this.vx = (Math.random() > 0.5 ? 1 : -1) * 3;
            this.vy = 5;
            this.speedMultiplier = 1;
        }

        update(dt) {
            let speed = this.speedMultiplier * currentDim.ballSpeed;
            
            this.x += (this.vx + currentDim.gravityX) * speed * (dt/16.67);
            this.y += (this.vy + currentDim.gravityY) * speed * (dt/16.67);

            // Wall collisions
            if (this.x <= this.radius || this.x >= canvas.width - this.radius) {
                this.vx *= -1;
                // Unpredictable bounce in Zero-G
                if (currentDim.unpredictableWall) this.vy += (Math.random() - 0.5) * 4;
                
                // Keep in bounds
                this.x = Math.max(this.radius, Math.min(canvas.width - this.radius, this.x));
            }
        }

        render(ctx) {
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
            ctx.fillStyle = '#00f3ff';
            ctx.shadowBlur = 15;
            ctx.shadowColor = '#00f3ff';
            ctx.fill();
            ctx.closePath();
        }
    }

    class Paddle {
        constructor(isPlayer) {
            this.width = 100;
            this.height = 15;
            this.isPlayer = isPlayer;
            this.x = canvas.width / 2 - this.width / 2;
            this.y = isPlayer ? canvas.height - 60 : 40;
            this.targetX = this.x;
            this.color = isPlayer ? '#ff007f' : '#39ff14';
            
            // Swing mechanic
            this.isSwinging = false;
            this.swingTimer = 0;
        }

        setTarget(x) {
            this.targetX = Math.max(0, Math.min(x - this.width / 2, canvas.width - this.width));
        }

        swing() {
            this.isSwinging = true;
            this.swingTimer = 10; // frames
        }

        update(dt, ball) {
            // AI Logic
            if (!this.isPlayer) {
                if (ball.vy < 0) { // Ball moving towards AI
                    let dest = ball.x;
                    this.targetX = dest - this.width / 2;
                    
                    if (ball.y < this.y + 40 && !this.isSwinging && ball.y > this.y) {
                        this.swing();
                        ball.vy *= -1;
                        ball.vy = Math.abs(ball.vy); // ensure it goes down
                        ball.y = this.y + this.height + ball.radius;
                    }
                } else {
                    this.targetX = canvas.width/2 - this.width/2;
                }
            }
            
            // Interpolation
            let interpSpeed = this.isPlayer ? 0.2 : 0.05;
            this.x += (this.targetX - this.x) * interpSpeed * (dt / 16.67);
            this.x = Math.max(0, Math.min(canvas.width - this.width, this.x));
            
            if (this.isSwinging) {
                this.swingTimer--;
                if (this.swingTimer <= 0) this.isSwinging = false;
            }
        }

        render(ctx) {
            ctx.fillStyle = this.color;
            ctx.shadowBlur = this.isSwinging ? 25 : 10;
            ctx.shadowColor = this.color;
            
            let drawY = this.y;
            if (this.isSwinging) {
                drawY += this.isPlayer ? -10 : 10; // Lunge forward
            }
            
            ctx.fillRect(this.x, drawY, this.width, this.height);
        }
    }

    const ball = new Ball();
    const player = new Paddle(true);
    const ai = new Paddle(false);

    // Input Handling
    canvas.addEventListener('mousemove', (e) => {
        if (gameState !== 'PLAYING') return;
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const x = (e.clientX - rect.left) * scaleX;
        player.setTarget(x);
    });

    canvas.addEventListener('mousedown', () => {
        if (gameState !== 'PLAYING') return;
        playerSwing();
    });

    document.addEventListener('keydown', (e) => {
        if (e.code === 'Space' && gameState === 'PLAYING') {
            playerSwing();
        }
    });

    function playerSwing() {
        if (player.isSwinging) return;
        player.swing();
        
        const strikeZoneY = player.y - 30;
        
        if (ball.y > strikeZoneY && ball.y < player.y + 20) {
            if (ball.x > player.x - 20 && ball.x < player.x + player.width + 20) {
                const dist = Math.abs(ball.y - player.y);
                let accuracy = 0;
                if (dist < 15) accuracy = 0.9;
                else if (dist < 25) accuracy = 0.6;
                else accuracy = 0.2;
                
                let feedback = '';
                
                if (accuracy > 0.85) {
                    feedback = 'PERFECT!';
                    ball.speedMultiplier = 1.5;
                    hitFeedback.className = 'feedback-perfect';
                    totalHits++;
                    playerScore += 100;
                    checkDimensionShift();
                } else if (accuracy > 0.5) {
                    feedback = 'GOOD';
                    ball.speedMultiplier = 1.0;
                    hitFeedback.className = 'feedback-good';
                    totalHits++;
                    playerScore += 50;
                    checkDimensionShift();
                } else {
                    feedback = 'MISS';
                    ball.speedMultiplier = 0.5;
                    hitFeedback.className = 'feedback-miss';
                    playerScore += 10;
                    document.getElementById('game-container').classList.add('glitch');
                    setTimeout(() => document.getElementById('game-container').classList.remove('glitch'), 200);
                }
                
                document.getElementById('player-score').innerText = `SCORE: ${playerScore}`;
                showFeedback(feedback);
                
                ball.vy *= -1;
                ball.vy = -Math.abs(ball.vy);
                ball.y = strikeZoneY;
                
                let hitPos = (ball.x - (player.x + player.width/2)) / (player.width/2);
                ball.vx = hitPos * 5; 
            }
        }
    }

    function showFeedback(text) {
        hitFeedback.innerText = text;
        hitFeedback.style.opacity = 1;
        setTimeout(() => { hitFeedback.style.opacity = 0; }, 500);
    }

    function checkDimensionShift() {
        if (totalHits % 5 === 0 && totalHits > 0) {
            dimensionLevel++;
            currentDim = DIMENSIONS[dimensionLevel % DIMENSIONS.length];
            dimensionText.innerText = `LEVEL: ${currentDim.name}`;
            
            document.getElementById('game-container').classList.add('glitch');
            setTimeout(() => document.getElementById('game-container').classList.remove('glitch'), 500);
        }
    }

    // Game Loop
    let lastTime = 0;
    function loop(timestamp) {
        if (!lastTime) lastTime = timestamp;
        const dt = timestamp - lastTime;
        lastTime = timestamp;
        
        if (gameState === 'PLAYING') {
            update(dt);
            draw();
        }
        
        requestAnimationFrame(loop);
    }

    function update(dt) {
        player.update(dt, ball);
        ai.update(dt, ball);
        ball.update(dt);
        
        if (ball.y > canvas.height) {
            lives--;
            livesText.innerText = `LIVES: ${lives}`;
            showFeedback("FAULT!");
            hitFeedback.className = 'feedback-miss';
            if (lives <= 0) {
                endGame();
            } else {
                ball.reset();
            }
        } else if (ball.y < 0) {
            totalHits += 2;
            playerScore += 200;
            document.getElementById('player-score').innerText = `SCORE: ${playerScore}`;
            ball.reset();
        }
    }

    function draw() {
        ctx.fillStyle = currentDim.bg;
        ctx.globalAlpha = 0.3;
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.globalAlpha = 1.0;
        
        ctx.strokeStyle = 'rgba(255,255,255,0.2)';
        ctx.setLineDash([10, 10]);
        ctx.beginPath();
        ctx.moveTo(0, canvas.height/2);
        ctx.lineTo(canvas.width, canvas.height/2);
        ctx.stroke();
        ctx.setLineDash([]);
        
        player.render(ctx);
        ai.render(ctx);
        ball.render(ctx);
    }

    // Controls
    document.getElementById('start-btn').addEventListener('click', () => {
        startScreen.classList.add('hidden');
        gameState = 'PLAYING';
        ball.reset();
        totalHits = 0;
        playerScore = 0;
        document.getElementById('player-score').innerText = `SCORE: 0`;
        lives = 3;
        dimensionLevel = 0;
        currentDim = DIMENSIONS[0];
        dimensionText.innerText = `LEVEL: ${currentDim.name}`;
        livesText.innerText = `LIVES: ${lives}`;
    });

    document.getElementById('restart-btn').addEventListener('click', () => {
        gameOverScreen.classList.add('hidden');
        startScreen.classList.remove('hidden');
        gameState = 'START';
    });

    function endGame() {
        gameState = 'GAMEOVER';
        saveScore(playerScore);
        document.getElementById('final-score').innerText = `Score: ${playerScore}\nMax Level: ${currentDim.name}`;
        gameOverScreen.classList.remove('hidden');
    }

    function saveScore(score) {
        if (score === 0) return;
        fetch('{{ route('customer.game-scores.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                game_type: 'pinball',
                score: score
            })
        }).catch(err => console.error("Error saving score:", err));
    }

    // Start loop
    requestAnimationFrame(loop);
</script>
@endpush
