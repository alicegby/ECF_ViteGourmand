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
 
    /* Récupérer les avis validés */

    public function findValidAvis(?int $limit = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->join('a.statut', 's')
            ->where('s.libelle = :status')
            ->setParameter('status', 'Validé')
            ->orderBy('a.dateCreation', 'DESC');

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }
}