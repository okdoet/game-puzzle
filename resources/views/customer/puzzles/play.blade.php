@extends('layouts.app')

@section('title', 'Playing: ' . $level->name)

@push('styles')
<style>
    .game-board {
        --grid-size: {{ $level->grid_size }};
        --gap: 2px;
        position: relative;
        background: var(--board-bg, #f1f5f9);
        padding: 6px;
        border-radius: 12px;
        border: 0.5px solid var(--border-color);
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        margin: 0 auto;
        max-width: 600px; 
        aspect-ratio: 1/1; /* enforce square */
    }
    
    [data-theme="dark"] .game-board {
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }

    #tile-container {
        position: absolute;
        top: 6px; left: 6px; right: 6px; bottom: 6px;
    }

    .puzzle-tile {
        position: absolute;
        width: calc((100% - (var(--grid-size) - 1) * var(--gap)) / var(--grid-size));
        height: calc((100% - (var(--grid-size) - 1) * var(--gap)) / var(--grid-size));
        background-color: var(--card-bg);
        background-image: url('{{ asset($level->image_path) }}');
        background-repeat: no-repeat;
        cursor: pointer;
        transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 6px;
    }

    .puzzle-tile:hover {
        opacity: 0.9;
    }

    .puzzle-tile.empty {
        background-image: none !important;
        background-color: transparent !important;
        cursor: default;
    }

    .win-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.6);
        display: none;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        backdrop-filter: blur(8px);
    }
    .win-overlay h2 {
        font-size: 3rem;
        color: #34d399;
        margin-bottom: 0.5rem;
    }

    .play-area {
        position: relative;
        max-width: 600px;
        margin: 0 auto;
    }

    .reference-panel {
        position: absolute;
        left: calc(100% + 2rem);
        top: 0;
        width: 180px;
        background: var(--card-bg, #fff);
        padding: 10px;
        border-radius: 12px;
        border: 0.5px solid var(--border-color);
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }
    
    [data-theme="dark"] .reference-panel {
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }

    /* Stack on smaller screens where absolute positioning would cause overflow */
    @media (max-width: 1050px) {
        .play-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
        }
        .reference-panel {
            position: relative;
            left: auto;
            top: auto;
            width: 140px;
            order: -1;
        }
    }
</style>
@endpush

