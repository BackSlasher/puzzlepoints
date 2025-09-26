<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'puzzle_inputs')]
#[ORM\Index(name: 'idx_puzzle_inputs_created_at', columns: ['created_at'])]
#[ORM\Index(name: 'idx_puzzle_inputs_parsed', columns: ['parsed_successfully'])]
#[ORM\Index(name: 'idx_puzzle_inputs_game_type', columns: ['detected_game_type'])]
class PuzzleInput
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'text')]
    private string $rawInput;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $submittedByDisplayname = null;

    #[ORM\Column(type: 'boolean', name: 'parsed_successfully')]
    private bool $parsedSuccessfully;

    #[ORM\Column(type: 'string', enumType: GameType::class, nullable: true, name: 'detected_game_type')]
    private ?GameType $detectedGameType = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true, name: 'detected_puzzle_number')]
    private ?string $detectedPuzzleNumber = null;

    #[ORM\Column(type: 'integer', nullable: true, name: 'detected_score')]
    private ?int $detectedScore = null;

    #[ORM\Column(type: 'text', nullable: true, name: 'parsing_error')]
    private ?string $parsingError = null;

    #[ORM\Column(type: 'string', length: 45, nullable: true, name: 'ip_address')]
    private ?string $ipAddress = null;

    #[ORM\Column(type: 'text', nullable: true, name: 'user_agent')]
    private ?string $userAgent = null;

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

    public function getRawInput(): string
    {
        return $this->rawInput;
    }

    public function setRawInput(string $rawInput): self
    {
        $this->rawInput = $rawInput;
        return $this;
    }

    public function getSubmittedByDisplayname(): ?string
    {
        return $this->submittedByDisplayname;
    }

    public function setSubmittedByDisplayname(?string $submittedByDisplayname): self
    {
        $this->submittedByDisplayname = $submittedByDisplayname;
        return $this;
    }

    public function isParsedSuccessfully(): bool
    {
        return $this->parsedSuccessfully;
    }

    public function setParsedSuccessfully(bool $parsedSuccessfully): self
    {
        $this->parsedSuccessfully = $parsedSuccessfully;
        return $this;
    }

    public function getDetectedGameType(): ?GameType
    {
        return $this->detectedGameType;
    }

    public function setDetectedGameType(?GameType $detectedGameType): self
    {
        $this->detectedGameType = $detectedGameType;
        return $this;
    }

    public function getDetectedPuzzleNumber(): ?string
    {
        return $this->detectedPuzzleNumber;
    }

    public function setDetectedPuzzleNumber(?string $detectedPuzzleNumber): self
    {
        $this->detectedPuzzleNumber = $detectedPuzzleNumber;
        return $this;
    }

    public function getDetectedScore(): ?int
    {
        return $this->detectedScore;
    }

    public function setDetectedScore(?int $detectedScore): self
    {
        $this->detectedScore = $detectedScore;
        return $this;
    }

    public function getParsingError(): ?string
    {
        return $this->parsingError;
    }

    public function setParsingError(?string $parsingError): self
    {
        $this->parsingError = $parsingError;
        return $this;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(?string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): self
    {
        $this->userAgent = $userAgent;
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