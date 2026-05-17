<?php

namespace App\Repository;

use App\Entity\DiagnosticThreshold;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DiagnosticThresholdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DiagnosticThreshold::class);
    }

    public function findForScore(int $score): ?DiagnosticThreshold
    {
        return $this->createQueryBuilder('t')
            ->where('t.scoreMin <= :score AND t.scoreMax >= :score')
            ->setParameter('score', $score)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
