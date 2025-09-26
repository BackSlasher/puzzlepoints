<?php
$title = 'All Results';
ob_start();
?>

<h2>All Game Results</h2>

<div class="filters">
    <h3>Filters</h3>
    <form method="GET">
        <div class="filter-row">
            <div>
                <label for="game">Game Type:</label>
                <select id="game" name="game">
                    <option value="">All Games</option>
                    <?php foreach ($gameTypes as $gameType): ?>
                        <option value="<?= $gameType->value ?>" <?= $gameFilter === $gameType->value ? 'selected' : '' ?>>
                            <?= ucwords(str_replace('_', ' ', $gameType->value)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="user">User:</label>
                <select id="user" name="user">
                    <option value="">All Users</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user->getId() ?>" <?= $userFilter == $user->getId() ? 'selected' : '' ?>>
                            <?= htmlspecialchars($user->getDisplayname()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="puzzle">Puzzle Number:</label>
                <input type="text" id="puzzle" name="puzzle" value="<?= htmlspecialchars($puzzleFilter) ?>" placeholder="e.g. 1234 or 2025-01-15" <?= empty($gameFilter) ? 'disabled' : '' ?>>
            </div>
            <div>
                <button type="submit">Filter</button>
            </div>
        </div>
    </form>
</div>

<?php if (empty($results)): ?>
    <p>No results found. <a href="/input">Submit your first result!</a></p>
<?php else: ?>
    <table class="results-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>User</th>
                <th>Game</th>
                <th>Puzzle</th>
                <th>Score</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $result): ?>
                <tr>
                    <td class="timestamp" data-timestamp="<?= $result->getCreatedAt()->format('Y-m-d H:i:s') ?>"><?= $result->getCreatedAt()->format('M j, Y g:i A') ?></td>
                    <td><?= htmlspecialchars($result->getUser()->getDisplayname()) ?></td>
                    <td><?= ucwords(str_replace('_', ' ', $result->getGameType()->value)) ?></td>
                    <td>
                        <a href="/results/<?= $result->getGameType()->value ?>/<?= urlencode($result->getPuzzleNumber()) ?>">
                            <?= htmlspecialchars($result->getPuzzleNumber()) ?>
                        </a>
                    </td>
                    <td><?= $result->getScore() ?></td>
                    <td>
                        <div class="game-body"><?= htmlspecialchars($result->getBody()) ?></div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const gameSelect = document.getElementById('game');
    const puzzleInput = document.getElementById('puzzle');

    function togglePuzzleInput() {
        if (gameSelect.value) {
            puzzleInput.disabled = false;
            puzzleInput.style.backgroundColor = '';
            puzzleInput.style.color = '';
        } else {
            puzzleInput.disabled = true;
            puzzleInput.style.backgroundColor = '#f5f5f5';
            puzzleInput.style.color = '#999';
            puzzleInput.value = '';
        }
    }

    // Set initial state
    togglePuzzleInput();

    // Listen for changes
    gameSelect.addEventListener('change', togglePuzzleInput);
});
</script>

<?php
$content = ob_get_clean();
require_once 'layout.php';
?>