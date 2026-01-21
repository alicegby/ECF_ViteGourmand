<?php

namespace App\Repository;

use App\Entity\Fromages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FromagesRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fromages::class);
    } 
    public function findByStockDisponible(int $minStock): array {
        return $this->createQueryBuilder('f')
            ->andWhere('f.stock >= :minStock')
            ->setParameter('minStock', $minStock)
            ->orderBy('f.stock', 'ASC')
            ->getQuery()
            ->getResult();
    }
}