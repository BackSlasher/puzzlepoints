<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private string $displayname;

    #[ORM\Column(type: 'datetime', name: 'created_at')]
    private \DateTime $createdAt;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: GameScore::class, cascade: ['persist', 'remove'])]
    private Collection $gameScores;

    public function __construct()
    {
        $this->gameScores = new ArrayCollection();
        $this->createdAt = new \DateTime('now', new \DateTimeZone('UTC'));
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDisplayname(): string
    {
        return $this->displayname;
    }

    public function setDisplayname(string $displayname): self
    {
        $this->displayname = $displayname;
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


    /**
     * @return Collection<int, GameScore>
     */
    public function getGameScores(): Collection
    {
        return $this->gameScores;
    }

    public function addGameScore(GameScore $gameScore): self
    {
        if (!$this->gameScores->contains($gameScore)) {
            $this->gameScores->add($gameScore);
            $gameScore->setUser($this);
        }

        return $this;
    }

    public function removeGameScore(GameScore $gameScore): self
    {
        if ($this->gameScores->removeElement($gameScore)) {
            if ($gameScore->getUser() === $this) {
                $gameScore->setUser(null);
            }
        }

        return $this;
    }
}