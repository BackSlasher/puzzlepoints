<?php
$title = ucwords(str_replace('_', ' ', $gameType->value)) . ' #' . $puzzleNumber . ' Results';
ob_start();
?>

<h2><?= ucwords(str_replace('_', ' ', $gameType->value)) ?> #<?= htmlspecialchars($puzzleNumber) ?> Results</h2>

<p><a href="/results">← Back to all results</a></p>

<?php if (empty($results)): ?>
    <p>No results found for this game and puzzle.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th><i class="bi bi-trophy"></i> Rank</th>
                    <th><i class="bi bi-person"></i> User</th>
                    <th><i class="bi bi-star"></i> Score</th>
                    <th><i class="bi bi-clock"></i> Submitted</th>
                    <th><i class="bi bi-card-text"></i> Result</th>
                </tr>
            </thead>
            <tbody>
                <?php $rank = 1; ?>
                <?php foreach ($results as $result): ?>
                    <tr>
                        <td class="text-center">
                            <?php if ($rank === 1): ?>
                                <span class="fs-4">🥇</span>
                            <?php elseif ($rank === 2): ?>
                                <span class="fs-4">🥈</span>
                            <?php elseif ($rank === 3): ?>
                                <span class="fs-4">🥉</span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><?= $rank ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-info"><?= htmlspecialchars($result->getUser()->getDisplayname()) ?></span>
                        </td>
                        <td>
                            <span class="badge bg-success">
                                <?= $result->getDisplayScore() ?: $result->getScore() ?>
                            </span>
                        </td>
                        <td class="timestamp" data-timestamp="<?= $result->getCreatedAt()->format('Y-m-d H:i:s') ?>">
                            <?= $result->getCreatedAt()->format('g:i A') ?>
                        </td>
                        <td>
                            <div class="game-body"><?= htmlspecialchars($result->getBody()) ?></div>
                        </td>
                    </tr>
                    <?php $rank++; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        <h3>Share this page:</h3>
        <code><?= $_SERVER['HTTP_HOST'] ?? 'localhost' ?><?= $_SERVER['REQUEST_URI'] ?></code>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require_once 'layout.php';
?>