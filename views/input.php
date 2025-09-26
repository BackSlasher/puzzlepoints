<?php
$title = 'Submit Game Results';
ob_start();
?>

<h2>Submit Your Game Results</h2>

<?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST">
    <div class="form-group">
        <label for="displayname">Display Name:</label>
        <?php if ($user ?? false): ?>
            <input type="text" id="displayname" name="displayname" value="<?= htmlspecialchars($user->getDisplayname()) ?>" readonly style="background: #f5f5f5;">
            <small>Logged in as <?= htmlspecialchars($user->getDisplayname()) ?>. <a href="/logout">Change user</a></small>
        <?php else: ?>
            <input type="text" id="displayname" name="displayname" value="<?= htmlspecialchars($displayname ?? '') ?>" required>
            <small>Enter your display name (no password required)</small>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="game_input">Game Results:</label>
        <textarea id="game_input" name="game_input" required placeholder="Paste your game results here, e.g.:

Wordle 1,234 4/6

⬛🟨⬛⬛⬛
⬛🟩🟩⬛⬛
🟩🟩🟩⬛🟩
🟩🟩🟩🟩🟩"><?= htmlspecialchars($game_input ?? '') ?></textarea>
        <small>Supported games: Wordle, Connections, Strands, Mini Crossword, Spelling Bee</small>
    </div>

    <button type="submit">Submit Results</button>
</form>

<div style="margin-top: 40px;">
    <h3>Supported Games:</h3>
    <ul>
        <li>Wordle</li>
        <li>Connections</li>
        <li>Strands</li>
        <li>Mini Crossword</li>
        <li>Spelling Bee</li>
        <li>Bracket City</li>
    </ul>
</div>

<?php
$content = ob_get_clean();
require_once 'layout.php';
?>