const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');
canvas.width = 600;
canvas.height = 800;

// UI Elements
const dimensionText = document.getElementById('dimension-text');
const livesText = document.getElementById('player-lives');
const aiScoreText = document.getElementById('ai-score');
const beatCursor = document.getElementById('beat-cursor');
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

// Audio Engine (Web Audio API Synth)
class RhythmEngine {
    constructor() {
        this.bpm = 100;
        this.isPlaying = false;
    }
    init() {}
    start() { this.isPlaying = true; }
    stop() { this.isPlaying = false; }
    getAccuracy() { return 1.0; }
    updateBPM(newBPM) { this.bpm = newBPM; }
}

const rhythm = new RhythmEngine();

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
                // Move towards ball
                let dest = ball.x;
                // Add some error based on dimension speed
                this.targetX = dest - this.width / 2;
                
                // Auto swing when close
                if (ball.y < this.y + 40 && !this.isSwinging && ball.y > this.y) {
                    this.swing();
                    // AI always hits reasonably well
                    ball.vy *= -1;
                    ball.vy = Math.abs(ball.vy); // ensure it goes down
                    ball.y = this.y + this.height + ball.radius;
                }
            } else {
                // Return to center
                this.targetX = canvas.width/2 - this.width/2;
            }
        }
        
        // Interpolation (AI moves slower so it's easier to beat)
        let interpSpeed = this.isPlayer ? 0.2 : 0.05;
        this.x += (this.targetX - this.x) * interpSpeed * (dt / 16.67);
        
        // Keep in bounds
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
    const x = e.clientX - rect.left;
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
    
    // Check collision with ball (Strike Zone)
    // Ball must be near player Y
    const strikeZoneY = player.y - 30;
    
    if (ball.y > strikeZoneY && ball.y < player.y + 20) {
        // Is ball horizontally within paddle?
        if (ball.x > player.x - 20 && ball.x < player.x + player.width + 20) {
            // Hit!
            // Calculate accuracy based on vertical distance to paddle
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
                feedback = 'MISS BEAT';
                ball.speedMultiplier = 0.5; // weak hit
                hitFeedback.className = 'feedback-miss';
                playerScore += 10;
                // Small penalty glitch
                document.getElementById('game-container').classList.add('glitch');
                setTimeout(() => document.getElementById('game-container').classList.remove('glitch'), 200);
            }
            
            document.getElementById('player-score').innerText = `SCORE: ${playerScore}`;
            showFeedback(feedback);
            
            // Reflect ball
            ball.vy *= -1;
            ball.vy = -Math.abs(ball.vy); // Ensure it goes up
            ball.y = strikeZoneY;
            
            // Add some English (spin) based on where it hit paddle
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
        
        // Increase BPM slightly
        if (rhythm.bpm < 180) {
            rhythm.updateBPM(rhythm.bpm + 10);
        }
        
        // Visual effect
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
    
    // Scoring / Miss
    if (ball.y > canvas.height) {
        // Player missed
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
        // AI missed (rare, but just in case)
        totalHits += 2; // Bonus
        playerScore += 200;
        document.getElementById('player-score').innerText = `SCORE: ${playerScore}`;
        ball.reset();
    }
}

function draw() {
    // Background with trail effect
    ctx.fillStyle = currentDim.bg;
    ctx.globalAlpha = 0.3; // Creates trail
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    ctx.globalAlpha = 1.0;
    
    // Draw Center Line
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

function updateBeatTracker() {
    // Disabled beat tracker
}

// Controls
document.getElementById('start-btn').addEventListener('click', () => {
    rhythm.init();
    rhythm.start();
    startScreen.classList.add('hidden');
    gameState = 'PLAYING';
    ball.reset();
    totalHits = 0;
    playerScore = 0;
    document.getElementById('player-score').innerText = `SCORE: 0`;
    lives = 3;
    rhythm.updateBPM(100);
    dimensionLevel = 0;
    currentDim = DIMENSIONS[0];
    dimensionText.innerText = `LEVEL: ${currentDim.name}`;
    livesText.innerText = `LIVES: ${lives}`;
});

document.getElementById('restart-btn').addEventListener('click', () => {
    gameOverScreen.classList.add('hidden');
    startScreen.classList.remove('hidden');
    rhythm.stop();
    gameState = 'START';
});

function endGame() {
    gameState = 'GAMEOVER';
    rhythm.stop();
    saveScore(playerScore);
    renderLeaderboard();
    document.getElementById('final-score').innerText = `Score: ${playerScore}\nMax Level: ${currentDim.name}`;
    gameOverScreen.classList.remove('hidden');
}

// Leaderboard Logic
function getLeaderboard() {
    return JSON.parse(localStorage.getItem('dimensionScores') || '[]');
}

function saveScore(score) {
    if (score === 0) return;
    let scores = getLeaderboard();
    scores.push(score);
    scores.sort((a,b) => b - a);
    scores = scores.slice(0, 5); // top 5
    localStorage.setItem('dimensionScores', JSON.stringify(scores));
    
    if (typeof window.LaravelSubmitScore === 'function') {
        window.LaravelSubmitScore(score);
    }
}

function renderLeaderboard() {
    const scores = getLeaderboard();
    const listHtml = scores.length ? scores.map((s, i) => `<li>#${i+1} - ${s} PTS</li>`).join('') : '<li>No scores yet</li>';
    const mainList = document.getElementById('leaderboard-list');
    const overList = document.getElementById('game-over-leaderboard-list');
    if (mainList) mainList.innerHTML = listHtml;
    if (overList) overList.innerHTML = listHtml;
}

// Initial render
renderLeaderboard();

// Start loop
requestAnimationFrame(loop);
