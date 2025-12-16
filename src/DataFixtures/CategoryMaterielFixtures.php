<?php

namespace App\DataFixtures;

use App\Entity\CategoryMateriel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class CategoryMaterielFixtures extends Fixture implements FixtureGroupInterface {
    public function load(ObjectManager $manager): void {
        $cuisine = new CategoryMateriel();
        $cuisine->setLibelle('Matériel de cuisine');
        $manager->persist($cuisine);
        $this->addReference('cuisine', $cuisine);

        $froid = new CategoryMateriel();
        $froid->setLibelle('Froid');
        $manager->persist($froid);
        $this->addReference('froid', $froid);

        $service = new CategoryMateriel();
        $service->setLibelle('Service');
        $manager->persist($service);
        $this->addReference('service', $service);

        $mobilier = new CategoryMateriel();
        $mobilier->setLibelle('Mobilier');
        $manager->persist($mobilier);
        $this->addReference('mobilier', $mobilier);

        $linge = new CategoryMateriel();
        $linge->setLibelle('Linge');
        $manager->persist($linge);
        $this->addReference('linge', $linge);

        $vaisselle = new CategoryMateriel();
        $vaisselle->setLibelle('Vaisselle');
        $manager->persist($vaisselle);
        $this->addReference('vaisselle', $vaisselle);

        $exterieur = new CategoryMateriel();
        $exterieur->setLibelle('Extérieur');
        $manager->persist($exterieur);
        $this->addReference('exterieur', $exterieur);

        $manager->flush();
    }

    public static function getGroups(): array {
        return['categorymateriel'];
    }
}