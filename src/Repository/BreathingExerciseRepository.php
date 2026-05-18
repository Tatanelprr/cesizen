<?php

namespace App\Repository;

use App\Entity\BreathingExercise;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BreathingExerciseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BreathingExercise::class);
    }

    public function findForUser(?User $user): array
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.isDefault = true');

        if ($user) {
            $qb->orWhere('e.user = :user')
               ->setParameter('user', $user);
        }

        return $qb->orderBy('e.isDefault', 'DESC')
                  ->addOrderBy('e.id', 'ASC')
                  ->getQuery()
                  ->getResult();
    }
}
