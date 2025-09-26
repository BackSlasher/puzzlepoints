<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'game_scores')]
#[ORM\UniqueConstraint(name: 'unique_user_game_puzzle', columns: ['user_id', 'game_type', 'puzzle_number'])]
#[ORM\Index(name: 'idx_user_id', columns: ['user_id'])]
#[ORM\Index(name: 'idx_game_puzzle', columns: ['game_type', 'puzzle_number'])]
#[ORM\Index(name: 'idx_user_game', columns: ['user_id', 'game_type'])]
#[ORM\Index(name: 'idx_created_at', columns: ['created_at'])]
class GameScore
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'gameScores')]
    #[ORM\JoinColumn(name: 'user_id', nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(type: 'string', enumType: GameType::class, name: 'game_type')]
    private GameType $gameType;

    #[ORM\Column(type: 'string', length: 20, name: 'puzzle_number')]
    private string $puzzleNumber;

    #[ORM\Column(type: 'integer')]
    private int $score;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $body = null;

    #[ORM\Column(type: 'datetime', name: 'created_at')]
    private \DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime('now', new \DateTimeZone('UTC'));
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getGameType(): GameType
    {
        return $this->gameType;
    }

    public function setGameType(GameType $gameType): self
    {
        $this->gameType = $gameType;
        return $this;
    }

    public function getPuzzleNumber(): string
    {
        return $this->puzzleNumber;
    }

    public function setPuzzleNumber(string $puzzleNumber): self
    {
        $this->puzzleNumber = $puzzleNumber;
        return $this;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;
        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}