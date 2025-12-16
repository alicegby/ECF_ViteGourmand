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
}