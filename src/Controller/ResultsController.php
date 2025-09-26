<?php

use App\Entity\User;
use App\Entity\GameScore;
use App\Entity\GameType;
use Doctrine\ORM\EntityManager;

class ResultsController
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function index(): void
    {
        $gameFilter = $_GET['game'] ?? '';
        $userFilter = $_GET['user'] ?? '';
        $puzzleFilter = $_GET['puzzle'] ?? '';

        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('gs', 'u')
            ->from(GameScore::class, 'gs')
            ->join('gs.user', 'u')
            ->orderBy('gs.createdAt', 'DESC');

        if (!empty($gameFilter)) {
            $queryBuilder->andWhere('gs.gameType = :gameType')
                        ->setParameter('gameType', GameType::from($gameFilter));
        }

        if (!empty($userFilter)) {
            $queryBuilder->andWhere('u.id = :userId')
                        ->setParameter('userId', (int)$userFilter);
        }

        if (!empty($puzzleFilter)) {
            $queryBuilder->andWhere('gs.puzzleNumber = :puzzleNumber')
                        ->setParameter('puzzleNumber', $puzzleFilter);
        }

        $results = $queryBuilder->getQuery()->getResult();

        // Get all users for filter dropdown
        $users = $this->entityManager->getRepository(User::class)->findAll();

        $this->render('results', [
            'results' => $results,
            'users' => $users,
            'gameFilter' => $gameFilter,
            'userFilter' => $userFilter,
            'puzzleFilter' => $puzzleFilter,
            'gameTypes' => GameType::cases()
        ]);
    }

    public function gameResults(string $gameTypeName, string $puzzleNumber): void
    {
        try {
            $gameType = GameType::from($gameTypeName);
        } catch (ValueError $e) {
            http_response_code(404);
            echo "Invalid game type";
            return;
        }

        // Get all results for this game and puzzle, ordered by score
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('gs', 'u')
            ->from(GameScore::class, 'gs')
            ->join('gs.user', 'u')
            ->where('gs.gameType = :gameType')
            ->andWhere('gs.puzzleNumber = :puzzleNumber')
            ->setParameter('gameType', $gameType)
            ->setParameter('puzzleNumber', $puzzleNumber);

        // Order by score (ascending for most games where lower is better,
        // except Spelling Bee where higher is better)
        if ($gameType === GameType::SPELLING_BEE) {
            $queryBuilder->orderBy('gs.score', 'DESC');
        } else {
            $queryBuilder->orderBy('gs.score', 'ASC');
        }

        $results = $queryBuilder->getQuery()->getResult();

        $this->render('game_results', [
            'results' => $results,
            'gameType' => $gameType,
            'puzzleNumber' => $puzzleNumber
        ]);
    }

    private function render(string $template, array $data = []): void
    {
        extract($data);
        require_once __DIR__ . "/../../views/$template.php";
    }
}