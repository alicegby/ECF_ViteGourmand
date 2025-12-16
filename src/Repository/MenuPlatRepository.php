<?php

namespace App\Repository;

use App\Entity\MenuPlat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MenuPlatRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuPlat::class);
    }
    public function findByMenuOrdered(int $menuId): array {
        return $this->createQueryBuilder('mp')
            ->andWhere('mp.menu = :menuId')
            ->setParameter('menuId', $menuId)
            ->orderBy('mp.ordre', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function findOneByMenuAndPlat(int $menuId, int $platId): ?MenuPlat {
        return $this->createQueryBuilder('mp')
            ->andWhere('mp.menu = :menuId')
            ->andWhere('mp.plat = :platId')
            ->setParameter('menuId', $menuId)
            ->setParameter('platId', $platId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}