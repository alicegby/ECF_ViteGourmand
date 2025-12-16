<?php

namespace App\DataFixtures;

use App\Entity\CategoryCheese;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class CategoryCheeseFixtures extends Fixture implements FixtureGroupInterface {
    public function load(ObjectManager $manager): void {
        $vache = new CategoryCheese();
        $vache->setLibelle('Lait de vache');
        $manager->persist($vache);
        $this->addReference('vache', $vache);

        $brebis = new CategoryCheese();
        $brebis->setLibelle('Lait de brebis');
        $manager->persist($brebis);
        $this->addReference('brebis', $brebis);

        $chevre = new CategoryCheese();
        $chevre->setLibelle('Lait de chèvre');
        $manager->persist($chevre);
        $this->addReference('chevre', $chevre);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return['categorycheese'];
    }
    
}