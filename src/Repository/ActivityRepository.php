<?php

namespace App\Repository;

use App\Entity\Activity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    public function findActiveByType(?string $type = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.isActive = true')
            ->orderBy('a.createdAt', 'DESC');

        if ($type) {
            $qb->andWhere('a.type = :type')->setParameter('type', $type);
        }

        return $qb->getQuery()->getResult();
    }
}
