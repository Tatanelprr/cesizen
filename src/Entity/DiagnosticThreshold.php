<?php

namespace App\Entity;

use App\Repository\DiagnosticThresholdRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiagnosticThresholdRepository::class)]
class DiagnosticThreshold
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $scoreMin = null;

    #[ORM\Column]
    private ?int $scoreMax = null;

    #[ORM\Column(length: 100)]
    private ?string $niveau = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column(type: 'text')]
    private ?string $conseil = null;

    #[ORM\Column(length: 7)]
    private string $codeColor = '#00C853';

    public function getId(): ?int { return $this->id; }
    public function getScoreMin(): ?int { return $this->scoreMin; }
    public function setScoreMin(int $scoreMin): static { $this->scoreMin = $scoreMin; return $this; }
    public function getScoreMax(): ?int { return $this->scoreMax; }
    public function setScoreMax(int $scoreMax): static { $this->scoreMax = $scoreMax; return $this; }
    public function getNiveau(): ?string { return $this->niveau; }
    public function setNiveau(string $niveau): static { $this->niveau = $niveau; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): static { $this->description = $description; return $this; }
    public function getConseil(): ?string { return $this->conseil; }
    public function setConseil(string $conseil): static { $this->conseil = $conseil; return $this; }
    public function getCodeColor(): string { return $this->codeColor; }
    public function setCodeColor(string $codeColor): static { $this->codeColor = $codeColor; return $this; }
}
