<?php

namespace App\Repository;

use App\Entity\Boissons;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BoissonsRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Boissons::class);
    }

    public function findByStockDisponible(int $minStock): array {
        return $this->createQueryBuilder('b')
            ->andWhere('b.stock >= :minStock')
            ->setParameter('minStock', $minStock)
            ->orderBy('b.stock', 'ASC')
            ->getQuery()
            ->getResult();
    }
}