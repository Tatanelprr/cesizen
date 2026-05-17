<?php

namespace App\Entity;

use App\Repository\ActivityFavoriteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActivityFavoriteRepository::class)]
#[ORM\UniqueConstraint(fields: ['user', 'activity'])]
class ActivityFavorite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'favorites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Activity $activity = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(User $user): static { $this->user = $user; return $this; }
    public function getActivity(): ?Activity { return $this->activity; }
    public function setActivity(Activity $activity): static { $this->activity = $activity; return $this; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
