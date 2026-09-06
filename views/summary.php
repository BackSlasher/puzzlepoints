<?php
$title = 'Your Summary';
$gameLabel = fn($type) => ucwords(str_replace('_', ' ', $type->value));
$ordinal = function (int $n): string {
    if ($n % 100 >= 11 && $n % 100 <= 13) {
        return $n . 'th';
    }
    return $n . ([1 => 'st', 2 => 'nd', 3 => 'rd'][$n % 10] ?? 'th');
};
ob_start();
?>

<h2><i class="bi bi-person-circle"></i> <?= htmlspecialchars($user->getDisplayname()) ?></h2>
<p>
    Your games from the last day and a half.
    <a href="/logout" class="text-decoration-none">Not you?</a>
</p>

<h3 class="h5 mt-4"><i class="bi bi-check2-circle"></i> Played today</h3>

<?php if (empty($played)): ?>
    <p>Nothing submitted yet today. <a href="/input">Paste a result</a> to see where you stand.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th><i class="bi bi-controller"></i> Game</th>
                    <th><i class="bi bi-star"></i> Your score</th>
                    <th><i class="bi bi-trophy"></i> Position</th>
                    <th><i class="bi bi-people"></i> Others</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($played as $row):
                    $score = $row['score'];
                    $type = $score->getGameType();
                    $url = '/results/' . $type->value . '/' . urlencode($score->getPuzzleNumber());
                    if ($row['rank'] === 1 && !$row['tied']) {
                        $badge = 'bg-success';
                    } elseif ($row['rank'] === 1) {
                        $badge = 'bg-primary';
                    } else {
                        $badge = 'bg-secondary';
                    }
                ?>
                    <tr>
                        <td>
                            <a href="<?= $url ?>" class="text-decoration-none">
                                <strong><?= $gameLabel($type) ?></strong>
                                <span class="text-muted">#<?= htmlspecialchars($score->getPuzzleNumber()) ?></span>
                            </a>
                        </td>
                        <td><?= htmlspecialchars((string)($score->getDisplayScore() ?: $score->getScore())) ?></td>
                        <td>
                            <span class="badge <?= $badge ?>">
                                <?= $row['tied'] ? 'Tied ' : '' ?><?= $ordinal($row['rank']) ?> of <?= $row['total'] ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= $url ?>" class="text-decoration-none">
                                <?php if ($row['others'] === 0): ?>
                                    Only you so far
                                <?php else: ?>
                                    <strong><?= $row['others'] ?></strong> other<?= $row['others'] === 1 ? '' : 's' ?>
                                <?php endif; ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php if (!empty($stillToPlay)): ?>
    <h3 class="h5 mt-4"><i class="bi bi-hourglass-split"></i> Still to play</h3>
    <p>
        <?php foreach ($stillToPlay as $type): ?>
            <a href="/input" class="btn btn-outline-secondary btn-sm me-1 mb-1"><?= $gameLabel($type) ?></a>
        <?php endforeach; ?>
    </p>
<?php endif; ?>

<p class="mt-4">
    <a href="/input" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Submit a result</a>
</p>

<?php
$content = ob_get_clean();
require_once 'layout.php';
?>
