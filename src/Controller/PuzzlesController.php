<?php

use App\Entity\GameScore;
use App\Entity\GameType;
use Doctrine\ORM\EntityManager;

class PuzzlesController
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function index(): void
    {
        // Get all unique puzzle type/number combinations with submission counts
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('gs.gameType', 'gs.puzzleNumber', 'COUNT(gs.id) as submissionCount', 'MAX(gs.createdAt) as latestSubmission')
            ->from(GameScore::class, 'gs')
            ->groupBy('gs.gameType', 'gs.puzzleNumber')
            ->orderBy('latestSubmission', 'DESC');

        $puzzleStats = $queryBuilder->getQuery()->getResult();

        $this->render('puzzles', [
            'puzzleStats' => $puzzleStats,
            'gameTypes' => GameType::cases()
        ]);
    }

    private function render(string $template, array $data = []): void
    {
        extract($data);
        require_once __DIR__ . "/../../views/$template.php";
    }
}