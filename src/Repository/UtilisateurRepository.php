<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }
    public function findByEmail(string $email): ?Utilisateur
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function findActiveUsers(): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.actif = :actif')
            ->setParameter('actif', true)
            ->orderBy('u.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
    public function findByFilters(?string $keyword, ?\DateTimeInterface $lastOrderDate = null): array
    {
        $qb = $this->createQueryBuilder('u');

        // Filtre par mot-clé (nom, prénom ou email)
        if ($keyword) {
            $qb->andWhere('u.nom LIKE :keyword OR u.prenom LIKE :keyword OR u.email LIKE :keyword')
            ->setParameter('keyword', '%'.$keyword.'%');
        }

        // Filtre par date de dernière commande
        if ($lastOrderDate) {
            // Supposons que tu as une relation commandes (u.commandes)
            $qb->join('u.commandes', 'c')
            ->andWhere('c.dateCommande >= :lastOrderDate')
            ->setParameter('lastOrderDate', $lastOrderDate);
        }

        return $qb->orderBy('u.nom', 'ASC')
                ->getQuery()
                ->getResult();
    }
}