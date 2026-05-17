<?php

namespace App\Repository;

use App\Entity\JournalEntry;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class JournalEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JournalEntry::class);
    }

    public function findByUserAndPeriod(User $user, \DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        return $this->createQueryBuilder('j')
            ->join('j.emotion', 'e')
            ->join('e.category', 'c')
            ->where('j.user = :user')
            ->andWhere('j.dateCreation BETWEEN :from AND :to')
            ->setParameter('user', $user)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('j.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
