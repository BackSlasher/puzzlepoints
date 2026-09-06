<?php

use App\Entity\User;
use App\Entity\GameScore;
use App\Entity\GameType;
use Doctrine\ORM\EntityManager;

class HomeController
{
    /** How far back a submission counts as "today". Avoids timezone guessing. */
    private const RECENT_WINDOW = 'PT36H';

    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function index(): void
    {
        session_start();

        $user = null;
        if (!empty($_SESSION['user_id'])) {
            $user = $this->entityManager->find(User::class, $_SESSION['user_id']);
        }

        if (!$user) {
            // No identity yet: submitting a result is how a user gets one.
            header('Location: /input');
            exit;
        }

        $since = (new \DateTime('now', new \DateTimeZone('UTC')))->sub(new \DateInterval(self::RECENT_WINDOW));

        // The user's recent submissions, newest first, so the first one per game wins.
        $recentScores = $this->entityManager->createQueryBuilder()
            ->select('gs')
            ->from(GameScore::class, 'gs')
            ->where('gs.user = :user')
            ->andWhere('gs.createdAt >= :since')
            ->setParameter('user', $user)
            ->setParameter('since', $since)
            ->orderBy('gs.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        /** @var array<string, GameScore> $latestPerGame */
        $latestPerGame = [];
        foreach ($recentScores as $score) {
            $key = $score->getGameType()->value;
            if (!isset($latestPerGame[$key])) {
                $latestPerGame[$key] = $score;
            }
        }

        $played = [];
        foreach ($latestPerGame as $mine) {
            // Everyone's scores on this exact puzzle, best first.
            $all = $this->entityManager->createQueryBuilder()
                ->select('gs', 'u')
                ->from(GameScore::class, 'gs')
                ->join('gs.user', 'u')
                ->where('gs.gameType = :gameType')
                ->andWhere('gs.puzzleNumber = :puzzleNumber')
                ->setParameter('gameType', $mine->getGameType())
                ->setParameter('puzzleNumber', $mine->getPuzzleNumber())
                ->orderBy('gs.score', 'DESC')
                ->getQuery()
                ->getResult();

            $better = 0;
            $tied = 0;
            foreach ($all as $other) {
                if ($other->getScore() > $mine->getScore()) {
                    $better++;
                } elseif ($other->getScore() === $mine->getScore() && $other->getId() !== $mine->getId()) {
                    $tied++;
                }
            }

            $played[] = [
                'score' => $mine,
                'rank' => $better + 1,
                'tied' => $tied > 0,
                'total' => count($all),
                'others' => count($all) - 1,
                'leader' => $all[0] ?? null,
            ];
        }

        // Most recent submission first, same order as the query.
        usort($played, fn($a, $b) => $b['score']->getCreatedAt() <=> $a['score']->getCreatedAt());

        $stillToPlay = array_values(array_filter(
            GameType::cases(),
            fn(GameType $type) => $type !== GameType::OTHER && !isset($latestPerGame[$type->value])
        ));

        $this->render('summary', [
            'user' => $user,
            'played' => $played,
            'stillToPlay' => $stillToPlay,
        ]);
    }

    private function render(string $template, array $data = []): void
    {
        extract($data);
        require_once __DIR__ . "/../../views/$template.php";
    }
}
