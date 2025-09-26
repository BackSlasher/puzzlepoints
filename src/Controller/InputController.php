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
        $this->parser = new GameResultParser();
    }

    public function show(): void
    {
        session_start();
        $currentUser = null;

        if (!empty($_SESSION['user_id'])) {
            $currentUser = $this->entityManager->find(User::class, $_SESSION['user_id']);
        }

        $this->render('input', ['user' => $currentUser]);
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

        // Parse game result
        $parsedResult = $this->parser->parseGameResult($gameInput);

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

        // Redirect to game results page
        $gameTypeName = $parsedResult['gameType']->value;
        $puzzleNumber = $parsedResult['puzzleNumber'];
        header("Location: /results/$gameTypeName/$puzzleNumber");
        exit;
    }

    private function render(string $template, array $data = []): void
    {
        extract($data);
        require_once __DIR__ . "/../../views/$template.php";
    }
}