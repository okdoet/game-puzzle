@extends('layouts.app')

@section('title', 'Playing: ' . $level->name)

@push('styles')
<style>
    .game-board {
        display: grid;
        gap: 2px;
        background: #1e293b;
        padding: 4px;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        margin: 0 auto;
        /* max-width will be calculated based on grid size to prevent it from being too huge */
        max-width: 600px; 
        aspect-ratio: 1/1; /* enforce square */
    }

    .puzzle-tile {
        background-color: var(--card-bg);
        background-image: url('{{ asset("storage/" . $level->image_path) }}');
        background-repeat: no-repeat;
        cursor: pointer;
        transition: transform 0.1s;
        border-radius: 4px;
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
        background: rgba(0,0,0,0.8);
        display: none;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        backdrop-filter: blur(8px);
    }
    .win-overlay h2 {
        font-size: 3rem;
        color: var(--success);
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<div style="text-align: center; margin-bottom: 2rem;">
    <h2>{{ $level->name }}</h2>
    <p style="color: var(--text-muted);">Difficulty: <span style="text-transform: capitalize;">{{ $level->difficulty }}</span> ({{ $level->grid_size }}x{{ $level->grid_size }})</p>
    <div id="timer" style="font-size: 1.5rem; font-weight: bold; margin-top: 1rem;">Time: 0s</div>
</div>

<div class="game-board" id="gameBoard">
    <!-- Tiles will be injected by JS -->
</div>

<div style="text-align: center; margin-top: 2rem;">
    <button class="btn" onclick="shuffleTiles()">Restart</button>
    <a href="{{ route('customer.game.index') }}" class="btn" style="background: rgba(255,255,255,0.1); margin-left: 1rem;">Back</a>
</div>

<div class="win-overlay" id="winOverlay">
    <h2>Puzzle Solved!</h2>
    <p style="font-size: 1.25rem; margin-bottom: 2rem;">Time taken: <span id="finalTime"></span> seconds</p>
    <a href="{{ route('customer.game.index') }}" class="btn">Back to Levels</a>
</div>

@endsection

@push('scripts')
<script>
    const gridSize = {{ $level->grid_size }};
    const board = document.getElementById('gameBoard');
    let tiles = [];
    let emptyIndex = gridSize * gridSize - 1; // last tile is empty
    let isPlaying = false;
    let timeTaken = 0;
    let timerInterval = null;

    board.style.gridTemplateColumns = `repeat(${gridSize}, 1fr)`;
    board.style.gridTemplateRows = `repeat(${gridSize}, 1fr)`;

    // Initialize tiles
    function init() {
        for (let i = 0; i < gridSize * gridSize; i++) {
            let tile = document.createElement('div');
            tile.classList.add('puzzle-tile');
            
            if (i === emptyIndex) {
                tile.classList.add('empty');
            } else {
                // Calculate background position
                let row = Math.floor(i / gridSize);
                let col = i % gridSize;
                
                let bgSize = gridSize * 100;
                tile.style.backgroundSize = `${bgSize}% ${bgSize}%`;
                
                // background-position calculation
                let x = (col / (gridSize - 1)) * 100;
                let y = (row / (gridSize - 1)) * 100;
                tile.style.backgroundPosition = `${x}% ${y}%`;
            }
            
            tile.dataset.index = i;
            tile.addEventListener('click', () => handleTileClick(i));
            tiles.push(i);
            board.appendChild(tile);
        }
        
        // Shuffle after init
        shuffleTiles();
    }

    function renderBoard() {
        board.innerHTML = '';
        tiles.forEach((correctIndex, currentIndex) => {
            let tile = document.createElement('div');
            tile.classList.add('puzzle-tile');
            
            if (correctIndex === gridSize * gridSize - 1) {
                tile.classList.add('empty');
            } else {
                let row = Math.floor(correctIndex / gridSize);
                let col = correctIndex % gridSize;
                
                let bgSize = gridSize * 100;
                tile.style.backgroundSize = `${bgSize}% ${bgSize}%`;
                
                let x = (gridSize > 1) ? (col / (gridSize - 1)) * 100 : 0;
                let y = (gridSize > 1) ? (row / (gridSize - 1)) * 100 : 0;
                tile.style.backgroundPosition = `${x}% ${y}%`;
            }
            
            tile.addEventListener('click', () => handleTileClick(currentIndex));
            board.appendChild(tile);
        });
    }

    function handleTileClick(index) {
        if (!isPlaying) return;

        let emptyPos = tiles.indexOf(gridSize * gridSize - 1);
        let clickedPos = index;

        // Check if adjacent (same row and col diff is 1, or same col and row diff is 1)
        let emptyRow = Math.floor(emptyPos / gridSize);
        let emptyCol = emptyPos % gridSize;
        let clickedRow = Math.floor(clickedPos / gridSize);
        let clickedCol = clickedPos % gridSize;

        let isAdjacent = Math.abs(emptyRow - clickedRow) + Math.abs(emptyCol - clickedCol) === 1;

        if (isAdjacent) {
            // Swap
            [tiles[emptyPos], tiles[clickedPos]] = [tiles[clickedPos], tiles[emptyPos]];
            renderBoard();
            checkWin();
        }
    }

    function shuffleTiles() {
        isPlaying = false;
        clearInterval(timerInterval);
        timeTaken = 0;
        document.getElementById('timer').innerText = `Time: 0s`;

        // Start from solved state
        tiles = Array.from({length: gridSize * gridSize}, (_, i) => i);
        
        // Perform N random valid moves
        let moves = gridSize * gridSize * 10;
        for (let i = 0; i < moves; i++) {
            let emptyPos = tiles.indexOf(gridSize * gridSize - 1);
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
        
        renderBoard();
        
        isPlaying = true;
        timerInterval = setInterval(() => {
            timeTaken++;
            document.getElementById('timer').innerText = `Time: ${timeTaken}s`;
        }, 1000);
    }

    function checkWin() {
        let isWin = tiles.every((val, index) => val === index);
        if (isWin) {
            isPlaying = false;
            clearInterval(timerInterval);
            
            // Fill the empty tile with the image
            let emptyTile = board.querySelector('.empty');
            if(emptyTile) {
                emptyTile.classList.remove('empty');
                let row = Math.floor((gridSize * gridSize - 1) / gridSize);
                let col = (gridSize * gridSize - 1) % gridSize;
                let bgSize = gridSize * 100;
                emptyTile.style.backgroundSize = `${bgSize}% ${bgSize}%`;
                let x = (col / (gridSize - 1)) * 100;
                let y = (row / (gridSize - 1)) * 100;
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
