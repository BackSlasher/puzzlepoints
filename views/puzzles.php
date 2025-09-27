<?php
$title = 'Puzzles Overview';
ob_start();
?>

<h2>Puzzles Overview</h2>
<p>All puzzles with submission counts, ordered by most recent activity.</p>

<?php if (empty($puzzleStats)): ?>
    <p>No puzzles found. <a href="/input">Submit your first result!</a></p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th><i class="bi bi-controller"></i> Game</th>
                    <th><i class="bi bi-hash"></i> Puzzle</th>
                    <th><i class="bi bi-people"></i> Submissions</th>
                    <th><i class="bi bi-clock-history"></i> Latest Activity</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($puzzleStats as $puzzle): ?>
                    <tr>
                        <td><?= ucwords(str_replace('_', ' ', $puzzle['gameType']->value)) ?></td>
                        <td>
                            <a href="/results/<?= $puzzle['gameType']->value ?>/<?= urlencode($puzzle['puzzleNumber']) ?>" class="btn btn-outline-primary btn-sm">
                                <?= htmlspecialchars($puzzle['puzzleNumber']) ?>
                            </a>
                        </td>
                        <td>
                            <a href="/results/<?= $puzzle['gameType']->value ?>/<?= urlencode($puzzle['puzzleNumber']) ?>" class="text-decoration-none">
                                <strong><?= $puzzle['submissionCount'] ?></strong>
                                <?= $puzzle['submissionCount'] == 1 ? 'submission' : 'submissions' ?>
                            </a>
                        </td>
                        <td class="timestamp" data-timestamp="<?php
                            $latestDate = $puzzle['latestSubmission'];
                            if ($latestDate instanceof DateTime) {
                                echo $latestDate->format('Y-m-d H:i:s');
                            } else {
                                echo date('Y-m-d H:i:s', strtotime($latestDate));
                            }
                            ?>">
                            <?php
                            $latestDate = $puzzle['latestSubmission'];
                            if ($latestDate instanceof DateTime) {
                                echo $latestDate->format('M j, Y g:i A');
                            } else {
                                echo date('M j, Y g:i A', strtotime($latestDate));
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        <p><strong><?= count($puzzleStats) ?></strong> unique puzzles with <strong><?= array_sum(array_column($puzzleStats, 'submissionCount')) ?></strong> total submissions.</p>
    </div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require_once 'layout.php';
?>