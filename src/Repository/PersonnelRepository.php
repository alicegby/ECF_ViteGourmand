<?php

namespace App\Repository;

use App\Entity\Personnel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PersonnelRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Personnel::class);
    }

    public function findByStockDisponible(int $minStock): array {
        return $this->createQueryBuilder('ps')
            ->andWhere('ps.stock >= :minStock')
            ->setParameter('minStock', $minStock)
            ->orderBy('ps.stock', 'ASC')
            ->getQuery()
            ->getResult();
    }
}