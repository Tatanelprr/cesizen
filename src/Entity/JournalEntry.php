<?php

namespace App\Entity;

use App\Repository\JournalEntryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: JournalEntryRepository::class)]
class JournalEntry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'journalEntries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'journalEntries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Emotion $emotion = null;

    #[ORM\Column(type: 'smallint')]
    #[Assert\Range(min: 1, max: 10)]
    private ?int $intensite = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notePerso = null;

    #[ORM\Column]
    private \DateTimeImmutable $dateCreation;

    public function __construct()
    {
        $this->dateCreation = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }

    public function getEmotion(): ?Emotion { return $this->emotion; }
    public function setEmotion(?Emotion $emotion): static { $this->emotion = $emotion; return $this; }

    public function getIntensite(): ?int { return $this->intensite; }
    public function setIntensite(int $intensite): static { $this->intensite = $intensite; return $this; }

    public function getNotePerso(): ?string { return $this->notePerso; }
    public function setNotePerso(?string $notePerso): static { $this->notePerso = $notePerso; return $this; }

    public function getDateCreation(): \DateTimeImmutable { return $this->dateCreation; }
    public function setDateCreation(\DateTimeImmutable $dateCreation): static { $this->dateCreation = $dateCreation; return $this; }
}