@section('content')
<div style="font-family: var(--font-sans, system-ui, sans-serif); padding: 1rem 0;">

    <div style="text-align: center; margin-bottom: 2rem;">
        <p style="font-size: 11px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text-muted); margin: 0 0 0.75rem;">Playing</p>
        <h1 style="font-size: 2rem; font-weight: 500; color: var(--text-color, #111); margin: 0 0 0.4rem; line-height: 1.2;">{{ $level->name }}</h1>
        <p style="font-size: 0.9375rem; color: var(--text-muted); margin: 0;">Difficulty: <span style="text-transform: capitalize;">{{ $level->difficulty }}</span> ({{ $level->grid_size }}x{{ $level->grid_size }})</p>
        
        <div style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; background: var(--card-bg, #fff); border: 0.5px solid var(--border-color); padding: 0.5rem 1rem; border-radius: 100px; margin-top: 1.5rem;">
            <span style="font-size: 0.8125rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); font-weight: 600;">Time</span>
            <span id="timer" style="font-size: 1.125rem; font-weight: 600; color: var(--primary); font-family: monospace;">0s</span>
        </div>
    </div>

    <div class="play-area">
        <div class="game-board" id="gameBoard" style="flex: 1 1 auto; width: 100%;">
            <div id="tile-container"></div>
        </div>

        <div class="reference-panel">
            <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0 0 0.5rem; text-align: center; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 600;">Reference</p>
            <img src="{{ asset($level->image_path) }}" style="width: 100%; border-radius: 6px; display: block;" alt="Reference Image">
        </div>
    </div>

    <div style="text-align: center; margin-top: 2.5rem; display: flex; gap: 1rem; justify-content: center;">
        <button class="btn btn-secondary" onclick="shuffleTiles()">Restart</button>
        <a href="{{ route('customer.game.index') }}" class="btn btn-secondary">Back to Levels</a>
    </div>

    <div class="win-overlay" id="winOverlay">
        <div style="background: var(--card-bg, #fff); border: 0.5px solid var(--border-color); border-radius: 16px; padding: 3rem; text-align: center; box-shadow: 0 20px 40px rgba(0,0,0,0.2);">
            <h2>🎉 Solved!</h2>
            <p style="font-size: 1.125rem; margin-bottom: 2rem; color: var(--text-muted);">You completed the puzzle in <strong id="finalTime" style="color: var(--text-color, #111);"></strong> seconds.</p>
            <a href="{{ route('customer.game.index') }}" class="btn">Back to Levels</a>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const gridSize = {{ $level->grid_size }};
    const container = document.getElementById('tile-container');
    let tiles = []; // index = pos, value = correctIndex
    let tileElements = []; // array of DOM elements ordered by correctIndex
    let emptyIndex = gridSize * gridSize - 1; // last tile is empty
    let isPlaying = false;
    let timeTaken = 0;
    let timerInterval = null;

    // Initialize tiles
    function init() {
        for (let i = 0; i < gridSize * gridSize; i++) {
            let tile = document.createElement('div');
            tile.classList.add('puzzle-tile');
            
            if (i === emptyIndex) {
                tile.classList.add('empty');
            } else {
                let row = Math.floor(i / gridSize);
                let col = i % gridSize;
                
                let bgSize = gridSize * 100;
                tile.style.backgroundSize = `${bgSize}% ${bgSize}%`;
                
                let x = (gridSize > 1) ? (col / (gridSize - 1)) * 100 : 0;
                let y = (gridSize > 1) ? (row / (gridSize - 1)) * 100 : 0;
                tile.style.backgroundPosition = `${x}% ${y}%`;
            }
            
            tile.dataset.correctIndex = i;
            tile.addEventListener('click', () => handleTileClick(tile));
            
            tileElements.push(tile);
            container.appendChild(tile);
        }
        
        // Initial correct positions
        tiles = Array.from({length: gridSize * gridSize}, (_, i) => i);
        updateTilePositions(false);
        
        shuffleTiles();
    }

    function updateTilePositions(check = false) {
        tiles.forEach((correctIndex, pos) => {
            let tile = tileElements[correctIndex];
            let row = Math.floor(pos / gridSize);
            let col = pos % gridSize;
            tile.style.transform = `translate(calc(${col * 100}% + ${col * 2}px), calc(${row * 100}% + ${row * 2}px))`;
        });
        
        if (check) setTimeout(checkWin, 200);
    }

    function handleTileClick(tile) {
        if (!isPlaying) return;

        let correctIndex = parseInt(tile.dataset.correctIndex);
        let clickedPos = tiles.indexOf(correctIndex);
        let emptyPos = tiles.indexOf(emptyIndex);

        // Check if adjacent (same row and col diff is 1, or same col and row diff is 1)
        let emptyRow = Math.floor(emptyPos / gridSize);
        let emptyCol = emptyPos % gridSize;
        let clickedRow = Math.floor(clickedPos / gridSize);
        let clickedCol = clickedPos % gridSize;

        let isAdjacent = Math.abs(emptyRow - clickedRow) + Math.abs(emptyCol - clickedCol) === 1;

        if (isAdjacent) {
            // Swap
            [tiles[emptyPos], tiles[clickedPos]] = [tiles[clickedPos], tiles[emptyPos]];
            updateTilePositions(true);
        }
    }

    function shuffleTiles() {
        isPlaying = false;
        clearInterval(timerInterval);
        timeTaken = 0;
        document.getElementById('timer').innerText = `0s`;

        // Start from solved state
        tiles = Array.from({length: gridSize * gridSize}, (_, i) => i);
        
        // Disable transition during shuffle
        tileElements.forEach(t => t.style.transition = 'none');

        // Perform N random valid moves
        let moves = gridSize * gridSize * 10;
        for (let i = 0; i < moves; i++) {
            let emptyPos = tiles.indexOf(emptyIndex);
            let validMoves = [];
            
            let row = Math.floor(emptyPos / gridSize);
            let col = emptyPos % gridSize;
            
            if (row > 0) validMoves.push(emptyPos - gridSize); // up
            if (row < gridSize - 1) validMoves.push(emptyPos + gridSize); // down
            if (col > 0) validMoves.push(emptyPos - 1); // left
            if (col < gridSize - 1) validMoves.push(emptyPos + 1); // right
            
            let randomMove = validMoves[Math.floor(Math.random() * validMoves.length)];
            [tiles[emptyPos], tiles[randomMove]] = [tiles[randomMove], tiles[emptyPos]];
        }
        
        updateTilePositions(false);
        
        // Re-enable transition and start game
        setTimeout(() => {
            tileElements.forEach(t => t.style.transition = 'transform 0.2s cubic-bezier(0.4, 0, 0.2, 1)');
            isPlaying = true;
            timerInterval = setInterval(() => {
                timeTaken++;
                document.getElementById('timer').innerText = `${timeTaken}s`;
            }, 1000);
        }, 50);
    }

    function checkWin() {
        let isWin = tiles.every((val, index) => val === index);
        if (isWin) {
            isPlaying = false;
            clearInterval(timerInterval);
            
            // Fill the empty tile with the image
            let emptyTile = tileElements[emptyIndex];
            if(emptyTile) {
                emptyTile.classList.remove('empty');
                let row = Math.floor(emptyIndex / gridSize);
                let col = emptyIndex % gridSize;
                let bgSize = gridSize * 100;
                emptyTile.style.backgroundSize = `${bgSize}% ${bgSize}%`;
                let x = (gridSize > 1) ? (col / (gridSize - 1)) * 100 : 0;
                let y = (gridSize > 1) ? (row / (gridSize - 1)) * 100 : 0;
                emptyTile.style.backgroundPosition = `${x}% ${y}%`;
            }

            document.getElementById('finalTime').innerText = timeTaken;
            document.getElementById('winOverlay').style.display = 'flex';

            // Send complete request
            fetch("{{ route('customer.game.complete', $level->id) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ time_taken: timeTaken })
            });
        }
    }

    init();
</script>
@endpush
