<?php

namespace App\Entity;

use App\Repository\EmotionCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmotionCategoryRepository::class)]
class EmotionCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $libelle = null;

    #[ORM\Column(length: 7)]
    private ?string $codeColor = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Emotion::class, orphanRemoval: true)]
    private Collection $emotions;

    public function __construct()
    {
        $this->emotions = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getLibelle(): ?string { return $this->libelle; }
    public function setLibelle(string $libelle): static { $this->libelle = $libelle; return $this; }

    public function getCodeColor(): ?string { return $this->codeColor; }
    public function setCodeColor(string $codeColor): static { $this->codeColor = $codeColor; return $this; }

    public function getEmotions(): Collection { return $this->emotions; }
}
