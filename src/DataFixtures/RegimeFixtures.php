<?php

namespace App\DataFixtures;

use App\Entity\Regime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class RegimeFixtures extends Fixture implements FixtureGroupInterface {
    public function load(ObjectManager $manager): void {
        $regimes = ['Classique', 'Végétarien', 'Vegan', 'Sans gluten', 'Sans lactose'];

        foreach ($regimes as $key => $libelle) {
            $regime = new Regime();
            $regime->setLibelle($libelle);

            $manager->persist($regime);

            $this->addReference('regime_' . $key, $regime);
        }

        $manager->flush(); 
    }

    // Groupe pour envoi BDD
    public static function getGroups(): array
    {
        return ['regime'];
    }
}