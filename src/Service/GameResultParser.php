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

    private function parseBracketCity(array $lines): ?array
    {
        foreach ($lines as $line) {
            // Bracket City pattern: "[Bracket City]"
            if (preg_match('/^\[Bracket City\]/i', $line)) {
                // Look for date in next lines (e.g., "September 3, 2025")
                $puzzleDate = null;
                $score = null;

                foreach ($lines as $checkLine) {
                    // Parse date line (e.g., "September 3, 2025")
                    if (preg_match('/^(January|February|March|April|May|June|July|August|September|October|November|December)\s+(\d+),\s+(\d{4})$/i', $checkLine, $dateMatches)) {
                        $month = date('m', strtotime($dateMatches[1]));
                        $day = str_pad($dateMatches[2], 2, '0', STR_PAD_LEFT);
                        $year = $dateMatches[3];
                        $puzzleDate = "$year-$month-$day";
                    }

                    // Parse score line (e.g., "Total Score: 96.0")
                    if (preg_match('/Total\s+Score:\s+([0-9.]+)/i', $checkLine, $scoreMatches)) {
                        $score = (int)round((float)$scoreMatches[1]); // Convert to integer for consistency
                    }
                }

                if ($puzzleDate && $score !== null) {
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
                        'score' => $score, // Higher is better
                        'body' => implode("\n", $cleanedLines) // URLs removed
                    ];
                }
            }
        }
        return null;
    }
}