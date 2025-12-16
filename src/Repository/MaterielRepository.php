<?php

namespace App\Repository;

use App\Entity\Materiel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MaterielRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Materiel::class);
    }

    public function findByStockDisponible(int $minStock): array {
        return $this->createQueryBuilder('mt')
            ->andWhere('mt.stock >= :minStock')
            ->setParameter('minStock', $minStock)
            ->orderBy('mt.stock', 'ASC')
            ->getQuery()
            ->getResult();
    }
}