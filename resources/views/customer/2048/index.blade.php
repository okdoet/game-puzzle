@extends('layouts.app')

@section('title', '2048')

@push('styles')
<style>
    .game-container {
        max-width: 500px;
        margin: 0 auto;
        text-align: center;
        font-family: var(--font-sans, system-ui, sans-serif);
        padding: 2rem 0 1rem;
    }

    .header-2048 {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .header-2048 h1 {
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

    .grid-container {
        background: var(--board-bg, #f1f5f9);
        border: 0.5px solid var(--border-color);
        padding: 1rem;
        border-radius: 12px;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        grid-template-rows: repeat(4, 1fr);
        gap: 0.75rem;
        aspect-ratio: 1;
        position: relative;
        touch-action: none;
    }

    .grid-cell {
        background: var(--border-color);
        border-radius: 8px;
    }

    .tile {
        position: absolute;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: bold;
        border-radius: 8px;
        font-size: 2rem;
        color: white;
        transition: transform 0.15s ease-in-out;
        width: calc((100% - 2.25rem) / 4);
        height: calc((100% - 2.25rem) / 4);
    }
    
    .tile-inner {
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: inherit;
        font-family: monospace;
    }

    .tile.new .tile-inner {
        animation: appear 0.2s ease;
    }

    @keyframes appear {
        0% { transform: scale(0); }
        100% { transform: scale(1); }
    }

    /* Tile Colors */
    .tile-2 .tile-inner { background: #eee4da; color: #776e65; }
    .tile-4 .tile-inner { background: #ede0c8; color: #776e65; }
    .tile-8 .tile-inner { background: #f2b179; color: #f9f6f2; }
    .tile-16 .tile-inner { background: #f59563; color: #f9f6f2; }
    .tile-32 .tile-inner { background: #f67c5f; color: #f9f6f2; }
    .tile-64 .tile-inner { background: #f65e3b; color: #f9f6f2; }
    .tile-128 .tile-inner { background: #edcf72; color: #f9f6f2; font-size: 1.5rem; }
    .tile-256 .tile-inner { background: #edcc61; color: #f9f6f2; font-size: 1.5rem; }
    .tile-512 .tile-inner { background: #edc850; color: #f9f6f2; font-size: 1.5rem; }
    .tile-1024 .tile-inner { background: #edc53f; color: #f9f6f2; font-size: 1.2rem; }
    .tile-2048 .tile-inner { background: #edc22e; color: #f9f6f2; font-size: 1.2rem; box-shadow: 0 0 30px 10px rgba(243, 215, 116, 0.55); }
    .tile-super .tile-inner { background: #3c3a32; color: #f9f6f2; font-size: 1rem; }

    #tile-container {
        position: absolute;
        top: 1rem; left: 1rem; right: 1rem; bottom: 1rem;
        z-index: 10;
        pointer-events: none;
    }

    .tile.pop .tile-inner {
        animation: pop 0.2s ease;
    }

    @keyframes pop {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
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
            width: 50px;
            height: 50px;
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
        <p style="font-size: 11px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text-muted); margin: 0 0 0.75rem;">Numbers</p>
    </div>

    <div class="header-2048">
        <h1>2048</h1>
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
        Join the numbers and get to the <strong>2048 tile!</strong>
    </div>

    <div class="grid-container" id="grid-container">
        <!-- 16 Background Cells -->
        <div class="grid-cell"></div><div class="grid-cell"></div><div class="grid-cell"></div><div class="grid-cell"></div>
        <div class="grid-cell"></div><div class="grid-cell"></div><div class="grid-cell"></div><div class="grid-cell"></div>
        <div class="grid-cell"></div><div class="grid-cell"></div><div class="grid-cell"></div><div class="grid-cell"></div>
        <div class="grid-cell"></div><div class="grid-cell"></div><div class="grid-cell"></div><div class="grid-cell"></div>
        
        <div class="game-message" id="game-message">
            <p id="message-text">Game Over!</p>
            <button class="btn" onclick="initGame()">Try Again</button>
        </div>
        
        <div id="tile-container"></div>
    </div>
    
    <div class="mobile-controls">
        <button class="mobile-btn" onclick="move('up')">↑</button>
        <div class="row-controls">
            <button class="mobile-btn" onclick="move('left')">←</button>
            <button class="mobile-btn" onclick="move('down')">↓</button>
            <button class="mobile-btn" onclick="move('right')">→</button>
        </div>
    </div>

    <div class="controls">
        <button class="btn btn-secondary" onclick="initGame()">New Game</button>
        <a href="{{ route('customer.leaderboards.game', '2048') }}" class="btn btn-secondary">Leaderboard</a>
        <a href="{{ route('customer.dashboard') }}" class="btn btn-secondary">Back</a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let grid = [];
    let score = 0;
    let bestScore = localStorage.getItem('2048BestScore') || 0;
    let size = 4;
    let isGameOver = false;
    let tileIdCounter = 0;

    const tileContainer = document.getElementById('tile-container');
    const scoreEl = document.getElementById('score');
    const bestScoreEl = document.getElementById('best-score');
    const gameMessage = document.getElementById('game-message');
    const messageText = document.getElementById('message-text');

    bestScoreEl.innerText = bestScore;

    function initGame() {
        grid = Array(size).fill().map(() => Array(size).fill(null));
        score = 0;
        isGameOver = false;
        tileIdCounter = 0;
        scoreEl.innerText = score;
        gameMessage.style.display = 'none';
        tileContainer.innerHTML = '';
        
        addRandomTile();
        addRandomTile();
    }

    function addRandomTile() {
        let emptyCells = [];
        for (let r = 0; r < size; r++) {
            for (let c = 0; c < size; c++) {
                if (grid[r][c] === null) emptyCells.push({r, c});
            }
        }
        if (emptyCells.length > 0) {
            let randomCell = emptyCells[Math.floor(Math.random() * emptyCells.length)];
            let val = Math.random() < 0.9 ? 2 : 4;
            let tile = { id: tileIdCounter++, val: val };
            grid[randomCell.r][randomCell.c] = tile;
            
            // Create DOM element immediately
            createTileDOM(tile, randomCell.r, randomCell.c);
        }
    }

    function createTileDOM(tile, r, c) {
        let el = document.createElement('div');
        el.id = 'tile-' + tile.id;
        el.className = `tile tile-${tile.val > 2048 ? 'super' : tile.val} new`;
        el.innerHTML = `<div class="tile-inner">${tile.val}</div>`;
        setTilePositionDOM(el, r, c);
        tileContainer.appendChild(el);
        setTimeout(() => el.classList.remove('new'), 200);
    }

    function setTilePositionDOM(el, r, c) {
        el.style.transform = `translate(calc(${c * 100}% + ${c * 0.75}rem), calc(${r * 100}% + ${r * 0.75}rem))`;
    }

    function move(direction) {
        if (isGameOver) return;
        
        let moved = false;
        let newGrid = Array(size).fill().map(() => Array(size).fill(null));
        let mergesToProcess = [];
        
        // Helper to slide array
        const slide = (row) => {
            let arr = row.filter(cell => cell !== null);
            for (let i = 0; i < arr.length - 1; i++) {
                if (arr[i].val === arr[i+1].val && !arr[i].merged) {
                    arr[i].newVal = arr[i].val * 2;
                    arr[i].mergedWith = arr[i+1];
                    arr[i].merged = true;
                    score += arr[i].newVal;
                    arr.splice(i + 1, 1);
                }
            }
            arr.forEach(a => delete a.merged);
            while (arr.length < size) arr.push(null);
            return arr;
        }

        if (direction === 'left' || direction === 'right') {
            for (let r = 0; r < size; r++) {
                let row = grid[r].slice();
                if (direction === 'right') row.reverse();
                let newRow = slide(row);
                if (direction === 'right') newRow.reverse();
                for (let c = 0; c < size; c++) {
                    if (grid[r][c] !== newRow[c]) moved = true;
                    newGrid[r][c] = newRow[c];
                }
            }
        } else if (direction === 'up' || direction === 'down') {
            for (let c = 0; c < size; c++) {
                let col = [grid[0][c], grid[1][c], grid[2][c], grid[3][c]];
                if (direction === 'down') col.reverse();
                let newCol = slide(col);
                if (direction === 'down') newCol.reverse();
                for (let r = 0; r < size; r++) {
                    if (grid[r][c] !== newCol[r]) moved = true;
                    newGrid[r][c] = newCol[r];
                }
            }
        }

        if (moved) {
            // Apply visual movement
            for (let r = 0; r < size; r++) {
                for (let c = 0; c < size; c++) {
                    let tile = newGrid[r][c];
                    if (tile) {
                        let el = document.getElementById('tile-' + tile.id);
                        if (el) setTilePositionDOM(el, r, c);
                        
                        if (tile.mergedWith) {
                            let mergedEl = document.getElementById('tile-' + tile.mergedWith.id);
                            if (mergedEl) setTilePositionDOM(mergedEl, r, c);
                            mergesToProcess.push(tile);
                        }
                    }
                }
            }

            grid = newGrid;
            
            // Wait for slide animation (150ms) before updating values and adding new tile
            setTimeout(() => {
                mergesToProcess.forEach(tile => {
                    // Remove the consumed tile from DOM
                    let elToRemove = document.getElementById('tile-' + tile.mergedWith.id);
                    if (elToRemove) elToRemove.remove();
                    
                    // Upgrade the base tile
                    tile.val = tile.newVal;
                    delete tile.newVal;
                    delete tile.mergedWith;
                    
                    let el = document.getElementById('tile-' + tile.id);
                    if (el) {
                        el.className = `tile tile-${tile.val > 2048 ? 'super' : tile.val} pop`;
                        el.innerHTML = `<div class="tile-inner">${tile.val}</div>`;
                        // Remove pop class after animation
                        setTimeout(() => el.classList.remove('pop'), 200);
                    }
                });

                addRandomTile();
                scoreEl.innerText = score;
                if (score > bestScore) {
                    bestScore = score;
                    bestScoreEl.innerText = bestScore;
                    localStorage.setItem('2048BestScore', bestScore);
                }
                checkGameOver();
            }, 150);
        }
    }

    function checkGameOver() {
        for (let r = 0; r < size; r++) {
            for (let c = 0; c < size; c++) {
                if (grid[r][c] === null) return;
            }
        }
        for (let r = 0; r < size; r++) {
            for (let c = 0; c < size; c++) {
                if (c < size - 1 && grid[r][c].val === grid[r][c+1].val) return;
                if (r < size - 1 && grid[r][c].val === grid[r+1][c].val) return;
            }
        }
        
        isGameOver = true;
        messageText.innerText = 'Game Over!';
        gameMessage.style.display = 'flex';

        // Submit score to database
        fetch("{{ route('customer.game-scores.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ game_type: '2048', score: score })
        });
    }

    document.addEventListener('keydown', (e) => {
        if (['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
            e.preventDefault();
            switch (e.key) {
                case 'ArrowUp': move('up'); break;
                case 'ArrowDown': move('down'); break;
                case 'ArrowLeft': move('left'); break;
                case 'ArrowRight': move('right'); break;
            }
        }
    });

    // Simple Swipe detection
    let touchStartX = 0;
    let touchStartY = 0;
    const gridContainer = document.getElementById('grid-container');

    gridContainer.addEventListener('touchstart', e => {
        touchStartX = e.changedTouches[0].screenX;
        touchStartY = e.changedTouches[0].screenY;
    }, {passive: false});

    gridContainer.addEventListener('touchmove', e => {
        e.preventDefault(); // Prevent scrolling
    }, {passive: false});

    gridContainer.addEventListener('touchend', e => {
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
                move(dx > 0 ? 'right' : 'left');
            } else {
                move(dy > 0 ? 'down' : 'up');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', initGame);
</script>
@endpush
