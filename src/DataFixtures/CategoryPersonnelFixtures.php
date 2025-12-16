<?php

namespace App\DataFixtures;

use App\Entity\CategoryPersonnel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\Persistence\ObjectManager;

class CategoryPersonnelFixtures extends Fixture implements FixtureGroupInterface {
    public function load(ObjectManager $manager): void {
        $cuisine = new CategoryPersonnel();
        $cuisine->setLibelle('Cuisine');
        $manager->persist($cuisine);
        $this->addReference('cuisine', $cuisine);

        $service = new CategoryPersonnel();
        $service->setLibelle('Service');
        $manager->persist($service);
        $this->addReference('service', $service);

        $bar = new CategoryPersonnel();
        $bar->setLibelle('Bar');
        $manager->persist($bar);
        $this->addReference('bar', $bar);

        $support = new CategoryPersonnel();
        $support->setLibelle('Support');
        $manager->persist($support);
        $this->addReference('support', $support);

        $accueil = new CategoryPersonnel();
        $accueil->setLibelle('Accueil');
        $manager->persist($accueil);
        $this->addReference('accueil', $accueil);

        $manager->flush();
    }

    public static function getGroups(): array{
        return['categorypersonnel'];
    }

}