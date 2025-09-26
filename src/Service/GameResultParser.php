<?php

namespace App\Service;

use App\Entity\GameType;

class GameResultParser
{
    public function parseGameResult(string $input): ?array
    {
        $lines = array_filter(array_map('trim', explode("\n", $input)));

        if (empty($lines)) {
            return null;
        }

        // Try to parse different game types
        $result = $this->parseWordle($lines) ??
                 $this->parseConnections($lines) ??
                 $this->parseStrands($lines) ??
                 $this->parseMiniCrossword($lines) ??
                 $this->parseSpellingBee($lines);

        return $result;
    }

    private function parseWordle(array $lines): ?array
    {
        foreach ($lines as $line) {
            // Wordle pattern: "Wordle 1,234 4/6"
            if (preg_match('/^Wordle\s+([0-9,]+)\s+([0-6X])\/6/i', $line, $matches)) {
                $puzzleNumber = str_replace(',', '', $matches[1]);
                $score = $matches[2] === 'X' ? 7 : (int)$matches[2]; // X means failed (7 points)

                return [
                    'gameType' => GameType::WORDLE,
                    'puzzleNumber' => $puzzleNumber,
                    'score' => $score,
                    'body' => implode("\n", $lines)
                ];
            }
        }
        return null;
    }

    private function parseConnections(array $lines): ?array
    {
        foreach ($lines as $line) {
            // Connections pattern: "Connections Puzzle #123"
            if (preg_match('/^Connections\s+Puzzle\s+#([0-9]+)/i', $line, $matches)) {
                $puzzleNumber = $matches[1];

                // Count mistakes (🟨 = mistake, 🟩 = correct)
                $mistakes = 0;
                foreach ($lines as $resultLine) {
                    $mistakes += substr_count($resultLine, '🟨');
                }

                return [
                    'gameType' => GameType::CONNECTIONS,
                    'puzzleNumber' => $puzzleNumber,
                    'score' => $mistakes, // Lower is better
                    'body' => implode("\n", $lines)
                ];
            }
        }
        return null;
    }

    private function parseStrands(array $lines): ?array
    {
        foreach ($lines as $line) {
            // Strands pattern: "Strands #123"
            if (preg_match('/^Strands\s+#([0-9]+)/i', $line, $matches)) {
                $puzzleNumber = $matches[1];

                // Count hints used (💡 symbols)
                $hints = 0;
                foreach ($lines as $resultLine) {
                    $hints += substr_count($resultLine, '💡');
                }

                return [
                    'gameType' => GameType::STRANDS,
                    'puzzleNumber' => $puzzleNumber,
                    'score' => $hints, // Lower is better
                    'body' => implode("\n", $lines)
                ];
            }
        }
        return null;
    }

    private function parseMiniCrossword(array $lines): ?array
    {
        foreach ($lines as $line) {
            // Mini Crossword pattern: "Mini Crossword" with time
            if (preg_match('/Mini\s+Crossword/i', $line)) {
                // Look for time pattern in subsequent lines
                foreach ($lines as $timeLine) {
                    if (preg_match('/([0-9]+):([0-9]+)/', $timeLine, $matches)) {
                        $totalSeconds = (int)$matches[1] * 60 + (int)$matches[2];
                        $date = date('Y-m-d'); // Use today's date

                        return [
                            'gameType' => GameType::MINI_CROSSWORD,
                            'puzzleNumber' => $date,
                            'score' => $totalSeconds, // Lower is better
                            'body' => implode("\n", $lines)
                        ];
                    }
                }
            }
        }
        return null;
    }

    private function parseSpellingBee(array $lines): ?array
    {
        foreach ($lines as $line) {
            // Spelling Bee pattern: Look for date and points
            if (preg_match('/Spelling\s+Bee/i', $line)) {
                // Look for points pattern
                foreach ($lines as $pointLine) {
                    if (preg_match('/([0-9]+)\s+points?/i', $pointLine, $matches)) {
                        $points = (int)$matches[1];
                        $date = date('Y-m-d'); // Use today's date

                        return [
                            'gameType' => GameType::SPELLING_BEE,
                            'puzzleNumber' => $date,
                            'score' => $points, // Higher is better
                            'body' => implode("\n", $lines)
                        ];
                    }
                }
            }
        }
        return null;
    }
}