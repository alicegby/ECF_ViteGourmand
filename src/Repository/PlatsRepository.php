<?php

namespace App\Repository;

use App\Entity\Plats;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PlatsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plats::class);
    }

    // Exemple de méthode personnalisée : trouver les plats par stock minimum
    public function findByStockDisponible(int $minStock): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.stock >= :minStock')
            ->setParameter('minStock', $minStock)
            ->orderBy('p.stock', 'ASC')
            ->getQuery()
            ->getResult();
    } 

    public function findAllGroupedByCategorie(): array
    {
        $plats = $this->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->addSelect('c')
            ->getQuery()
            ->getResult();

        $grouped = [];
        foreach ($plats as $plat) {
            $catId = $plat->getCategory()?->getId() ?? 0;
            if (!isset($grouped[$catId])) {
                $grouped[$catId] = [];
            }
            $grouped[$catId][] = $plat;
        }

        return $grouped;
    }
}