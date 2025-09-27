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
                <div class="col-md-4">
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
                <div class="col-md-4">
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
                <div class="col-md-4 d-flex align-items-end">
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
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $result): ?>
                    <!-- Main data row -->
                    <tr>
                        <td class="timestamp" data-timestamp="<?= $result->getCreatedAt()->format('Y-m-d H:i:s') ?>">
                            <?= $result->getCreatedAt()->format('M j, Y g:i A') ?>
                        </td>
                        <td>
                            <span class="badge bg-secondary"><?= htmlspecialchars($result->getUser()->getDisplayname()) ?></span>
                        </td>
                        <td>
                            <a href="/results?game=<?= $result->getGameType()->value ?>" class="text-decoration-none">
                                <?= ucwords(str_replace('_', ' ', $result->getGameType()->value)) ?>
                            </a>
                        </td>
                        <td>
                            <a href="/results/<?= $result->getGameType()->value ?>/<?= urlencode($result->getPuzzleNumber()) ?>" class="btn btn-outline-primary btn-sm">
                                <?= htmlspecialchars($result->getPuzzleNumber()) ?>
                            </a>
                        </td>
                        <td>
                            <?= $result->getDisplayScore() ?: $result->getScore() ?>
                        </td>
                    </tr>
                    <!-- Details row -->
                    <tr class="details-row">
                        <td colspan="5" class="details-cell">
                            <div class="game-body"><?= htmlspecialchars($result->getBody()) ?></div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>


<?php
$content = ob_get_clean();
require_once 'layout.php';
?>