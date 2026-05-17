<?php

namespace App\Entity;

use App\Repository\EmotionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmotionRepository::class)]
class Emotion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $libelle = null;

    #[ORM\Column(length: 7)]
    private ?string $codeColor = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $icon = null;

    #[ORM\Column]
    private bool $isActive = true;

    #[ORM\ManyToOne(inversedBy: 'emotions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?EmotionCategory $category = null;

    #[ORM\OneToMany(mappedBy: 'emotion', targetEntity: JournalEntry::class)]
    private Collection $journalEntries;

    public function __construct()
    {
        $this->journalEntries = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getLibelle(): ?string { return $this->libelle; }
    public function setLibelle(string $libelle): static { $this->libelle = $libelle; return $this; }

    public function getCodeColor(): ?string { return $this->codeColor; }
    public function setCodeColor(string $codeColor): static { $this->codeColor = $codeColor; return $this; }

    public function getIcon(): ?string { return $this->icon; }
    public function setIcon(?string $icon): static { $this->icon = $icon; return $this; }

    public function isActive(): bool { return $this->isActive; }
    public function setIsActive(bool $isActive): static { $this->isActive = $isActive; return $this; }

    public function getCategory(): ?EmotionCategory { return $this->category; }
    public function setCategory(?EmotionCategory $category): static { $this->category = $category; return $this; }

    public function getJournalEntries(): Collection { return $this->journalEntries; }
}
