# Claude Project Notes

## Quick Reference

### Deployment
```bash
make deploy        # Full deploy (files + DB schema)
make deploy-files  # Just sync code
make deploy-schema # Just update DB
make serve         # Local dev server at localhost:8000
```

### Adding a New Game Type

1. **Add enum case** in `src/Entity/GameType.php`:
   ```php
   case NEW_GAME = 'new_game';
   ```

2. **Add parser method** in `src/Service/GameResultParser.php`:
   - Create `parseNewGame(array $lines): ?array` method
   - Add to parser chain in `parseGameResult()` method

3. **Update UI** in `views/input.php`:
   - Add to "Supported Games" list

4. **Deploy**: `make deploy`

### Scoring System
All scores are normalized so **higher = better** for leaderboard sorting.

| Game | Conversion |
|------|------------|
| Wordle | `7 - guesses` (1 guess = 6 pts, fail = 0) |
| Connections | `5 - mistakes` (perfect = 5, fail = 0) |
| Strands | `-hints` (perfect = 0, hints = negative) |
| Mini Crossword | `10000 - seconds` (faster = higher) |
| Spelling Bee | Points directly |
| Bracket City | Total score directly |
| Catfishing | Score * 10 (5/10 = 50) |

### Project Structure
- `src/Entity/` - Doctrine entities (User, GameScore, GameType enum, PuzzleInput)
- `src/Service/GameResultParser.php` - Parses pasted game results
- `src/Controller/` - Route handlers
- `views/` - PHP templates (layout.php is base template)
- `config/doctrine.php` - DB config (MySQL prod, SQLite dev)

### Database
- GameType stored as string (varchar), not DB enum - no schema change needed for new game types
- Unique constraint on (user_id, game_type, puzzle_number) prevents duplicate submissions
- PuzzleInput table logs all parsing attempts for debugging

### Environment
- Production: DreamHost shared hosting
- PHP 8.3 required
- Uses `.env` and `.env.local` for config
