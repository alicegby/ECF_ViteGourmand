<?php

namespace App\Repository;

use App\Entity\Avis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AvisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avis::class);
    }

    /**
     * Récupère les avis validés
     */
    public function findValidAvis(): array
    {
        return $this->createQueryBuilder('a')
            ->join('a.statut', 's')
            ->where('s.name = :status')
            ->setParameter('status', 'validé') // ou le libellé exact dans ta table StatutAvis
            ->orderBy('a.dateCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }
}