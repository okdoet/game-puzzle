@extends('layouts.app')

@section('title', 'Tic Tac Toe')

@push('styles')
<style>
    .ttt-wrapper {
        max-width: 460px;
        margin: 0 auto;
        font-family: var(--font-sans, system-ui, sans-serif);
        padding: 2rem 0 1rem;
    }

    .ttt-modes {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .ttt-mode-btn {
        flex: 1;
        padding: 0.75rem 1rem;
        background: var(--card-banner-bg, #f8f8f9);
        border: 0.5px solid var(--border-color);
        color: var(--text-muted);
        border-radius: 10px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .ttt-mode-btn:hover {
        color: var(--text-color, #111);
        border-color: rgba(0,0,0,0.2);
    }
    
    [data-theme="dark"] .ttt-mode-btn:hover {
        border-color: rgba(255,255,255,0.3);
    }

    .ttt-mode-btn.active {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
    }

    .ttt-status {
        text-align: center;
        font-size: 1.0625rem;
        font-weight: 500;
        margin-bottom: 1.5rem;
        min-height: 1.5rem;
        color: var(--text-color, #111);
    }

    .ttt-board {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .ttt-cell {
        aspect-ratio: 1 / 1;
        background: var(--card-bg, #fff);
        border: 0.5px solid var(--border-color);
        border-radius: 12px;
        font-size: 3rem;
        font-weight: 500;
        color: var(--text-color, #111);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        user-select: none;
    }

    .ttt-cell:hover:not(.taken) {
        background: var(--card-banner-bg, #f8f8f9);
        border-color: rgba(0,0,0,0.2);
    }
    
    [data-theme="dark"] .ttt-cell:hover:not(.taken) {
        border-color: rgba(255,255,255,0.3);
    }

    .ttt-cell.x { color: var(--primary); }
    .ttt-cell.o { color: #ec4899; }

    .ttt-cell.win {
        background: rgba(16, 185, 129, 0.1);
        border-color: #10b981;
    }

    .ttt-scores {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .ttt-score {
        padding: 1rem 0.5rem;
        background: var(--card-bg, #fff);
        border: 0.5px solid var(--border-color);
        border-radius: 12px;
    }

    .ttt-score-label {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 500;
    }

    .ttt-score-value {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-color, #111);
    }

    .ttt-score.x .ttt-score-value { color: var(--primary); }
    .ttt-score.o .ttt-score-value { color: #ec4899; }
    .ttt-score.draw .ttt-score-value { color: var(--text-muted); }
</style>
@endpush

@section('content')
<div class="ttt-wrapper">
    <div style="text-align: center; margin-bottom: 2rem;">
        <p style="font-size: 11px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text-muted); margin: 0 0 0.75rem;">Strategy</p>
        <h1 style="font-size: 2rem; font-weight: 500; color: var(--text-color, #111); margin: 0 0 0.4rem; line-height: 1.2;">Tic Tac Toe</h1>
        <p style="font-size: 0.9375rem; color: var(--text-muted); margin: 0;">Play against a friend or challenge the AI</p>
    </div>

    <div style="background: var(--card-bg, #fff); border: 0.5px solid var(--border-color); border-radius: 16px; padding: 2rem;">
        <div class="ttt-modes">
            <button class="ttt-mode-btn active" data-mode="pvp">2 Player</button>
            <button class="ttt-mode-btn" data-mode="ai">vs AI</button>
        </div>

        <div class="ttt-status" id="ttt-status">Player 1's turn</div>

        <div class="ttt-board" id="ttt-board">
            @for($i = 0; $i < 9; $i++)
                <div class="ttt-cell" data-index="{{ $i }}"></div>
            @endfor
        </div>

        <div class="ttt-scores">
            <div class="ttt-score x">
                <div class="ttt-score-label" id="label-x">Player 1</div>
                <div class="ttt-score-value" id="score-x">0</div>
            </div>
            <div class="ttt-score draw">
                <div class="ttt-score-label">Draws</div>
                <div class="ttt-score-value" id="score-draw">0</div>
            </div>
            <div class="ttt-score o">
                <div class="ttt-score-label" id="label-o">Player 2</div>
                <div class="ttt-score-value" id="score-o">0</div>
            </div>
        </div>

        <button class="btn" id="ttt-reset" style="width: 100%; text-align: center;">New Game</button>
    </div>
    
    <div style="text-align: center; margin-top: 2rem;">
        <a href="{{ route('customer.dashboard') }}" class="btn btn-secondary" style="font-size: 0.875rem; padding: 0.5rem 1rem;">Back to Dashboard</a>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const boardEl = document.getElementById('ttt-board');
    const statusEl = document.getElementById('ttt-status');
    const cells = Array.from(boardEl.querySelectorAll('.ttt-cell'));
    const resetBtn = document.getElementById('ttt-reset');
    const modeBtns = Array.from(document.querySelectorAll('.ttt-mode-btn'));
    const labelX = document.getElementById('label-x');
    const labelO = document.getElementById('label-o');

    const scores = { X: 0, O: 0, draw: 0 };
    const HUMAN = 'X';
    const AI = 'O';

    let board = Array(9).fill('');
    let current = 'X';
    let startingPlayer = 'X';
    let mode = 'pvp'; // 'pvp' or 'ai'
    let gameOver = false;

    const WINS = [
        [0, 1, 2], [3, 4, 5], [6, 7, 8],
        [0, 3, 6], [1, 4, 7], [2, 5, 8],
        [0, 4, 8], [2, 4, 6]
    ];

    function getWinner(b) {
        for (const line of WINS) {
            const [a, c, d] = line;
            if (b[a] && b[a] === b[c] && b[a] === b[d]) {
                return { player: b[a], line };
            }
        }
        if (b.every(v => v !== '')) return { player: 'draw', line: null };
        return null;
    }

    function render() {
        cells.forEach((cell, i) => {
            cell.textContent = board[i];
            cell.classList.toggle('taken', board[i] !== '');
            cell.classList.remove('x', 'o', 'win');
            if (board[i] === 'X') cell.classList.add('x');
            if (board[i] === 'O') cell.classList.add('o');
        });
    }

    function setStatus(text) {
        statusEl.textContent = text;
    }

    function updateScores() {
        document.getElementById('score-x').textContent = scores.X;
        document.getElementById('score-o').textContent = scores.O;
        document.getElementById('score-draw').textContent = scores.draw;
    }

    function finish(result) {
        gameOver = true;
        if (result.player === 'draw') {
            scores.draw++;
            setStatus("It's a draw!");
        } else {
            scores[result.player]++;
            if (result.line) {
                result.line.forEach(i => cells[i].classList.add('win'));
            }
            if (mode === 'ai') {
                setStatus(result.player === HUMAN ? 'You win! 🎉' : 'AI wins!');
            } else {
                const playerNum = result.player === 'X' ? '1' : '2';
                setStatus('Player ' + playerNum + ' wins! 🎉');
            }
        }
        updateScores();
    }

    function nextTurnStatus() {
        if (mode === 'ai') {
            setStatus(current === HUMAN ? 'Your turn' : 'AI is thinking...');
        } else {
            const playerNum = current === 'X' ? '1' : '2';
            setStatus("Player " + playerNum + "'s turn");
        }
    }

    function move(index, player) {
        if (board[index] !== '' || gameOver) return false;
        board[index] = player;
        render();
        const result = getWinner(board);
        if (result) {
            finish(result);
            return true;
        }
        current = player === 'X' ? 'O' : 'X';
        nextTurnStatus();
        return true;
    }

    // Minimax with depth so the AI prefers faster wins / slower losses.
    function minimax(b, player) {
        const result = getWinner(b);
        if (result) {
            if (result.player === AI) return { score: 10 };
            if (result.player === HUMAN) return { score: -10 };
            return { score: 0 };
        }

        const moves = [];
        for (let i = 0; i < 9; i++) {
            if (b[i] === '') {
                b[i] = player;
                const result = minimax(b, player === AI ? HUMAN : AI);
                moves.push({ index: i, score: result.score });
                b[i] = '';
            }
        }

        if (player === AI) {
            return moves.reduce((best, m) => (m.score > best.score ? m : best));
        }
        return moves.reduce((best, m) => (m.score < best.score ? m : best));
    }

    function aiMove() {
        if (gameOver) return;
        const best = minimax(board.slice(), AI);
        move(best.index, AI);
    }

    function reset(isModeSwitch = false) {
        board = Array(9).fill('');
        if (isModeSwitch === true) {
            startingPlayer = 'X';
        } else if (mode === 'pvp') {
            startingPlayer = startingPlayer === 'X' ? 'O' : 'X';
        } else {
            startingPlayer = 'X';
        }
        current = startingPlayer;
        gameOver = false;
        render();
        nextTurnStatus();
    }

    cells.forEach(cell => {
        cell.addEventListener('click', () => {
            const index = parseInt(cell.dataset.index, 10);
            if (gameOver || board[index] !== '') return;

            if (mode === 'ai') {
                if (current !== HUMAN) return;
                move(index, HUMAN);
                if (!gameOver) {
                    setTimeout(aiMove, 300);
                }
            } else {
                move(index, current);
            }
        });
    });

    modeBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            modeBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            mode = btn.dataset.mode;
            labelX.textContent = mode === 'ai' ? 'Player' : 'Player 1';
            labelO.textContent = mode === 'ai' ? 'AI' : 'Player 2';
            scores.X = 0;
            scores.O = 0;
            scores.draw = 0;
            updateScores();
            reset(true);
        });
    });

    resetBtn.addEventListener('click', () => reset(false));

    render();
    nextTurnStatus();
})();
</script>
@endpush
