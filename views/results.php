<?php
$title = 'All Results';
ob_start();
?>

<h2>All Game Results</h2>

<div class="card mb-4">
    <div class="card-body">
        <h3 class="card-title">
            <i class="bi bi-funnel"></i> Filters
        </h3>
        <form method="GET">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="game" class="form-label">Game Type:</label>
                    <select id="game" name="game" class="form-select">
                        <option value="">All Games</option>
                        <?php foreach ($gameTypes as $gameType): ?>
                            <option value="<?= $gameType->value ?>" <?= $gameFilter === $gameType->value ? 'selected' : '' ?>>
                                <?= ucwords(str_replace('_', ' ', $gameType->value)) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="user" class="form-label">User:</label>
                    <select id="user" name="user" class="form-select">
                        <option value="">All Users</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= $user->getId() ?>" <?= $userFilter == $user->getId() ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user->getDisplayname()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="puzzle" class="form-label">Puzzle Number:</label>
                    <input type="text" id="puzzle" name="puzzle" class="form-control" value="<?= htmlspecialchars($puzzleFilter) ?>" placeholder="e.g. 1234 or 2025-01-15" <?= empty($gameFilter) ? 'disabled' : '' ?>>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if (empty($results)): ?>
    <p>No results found. <a href="/input">Submit your first result!</a></p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th><i class="bi bi-calendar3"></i> Date</th>
                    <th><i class="bi bi-person"></i> User</th>
                    <th><i class="bi bi-controller"></i> Game</th>
                    <th><i class="bi bi-hash"></i> Puzzle</th>
                    <th><i class="bi bi-trophy"></i> Score</th>
                    <th><i class="bi bi-card-text"></i> Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $result): ?>
                    <tr>
                        <td class="timestamp" data-timestamp="<?= $result->getCreatedAt()->format('Y-m-d H:i:s') ?>">
                            <?= $result->getCreatedAt()->format('M j, Y g:i A') ?>
                        </td>
                        <td>
                            <span class="badge bg-secondary"><?= htmlspecialchars($result->getUser()->getDisplayname()) ?></span>
                        </td>
                        <td><?= ucwords(str_replace('_', ' ', $result->getGameType()->value)) ?></td>
                        <td>
                            <a href="/results/<?= $result->getGameType()->value ?>/<?= urlencode($result->getPuzzleNumber()) ?>" class="btn btn-outline-primary btn-sm">
                                <?= htmlspecialchars($result->getPuzzleNumber()) ?>
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-success"><?= $result->getScore() ?></span>
                        </td>
                        <td>
                            <div class="game-body"><?= htmlspecialchars($result->getBody()) ?></div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
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