<?php
$title = 'Submit Game Results';
ob_start();
?>

<div class="card">
    <div class="card-body">
        <h2 class="card-title text-center mb-4">
            <i class="bi bi-cloud-arrow-up text-primary"></i> Submit Your Game Results
        </h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle"></i> <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label for="displayname" class="form-label">
                    <i class="bi bi-person"></i> Display Name
                </label>
                <?php if ($user ?? false): ?>
                    <input type="text" class="form-control" id="displayname" name="displayname"
                           value="<?= htmlspecialchars($user->getDisplayname()) ?>" required>
                    <div class="form-text">
                        <a href="/logout" class="text-decoration-none">
                            <i class="bi bi-arrow-right-square"></i> Switch user
                        </a>
                    </div>
                <?php else: ?>
                    <input type="text" class="form-control" id="displayname" name="displayname"
                           value="<?= htmlspecialchars($displayname ?? '') ?>" required>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="game_input" class="form-label">
                    <i class="bi bi-textarea-t"></i> Game Results
                </label>
                <textarea class="form-control" id="game_input" name="game_input" rows="8" required
                          placeholder="Paste your game results here, e.g.:

Wordle 1,234 4/6

⬛🟨⬛⬛⬛
⬛🟩🟩⬛⬛
🟩🟩🟩⬛🟩
🟩🟩🟩🟩🟩"><?= htmlspecialchars($game_input ?? '') ?></textarea>
            </div>

            <div class="text-center">
                <button type="submit" name="submit" class="btn btn-primary btn-lg me-3 mb-2">
                    <i class="bi bi-check-circle"></i> Submit Results
                </button>
                <button type="submit" name="submit_and_continue" class="btn btn-primary btn-lg mb-2">
                    <i class="bi bi-plus-circle"></i> Submit & Add Another
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <h3 class="card-title">
            <i class="bi bi-controller text-success"></i> Supported Games
        </h3>
        <div class="row">
            <div class="col-md-6">
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Wordle</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Connections</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Strands</li>
                </ul>
            </div>
            <div class="col-md-6">
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Mini Crossword</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Spelling Bee</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-success"></i> Bracket City</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once 'layout.php';
?>