<?php

namespace App\DataFixtures;

use App\Entity\CategoryDrink;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class CategoryDrinkFixtures extends Fixture implements FixtureGroupInterface {
    public function load(ObjectManager $manager): void {
        $vinRouge = new CategoryDrink();
        $vinRouge->setLibelle('Vin Rouge');
        $manager->persist($vinRouge);
        $this->addReference('vinrouge', $vinRouge);

        $vinBlanc = new CategoryDrink();
        $vinBlanc->setLibelle('Vin Blanc');
        $manager->persist($vinBlanc);
        $this->addReference('vinblanc', $vinBlanc);

        $rose = new CategoryDrink();
        $rose->setLibelle('Rosé');
        $manager->persist($rose);
        $this->addReference('rose', $rose);

        $champagne = new CategoryDrink();
        $champagne->setLibelle('Champagne');
        $manager->persist($champagne);
        $this->addReference('champagne', $champagne);

        $soda = new CategoryDrink();
        $soda->setLibelle('Soda');
        $manager->persist($soda);
        $this->addReference('soda', $soda);

        $eauGazeuse = new CategoryDrink();
        $eauGazeuse->setLibelle('Eau Gazeuse');
        $manager->persist($eauGazeuse);
        $this->addReference('eaugaz', $eauGazeuse);

        $eauPlate = new CategoryDrink();
        $eauPlate->setLibelle('Eau Plate');
        $manager->persist($eauPlate);
        $this->addReference('eauplate', $eauPlate);

        $the = new CategoryDrink();
        $the->setLibelle('Thé');
        $manager->persist($the);
        $this->addReference('the', $the);

        $cafe = new CategoryDrink();
        $cafe->setLibelle('Café');
        $manager->persist($cafe);
        $this->addReference('cafe', $cafe);

        $biere = new CategoryDrink();
        $biere->setLibelle('Bière');
        $manager->persist($biere);
        $this->addReference('biere', $biere);

        $infusion = new CategoryDrink();
        $infusion->setLibelle('Infusion');
        $manager->persist($infusion);
        $this->addReference('infusion', $infusion);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return['categorydrink'];
    }
}