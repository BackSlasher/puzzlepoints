<?php

use App\Entity\User;
use App\Entity\GameScore;
use App\Service\GameResultParser;
use Doctrine\ORM\EntityManager;

class InputController
{
    private EntityManager $entityManager;
    private GameResultParser $parser;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->parser = new GameResultParser($entityManager);
    }

    public function show(): void
    {
        session_start();
        $currentUser = null;
        $successMessage = null;

        if (!empty($_SESSION['user_id'])) {
            $currentUser = $this->entityManager->find(User::class, $_SESSION['user_id']);
        }

        // Check for success message from previous submission
        if (!empty($_SESSION['success_message'])) {
            $successMessage = $_SESSION['success_message'];
            unset($_SESSION['success_message']); // Clear it after displaying
        }

        $this->render('input', [
            'user' => $currentUser,
            'success' => $successMessage,
            'game_input' => $successMessage ? '' : ($_POST['game_input'] ?? '') // Clear input if there was a success message
        ]);
    }

    public function submit(): void
    {
        session_start();

        $displayname = trim($_POST['displayname'] ?? '');
        $gameInput = trim($_POST['game_input'] ?? '');

        if (empty($displayname) || empty($gameInput)) {
            $this->render('input', [
                'error' => 'Both display name and game result are required',
                'displayname' => $displayname,
                'game_input' => $gameInput
            ]);
            return;
        }

        // Find or create user
        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['displayname' => $displayname]);

        if (!$user) {
            $user = new User();
            $user->setDisplayname($displayname);
            $this->entityManager->persist($user);
        }

        // Get client info for logging
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        // Parse game result with logging
        $parsedResult = $this->parser->parseGameResult(
            $gameInput,
            $displayname,
            $ipAddress,
            $userAgent
        );

        if (!$parsedResult) {
            $this->render('input', [
                'error' => 'Could not parse game result. Please check the format.',
                'displayname' => $displayname,
                'game_input' => $gameInput,
                'user' => $user
            ]);
            return;
        }

        // Check for duplicate entry
        $existingScore = $this->entityManager->getRepository(GameScore::class)
            ->findOneBy([
                'user' => $user,
                'gameType' => $parsedResult['gameType'],
                'puzzleNumber' => $parsedResult['puzzleNumber']
            ]);

        if ($existingScore) {
            $this->render('input', [
                'error' => 'You have already submitted a result for this game and puzzle number.',
                'displayname' => $displayname,
                'game_input' => $gameInput,
                'user' => $user
            ]);
            return;
        }

        // Create game score
        $gameScore = new GameScore();
        $gameScore->setUser($user)
                  ->setGameType($parsedResult['gameType'])
                  ->setPuzzleNumber($parsedResult['puzzleNumber'])
                  ->setScore($parsedResult['score'])
                  ->setBody($parsedResult['body']);

        $this->entityManager->persist($gameScore);
        $this->entityManager->flush();

        // Set user session
        $_SESSION['user_id'] = $user->getId();

        // Check which button was clicked
        if (isset($_POST['submit_and_continue'])) {
            // Store success message in session
            $gameTypeName = ucwords(str_replace('_', ' ', $parsedResult['gameType']->value));
            $puzzleNumber = $parsedResult['puzzleNumber'];
            $_SESSION['success_message'] = "Successfully submitted $gameTypeName #$puzzleNumber with score {$parsedResult['score']}!";

            // Redirect back to input page
            header("Location: /input");
            exit;
        } else {
            // Default behavior: redirect to game results page
            $gameTypeName = $parsedResult['gameType']->value;
            $puzzleNumber = $parsedResult['puzzleNumber'];
            header("Location: /results/$gameTypeName/$puzzleNumber");
            exit;
        }
    }

    private function render(string $template, array $data = []): void
    {
        extract($data);
        require_once __DIR__ . "/../../views/$template.php";
    }
}