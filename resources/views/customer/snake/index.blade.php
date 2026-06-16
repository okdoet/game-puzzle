@extends('layouts.app')

@section('title', 'Snake')

@push('styles')
<style>
    .game-container {
        max-width: 500px;
        margin: 0 auto;
        text-align: center;
        font-family: var(--font-sans, system-ui, sans-serif);
        padding: 2rem 0 1rem;
    }

    .header-snake {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .header-snake h1 {
        font-size: 3rem;
        font-weight: 600;
        margin: 0;
        color: var(--text-color, #111);
        line-height: 1;
    }

    .score-container {
        display: flex;
        gap: 0.5rem;
    }

    .score-box {
        background: var(--card-bg, #fff);
        border: 0.5px solid var(--border-color);
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 500;
        min-width: 70px;
    }

    .score-title {
        font-size: 0.65rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 2px;
    }

    .score-value {
        font-size: 1.25rem;
        color: var(--text-color, #111);
        line-height: 1;
    }

    .canvas-container {
        background: var(--board-bg, #f1f5f9);
        border: 0.5px solid var(--border-color);
        padding: 1rem;
        border-radius: 12px;
        aspect-ratio: 1;
        position: relative;
        touch-action: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #snakeCanvas {
        background: var(--border-color);
        border-radius: 8px;
        width: 100%;
        height: 100%;
        display: block;
    }

    .game-message {
        display: none;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(30, 41, 59, 0.8);
        backdrop-filter: blur(4px);
        z-index: 100;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        animation: fade 0.5s ease;
    }

    @keyframes fade {
        0% { opacity: 0; }
        100% { opacity: 1; }
    }

    .game-message p {
        font-size: 2.5rem;
        font-weight: 500;
        color: white;
        margin-bottom: 1.5rem;
    }

    .controls {
        margin-top: 2rem;
        display: flex;
        justify-content: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .mobile-controls {
        display: none;
        margin-top: 1.5rem;
        gap: 0.5rem;
    }
    
    @media (max-width: 768px) {
        .mobile-controls {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .row-controls {
            display: flex;
            gap: 0.5rem;
        }
        .mobile-btn {
            width: 60px;
            height: 60px;
            font-size: 1.5rem;
            border-radius: 12px;
            background: var(--card-bg, #fff);
            border: 0.5px solid var(--border-color);
            color: var(--text-color, #111);
        }
    }
</style>
@endpush

@section('content')
<div class="game-container">
    <div style="text-align: center; margin-bottom: 1.5rem;">
        <p style="font-size: 11px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text-muted); margin: 0 0 0.75rem;">Arcade</p>
    </div>

    <div class="header-snake">
        <h1>Snake</h1>
        <div class="score-container">
            <div class="score-box">
                <div class="score-title">Score</div>
                <div class="score-value" id="score">0</div>
            </div>
            <div class="score-box">
                <div class="score-title">Best</div>
                <div class="score-value" id="best-score">0</div>
            </div>
        </div>
    </div>

    <div style="text-align: left; margin-bottom: 1rem; color: var(--text-muted); font-size: 0.9375rem;">
        Use <strong style="color: var(--text-color, #111);">W A S D</strong> keys to move. Eat the <strong style="color: #ef4444;">apples</strong> to grow!
    </div>

    <div class="canvas-container">
        <canvas id="snakeCanvas" width="400" height="400"></canvas>
        <div class="game-message" id="start-screen" style="display: flex;">
            <p style="font-size: 2rem;">Ready to Play?</p>
            <button class="btn" onclick="initGame()">Play Game</button>
        </div>
        <div class="game-message" id="game-message">
            <p id="message-text">Game Over!</p>
            <button class="btn" onclick="initGame()">Try Again</button>
        </div>
    </div>
    
    <div class="mobile-controls">
        <button class="mobile-btn" onclick="setDirection('UP')">↑</button>
        <div class="row-controls">
            <button class="mobile-btn" onclick="setDirection('LEFT')">←</button>
            <button class="mobile-btn" onclick="setDirection('DOWN')">↓</button>
            <button class="mobile-btn" onclick="setDirection('RIGHT')">→</button>
        </div>
    </div>

    <div class="controls">
        <button class="btn btn-secondary" onclick="initGame()">New Game</button>
        <a href="{{ route('customer.leaderboards.game', 'snake') }}" class="btn btn-secondary">Leaderboard</a>
        <a href="{{ route('customer.dashboard') }}" class="btn btn-secondary">Back</a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const canvas = document.getElementById("snakeCanvas");
    const ctx = canvas.getContext("2d");
    const scoreEl = document.getElementById("score");
    const bestScoreEl = document.getElementById("best-score");
    const gameMessage = document.getElementById("game-message");

    const box = 20; // Size of each grid square
    let snake = [];
    let food;
    let d;
    let score = 0;
    let bestScore = localStorage.getItem('snakeBestScore') || 0;
    let lastTime = 0;
    let accumulator = 0;
    const TICK_RATE = 200; // Slower logical speed
    let nextDir = "RIGHT";
    let gameInterval;
    let isGameOver = false;

    bestScoreEl.innerText = bestScore;

    function initGame() {
        cancelAnimationFrame(gameInterval);
        snake = [];
        snake[0] = { x: 9 * box, y: 10 * box, oldX: 9 * box, oldY: 10 * box };
        d = "RIGHT";
        nextDir = "RIGHT";
        score = 0;
        isGameOver = false;
        scoreEl.innerText = score;
        gameMessage.style.display = 'none';
        document.getElementById('start-screen').style.display = 'none';
        
        spawnFood();
        lastTime = performance.now();
        accumulator = 0;
        gameInterval = requestAnimationFrame(loop);
    }

    function spawnFood() {
        do {
            food = {
                x: Math.floor(Math.random() * (canvas.width / box)) * box,
                y: Math.floor(Math.random() * (canvas.height / box)) * box
            };
        } while (collision(food, snake));
    }

    // Controls
    document.addEventListener("keydown", direction);
    function direction(event) {
        let key = event.key.toLowerCase();
        
        // Prevent default browser scrolling if they accidentally use arrows or space
        if(["ArrowUp", "ArrowDown", "ArrowLeft", "ArrowRight", " "].indexOf(event.key) > -1) {
            event.preventDefault();
        }

        if(key === 'a' && d != "RIGHT") nextDir = "LEFT";
        else if(key === 'w' && d != "DOWN") nextDir = "UP";
        else if(key === 'd' && d != "LEFT") nextDir = "RIGHT";
        else if(key === 's' && d != "UP") nextDir = "DOWN";
    }

    function setDirection(newDir) {
        if(newDir == "LEFT" && d != "RIGHT") nextDir = "LEFT";
        else if(newDir == "UP" && d != "DOWN") nextDir = "UP";
        else if(newDir == "RIGHT" && d != "LEFT") nextDir = "RIGHT";
        else if(newDir == "DOWN" && d != "UP") nextDir = "DOWN";
    }

    function collision(head, array) {
        for(let i = 0; i < array.length; i++){
            if(head.x == array[i].x && head.y == array[i].y){
                return true;
            }
        }
        return false;
    }

    function checkGameOver() {
        cancelAnimationFrame(gameInterval);
        isGameOver = true;
        gameMessage.style.display = 'flex';

        if (score > bestScore) {
            bestScore = score;
            bestScoreEl.innerText = bestScore;
            localStorage.setItem('snakeBestScore', bestScore);
        }

        // Submit score to database
        fetch("{{ route('customer.game-scores.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ game_type: 'snake', score: score })
        });
    }

    function loop(timestamp) {
        if (isGameOver) return;
        
        let dt = timestamp - lastTime;
        lastTime = timestamp;
        accumulator += dt;
        
        while (accumulator >= TICK_RATE) {
            updateLogic();
            accumulator -= TICK_RATE;
        }
        
        // Draw with interpolation fraction
        let fraction = accumulator / TICK_RATE;
        draw(fraction);
        
        gameInterval = requestAnimationFrame(loop);
    }

    function updateLogic() {
        d = nextDir;
        
        let newX = snake[0].x;
        let newY = snake[0].y;

        // Move head logically
        if(d == "LEFT") newX -= box;
        if(d == "UP") newY -= box;
        if(d == "RIGHT") newX += box;
        if(d == "DOWN") newY += box;

        // Wall collision
        if(newX < 0 || newX >= canvas.width || newY < 0 || newY >= canvas.height) {
            checkGameOver();
            return;
        }

        let newHead = { x: newX, y: newY, oldX: snake[0].x, oldY: snake[0].y };

        // Self collision
        if(collision(newHead, snake)) {
            checkGameOver();
            return;
        }

        let ateFood = (newX == food.x && newY == food.y);

        if(ateFood) {
            score++;
            scoreEl.innerText = score;
            spawnFood();
        }

        // Move body segments to follow the one in front
        for(let i = snake.length - 1; i > 0; i--) {
            snake[i].oldX = snake[i].x;
            snake[i].oldY = snake[i].y;
            snake[i].x = snake[i-1].x;
            snake[i].y = snake[i-1].y;
        }

        // If ate food, add a new tail segment at the exact old position of the previous tail
        if (ateFood) {
            let last = snake[snake.length - 1];
            snake.push({ x: last.oldX, y: last.oldY, oldX: last.oldX, oldY: last.oldY });
        }

        // Update head
        snake[0].oldX = snake[0].x;
        snake[0].oldY = snake[0].y;
        snake[0].x = newX;
        snake[0].y = newY;
    }

    function draw(fraction) {
        // Clear canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Draw snake with interpolation
        for(let i = 0; i < snake.length; i++) {
            let isHead = (i == 0);
            
            // Interpolate coordinates
            let drawX = snake[i].oldX + (snake[i].x - snake[i].oldX) * fraction;
            let drawY = snake[i].oldY + (snake[i].y - snake[i].oldY) * fraction;
            
            ctx.fillStyle = isHead ? "#10b981" : "#34d399";
            ctx.beginPath();
            ctx.roundRect(drawX, drawY, box - 2, box - 2, 4);
            ctx.fill();

            // Draw Face on Head
            if (isHead) {
                ctx.fillStyle = "white";
                
                // Adjust eyes based on direction
                if (d === "RIGHT") {
                    ctx.fillRect(drawX + box - 8, drawY + 4, 3, 3);
                    ctx.fillRect(drawX + box - 8, drawY + box - 9, 3, 3);
                } else if (d === "LEFT") {
                    ctx.fillRect(drawX + 3, drawY + 4, 3, 3);
                    ctx.fillRect(drawX + 3, drawY + box - 9, 3, 3);
                } else if (d === "UP") {
                    ctx.fillRect(drawX + 4, drawY + 3, 3, 3);
                    ctx.fillRect(drawX + box - 9, drawY + 3, 3, 3);
                } else { // DOWN
                    ctx.fillRect(drawX + 4, drawY + box - 8, 3, 3);
                    ctx.fillRect(drawX + box - 9, drawY + box - 8, 3, 3);
                }
            }
        }

        // Draw food as an Apple emoji
        ctx.font = "18px sans-serif";
        ctx.textAlign = "center";
        ctx.textBaseline = "middle";
        ctx.fillText("🍎", food.x + box/2 - 1, food.y + box/2 + 1);
    }

    // Touch swipe logic for mobile
    let touchStartX = 0;
    let touchStartY = 0;
    const canvasContainer = document.querySelector('.canvas-container');

    canvasContainer.addEventListener('touchstart', e => {
        touchStartX = e.changedTouches[0].screenX;
        touchStartY = e.changedTouches[0].screenY;
    }, {passive: false});

    canvasContainer.addEventListener('touchmove', e => {
        e.preventDefault(); 
    }, {passive: false});

    canvasContainer.addEventListener('touchend', e => {
        let touchEndX = e.changedTouches[0].screenX;
        let touchEndY = e.changedTouches[0].screenY;
        handleSwipe(touchStartX, touchStartY, touchEndX, touchEndY);
    }, {passive: false});

    function handleSwipe(startX, startY, endX, endY) {
        const dx = endX - startX;
        const dy = endY - startY;
        const absDx = Math.abs(dx);
        const absDy = Math.abs(dy);

        if (Math.max(absDx, absDy) > 30) {
            if (absDx > absDy) {
                setDirection(dx > 0 ? 'RIGHT' : 'LEFT');
            } else {
                setDirection(dy > 0 ? 'DOWN' : 'UP');
            }
        }
    }

    // Removed auto initGame so it shows the start screen
</script>
@endpush
