<?php

namespace App\Repository;

use App\Entity\DiagnosticEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DiagnosticEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DiagnosticEvent::class);
    }

    public function findActive(): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.isActive = true')
            ->orderBy('e.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
