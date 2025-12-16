<?php

namespace App\DataFixtures;

use App\Entity\StatutAvis;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class StatutAvisFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['statutsavis'];
    }

    public function load(ObjectManager $manager): void
    {
        $statuts = [
            'En attente',
            'Validé',
            'Refusé'
        ];

        foreach ($statuts as $libelle) {
            $statut = new StatutAvis();
            $statut->setLibelle($libelle);
            $manager->persist($statut);
        }

        $manager->flush();
    }
}