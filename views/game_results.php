<?php
$title = ucwords(str_replace('_', ' ', $gameType->value)) . ' #' . $puzzleNumber . ' Results';
ob_start();
?>

<h2><?= ucwords(str_replace('_', ' ', $gameType->value)) ?> #<?= htmlspecialchars($puzzleNumber) ?> Results</h2>

<p><a href="/results">← Back to all results</a></p>

<?php if (empty($results)): ?>
    <p>No results found for this game and puzzle.</p>
<?php else: ?>
    <table class="results-table">
        <thead>
            <tr>
                <th>Rank</th>
                <th>User</th>
                <th>Score</th>
                <th>Submitted</th>
                <th>Result</th>
            </tr>
        </thead>
        <tbody>
            <?php $rank = 1; ?>
            <?php foreach ($results as $result): ?>
                <tr>
                    <td>
                        <?php if ($rank === 1): ?>
                            🥇
                        <?php elseif ($rank === 2): ?>
                            🥈
                        <?php elseif ($rank === 3): ?>
                            🥉
                        <?php else: ?>
                            <?= $rank ?>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($result->getUser()->getDisplayname()) ?></td>
                    <td>
                        <?= $result->getScore() ?>
                        <?php if ($gameType->value === 'spelling_bee'): ?>
                            points
                        <?php elseif ($gameType->value === 'wordle'): ?>
                            /6 <?= $result->getScore() === 7 ? '(Failed)' : '' ?>
                        <?php elseif ($gameType->value === 'connections'): ?>
                            mistakes
                        <?php elseif ($gameType->value === 'strands'): ?>
                            hints
                        <?php elseif ($gameType->value === 'mini_crossword'): ?>
                            <?= gmdate("i:s", $result->getScore()) ?>
                        <?php elseif ($gameType->value === 'bracket_city'): ?>
                            points
                        <?php endif; ?>
                    </td>
                    <td class="timestamp" data-timestamp="<?= $result->getCreatedAt()->format('Y-m-d H:i:s') ?>"><?= $result->getCreatedAt()->format('g:i A') ?></td>
                    <td>
                        <div class="game-body"><?= htmlspecialchars($result->getBody()) ?></div>
                    </td>
                </tr>
                <?php $rank++; ?>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        <h3>Share this page:</h3>
        <code><?= $_SERVER['HTTP_HOST'] ?? 'localhost' ?><?= $_SERVER['REQUEST_URI'] ?></code>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require_once 'layout.php';
?>