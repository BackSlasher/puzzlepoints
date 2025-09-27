<?php

namespace App\Service;

use App\Entity\GameType;
use App\Entity\PuzzleInput;
use Doctrine\ORM\EntityManager;

class GameResultParser
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function parseGameResult(string $input, string $displayname = null, string $ipAddress = null, string $userAgent = null): ?array
    {
        $lines = array_filter(array_map('trim', explode("\n", $input)));

        // Create log entry
        $puzzleInputLog = new PuzzleInput();
        $puzzleInputLog->setRawInput($input)
                      ->setSubmittedByDisplayname($displayname)
                      ->setIpAddress($ipAddress)
                      ->setUserAgent($userAgent);

        if (empty($lines)) {
            $puzzleInputLog->setParsedSuccessfully(false)
                          ->setParsingError('Input is empty');
            $this->entityManager->persist($puzzleInputLog);
            $this->entityManager->flush();
            return null;
        }

        // Try to parse different game types
        try {
            $result = $this->parseWordle($lines) ??
                     $this->parseConnections($lines) ??
                     $this->parseStrands($lines) ??
                     $this->parseMiniCrossword($lines) ??
                     $this->parseSpellingBee($lines) ??
                     $this->parseBracketCity($lines);

            if ($result) {
                $puzzleInputLog->setParsedSuccessfully(true)
                              ->setDetectedGameType($result['gameType'])
                              ->setDetectedPuzzleNumber($result['puzzleNumber'])
                              ->setDetectedScore($result['score']);
            } else {
                $puzzleInputLog->setParsedSuccessfully(false)
                              ->setParsingError('No matching game pattern found');
            }
        } catch (\Exception $e) {
            $puzzleInputLog->setParsedSuccessfully(false)
                          ->setParsingError('Parsing exception: ' . $e->getMessage());
            $result = null;
        }

        $this->entityManager->persist($puzzleInputLog);
        $this->entityManager->flush();

        return $result;
    }

    private function parseWordle(array $lines): ?array
    {
        foreach ($lines as $line) {
            // Wordle pattern: "Wordle 1,234 4/6"
            if (preg_match('/^Wordle\s+([0-9,]+)\s+([0-6X])\/6/i', $line, $matches)) {
                $puzzleNumber = str_replace(',', '', $matches[1]);
                $rawScore = $matches[2] === 'X' ? 7 : (int)$matches[2];

                // Convert to "higher is better": 7 - rawScore (so 1 guess = 6 points, failed = 0 points)
                $numericScore = 7 - $rawScore;
                $displayScore = $rawScore === 7 ? "X/6 (Failed)" : "$rawScore/6";

                return [
                    'gameType' => GameType::WORDLE,
                    'puzzleNumber' => $puzzleNumber,
                    'score' => $numericScore,
                    'displayScore' => $displayScore,
                    'body' => implode("\n", $lines)
                ];
            }
        }
        return null;
    }

    private function parseConnections(array $lines): ?array
    {
        $foundConnections = false;
        $puzzleNumber = null;

        foreach ($lines as $line) {
            // Look for "Connections" header
            if (preg_match('/^Connections$/i', $line)) {
                $foundConnections = true;
            }
            // Look for puzzle number in formats like "Puzzle #123" or "#123"
            if (preg_match('/^(?:Puzzle\s+)?#([0-9]+)$/i', $line, $matches)) {
                $puzzleNumber = $matches[1];
            }
            // Also handle single line format "Connections Puzzle #123"
            if (preg_match('/^Connections\s+Puzzle\s+#([0-9]+)/i', $line, $matches)) {
                $foundConnections = true;
                $puzzleNumber = $matches[1];
            }
        }

        if ($foundConnections && $puzzleNumber) {
            // Count completed rows (each row of 4 same emojis = 1 solved group)
            // Perfect game = 4 groups solved = 0 mistakes
            $colorRows = 0;
            foreach ($lines as $resultLine) {
                // Count rows with exactly 4 of the same color emoji
                if (preg_match('/^(🟦🟦🟦🟦|🟨🟨🟨🟨|🟪🟪🟪🟪|🟩🟩🟩🟩)$/', $resultLine)) {
                    $colorRows++;
                }
            }

            // Score is mistakes made: 4 (max groups) minus completed groups
            // Perfect game (4 groups) = 0 mistakes
            $mistakes = max(0, 4 - $colorRows);

            // Convert to "higher is better": 4 - mistakes (so 0 mistakes = 4 points, 4 mistakes = 0 points)
            $numericScore = 4 - $mistakes;
            $displayScore = $mistakes === 0 ? "Perfect!" : "$mistakes mistakes";

            return [
                'gameType' => GameType::CONNECTIONS,
                'puzzleNumber' => $puzzleNumber,
                'score' => $numericScore,
                'displayScore' => $displayScore,
                'body' => implode("\n", $lines)
            ];
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

                // Convert to "higher is better": use large number - hints (so 0 hints = 1000, 5 hints = 995)
                $numericScore = 1000 - $hints;
                $displayScore = $hints === 0 ? "Perfect!" : "$hints hints";

                return [
                    'gameType' => GameType::STRANDS,
                    'puzzleNumber' => $puzzleNumber,
                    'score' => $numericScore,
                    'displayScore' => $displayScore,
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

                        // Convert to "higher is better": use large number - seconds (faster time = higher score)
                        $numericScore = 10000 - $totalSeconds;
                        $displayScore = gmdate("i:s", $totalSeconds);

                        return [
                            'gameType' => GameType::MINI_CROSSWORD,
                            'puzzleNumber' => $date,
                            'score' => $numericScore,
                            'displayScore' => $displayScore,
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

                        $displayScore = "$points points";

                        return [
                            'gameType' => GameType::SPELLING_BEE,
                            'puzzleNumber' => $date,
                            'score' => $points, // Already "higher is better"
                            'displayScore' => $displayScore,
                            'body' => implode("\n", $lines)
                        ];
                    }
                }
            }
        }
        return null;
    }

    private function parseBracketCity(array $lines): ?array
    {
        foreach ($lines as $line) {
            // Bracket City pattern: "[Bracket City]"
            if (preg_match('/^\[Bracket City\]/i', $line)) {
                // Look for date, rank, and score in next lines
                $puzzleDate = null;
                $score = null;
                $rankEmoji = null;
                $rankTitle = null;

                foreach ($lines as $checkLine) {
                    // Parse date line (e.g., "September 3, 2025")
                    if (preg_match('/^(January|February|March|April|May|June|July|August|September|October|November|December)\s+(\d+),\s+(\d{4})$/i', $checkLine, $dateMatches)) {
                        $month = date('m', strtotime($dateMatches[1]));
                        $day = str_pad($dateMatches[2], 2, '0', STR_PAD_LEFT);
                        $year = $dateMatches[3];
                        $puzzleDate = "$year-$month-$day";
                    }

                    // Parse rank line (e.g., "Rank: 🔮 (Puppet Master)")
                    if (preg_match('/^Rank:\s*([^\s\(]+)\s*\(([^)]+)\)/i', $checkLine, $rankMatches)) {
                        $rankEmoji = trim($rankMatches[1]);
                        $rankTitle = trim($rankMatches[2]);
                    }

                    // Parse score line (e.g., "Total Score: 96.0")
                    if (preg_match('/Total\s+Score:\s+([0-9.]+)/i', $checkLine, $scoreMatches)) {
                        $score = (int)round((float)$scoreMatches[1]);
                    }
                }

                if ($puzzleDate && $score !== null) {
                    // Create display score and numeric score
                    $displayScore = null;
                    $numericScore = $score; // Use base score for ranking (higher points = better)

                    if ($rankEmoji && $rankTitle) {
                        $displayScore = "$rankEmoji ($rankTitle)";
                    } else {
                        $displayScore = "$score points";
                    }

                    // Remove URLs from the body for display
                    $cleanedLines = [];
                    foreach ($lines as $bodyLine) {
                        // Skip lines that are URLs (start with http)
                        if (!preg_match('/^https?:\/\//', $bodyLine)) {
                            $cleanedLines[] = $bodyLine;
                        }
                    }

                    return [
                        'gameType' => GameType::BRACKET_CITY,
                        'puzzleNumber' => $puzzleDate,
                        'score' => $numericScore, // Numeric score for sorting
                        'displayScore' => $displayScore, // User-visible score
                        'body' => implode("\n", $cleanedLines) // URLs removed
                    ];
                }
            }
        }
        return null;
    }

}