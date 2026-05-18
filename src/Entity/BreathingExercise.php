<?php

namespace App\Entity;

use App\Repository\BreathingExerciseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BreathingExerciseRepository::class)]
class BreathingExercise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $inhale = null;

    #[ORM\Column]
    private int $hold = 0;

    #[ORM\Column]
    private ?int $exhale = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    #[ORM\Column]
    private bool $isDefault = false;

    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }
    public function getInhale(): ?int { return $this->inhale; }
    public function setInhale(int $inhale): static { $this->inhale = $inhale; return $this; }
    public function getHold(): int { return $this->hold; }
    public function setHold(int $hold): static { $this->hold = $hold; return $this; }
    public function getExhale(): ?int { return $this->exhale; }
    public function setExhale(int $exhale): static { $this->exhale = $exhale; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }
    public function isDefault(): bool { return $this->isDefault; }
    public function setIsDefault(bool $isDefault): static { $this->isDefault = $isDefault; return $this; }
}
