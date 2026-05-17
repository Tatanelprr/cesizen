<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\ActivityFavorite;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ActivityFavoriteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ActivityFavorite::class);
    }

    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('f')
            ->join('f.activity', 'a')
            ->where('f.user = :user')
            ->andWhere('a.isActive = true')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function findOneByUserAndActivity(User $user, Activity $activity): ?ActivityFavorite
    {
        return $this->findOneBy(['user' => $user, 'activity' => $activity]);
    }
}
