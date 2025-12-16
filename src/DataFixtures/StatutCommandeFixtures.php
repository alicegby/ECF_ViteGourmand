<?php

namespace App\DataFixtures;

use App\Entity\StatutCommande;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class StatutCommandeFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['statutscommande'];
    }

    public function load(ObjectManager $manager): void
    {
        $statuts = [
            'En préparation',
            'En livraison',
            'Livrée',
            'Annulée'
        ];

        foreach ($statuts as $libelle) {
            $statut = new StatutCommande();
            $statut->setLibelle($libelle);
            $manager->persist($statut);
        }

        $manager->flush();
    }
}