@extends('layouts.app')

@section('title', 'Tic Tac Toe')

@push('styles')
<style>
    .ttt-wrapper {
        max-width: 460px;
        margin: 0 auto;
    }

    .ttt-modes {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .ttt-mode-btn {
        flex: 1;
        padding: 0.75rem 1rem;
        background: transparent;
        border: 1px solid var(--border-color);
        color: var(--text-muted);
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .ttt-mode-btn:hover {
        color: var(--text-main);
        border-color: var(--primary);
    }

    .ttt-mode-btn.active {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
    }

    .ttt-status {
        text-align: center;
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        min-height: 1.5rem;
        color: var(--text-main);
    }

    .ttt-board {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .ttt-cell {
        aspect-ratio: 1 / 1;
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        font-size: 3rem;
        font-weight: 700;
        color: var(--text-main);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        user-select: none;
    }

    .ttt-cell:hover:not(.taken) {
        background: rgba(99, 102, 241, 0.15);
        border-color: var(--primary);
    }

    .ttt-cell.x { color: #818cf8; }
    .ttt-cell.o { color: #f472b6; }

    .ttt-cell.win {
        background: rgba(16, 185, 129, 0.2);
        border-color: var(--success);
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
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid var(--border-color);
        border-radius: 12px;
    }

    .ttt-score-label {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-bottom: 0.35rem;
    }

    .ttt-score-value {
        font-size: 1.75rem;
        font-weight: 700;
    }

    .ttt-score.x .ttt-score-value { color: #818cf8; }
    .ttt-score.o .ttt-score-value { color: #f472b6; }
    .ttt-score.draw .ttt-score-value { color: var(--text-muted); }
</style>
@endpush

@section('content')
<div class="header" style="text-align: center; margin-bottom: 2rem;">
    <h1 style="font-size: 2.5rem; margin-bottom: 0.5rem; background: linear-gradient(135deg, #a5b4fc, #c084fc); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Tic Tac Toe</h1>
    <p style="color: var(--text-muted);">Play against a friend or challenge the AI</p>
</div>

<div class="ttt-wrapper">
    <div class="card">
        <div class="ttt-modes">
            <button class="ttt-mode-btn active" data-mode="pvp">2 Player</button>
            <button class="ttt-mode-btn" data-mode="ai">vs AI</button>
        </div>

        <div class="ttt-status" id="ttt-status">Player X's turn</div>

        <div class="ttt-board" id="ttt-board">
            @for($i = 0; $i < 9; $i++)
                <div class="ttt-cell" data-index="{{ $i }}"></div>
            @endfor
        </div>

        <div class="ttt-scores">
            <div class="ttt-score x">
                <div class="ttt-score-label">Player X</div>
                <div class="ttt-score-value" id="score-x">0</div>
            </div>
            <div class="ttt-score draw">
                <div class="ttt-score-label">Draws</div>
                <div class="ttt-score-value" id="score-draw">0</div>
            </div>
            <div class="ttt-score o">
                <div class="ttt-score-label" id="label-o">Player O</div>
                <div class="ttt-score-value" id="score-o">0</div>
            </div>
        </div>

        <button class="btn" id="ttt-reset" style="width: 100%; text-align: center;">New Game</button>
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
    const labelO = document.getElementById('label-o');

    const scores = { X: 0, O: 0, draw: 0 };
    const HUMAN = 'X';
    const AI = 'O';

    let board = Array(9).fill('');
    let current = 'X';
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
                setStatus('Player ' + result.player + ' wins! 🎉');
            }
        }
        updateScores();
    }

    function nextTurnStatus() {
        if (mode === 'ai') {
            setStatus(current === HUMAN ? 'Your turn' : 'AI is thinking...');
        } else {
            setStatus("Player " + current + "'s turn");
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

    function reset() {
        board = Array(9).fill('');
        current = 'X';
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
            labelO.textContent = mode === 'ai' ? 'AI (O)' : 'Player O';
            scores.X = 0;
            scores.O = 0;
            scores.draw = 0;
            updateScores();
            reset();
        });
    });

    resetBtn.addEventListener('click', reset);

    render();
    nextTurnStatus();
})();
</script>
@endpush
