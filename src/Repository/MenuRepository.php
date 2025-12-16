<?php

namespace App\Repository;

use App\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Menu::class);
    }

    public function findByStockDisponible(int $minStock): array {
        return $this->createQueryBuilder('m')
            ->andWhere('m.stock >= :minStock')
            ->setParameter('minStock', $minStock)
            ->getQuery()
            ->getResult();
    }

    public function findByFilters(array $criteria): array {
        $qb = $this->createQueryBuilder('m')
            ->leftJoin('m.theme', 't')
            ->addSelect('t')
            ->leftJoin('m.regime', 'r')
            ->addSelect('r')
            ->leftJoin('m.conditions', 'c')
            ->addSelect('c');
        
        if (!empty($criteria['theme'])) {
            $qb->andWhere('r.id = :theme')->setParameter('theme', $criteria['theme']);
        }
        if (!empty($criteria['regime'])) {
            $qb->andWhere('r.id = :regime')->setParameter('regime', $criteria['regime']);
        }

        if (!empty($criteria['condition'])) {
            $qb->andWhere(':condition MEMBER OF m.conditions')
                ->setParameter('condition', $criteria['condition']);
        }

        if (!empty($criteria['search'])) {
            $qb->andWhere('m.nom LIKE :search OR m.description LIKE :search')
                ->setParameter('search', '%'.$criteria['search'].'%');
        }

        $sort = $criteria['sort'] ?? 'nom';
        $direction = strtoupper($criteria['direction'] ?? 'ASC');
        $direction = $direction === 'DESC' ? 'DESC' : 'ASC';
        $qb->orderBy('m.' . $sort, $direction);

        return $qb->getQuery()->getResult();
    }

}