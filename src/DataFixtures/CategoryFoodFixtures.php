<?php

namespace App\DataFixtures;

use App\Entity\CategoryFood;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class CategoryFoodFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        // Création des catégories
        $entree = new CategoryFood();
        $entree->setLibelle('Entrée');
        $manager->persist($entree);
        $this->addReference('entree', $entree);

        $plat = new CategoryFood();
        $plat->setLibelle('Plat');
        $manager->persist($plat);
        $this->addReference('plat', $plat);

        $dessert = new CategoryFood();
        $dessert->setLibelle('Dessert');
        $manager->persist($dessert);
        $this->addReference('dessert', $dessert);

        // Enregistrement en base
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['categoryfood'];
    }
}