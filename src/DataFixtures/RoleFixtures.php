<?php

namespace App\DataFixtures;

use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class RoleFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $roleAdmin = new Role();
        $roleAdmin->setLibelle('Admin');
        $manager->persist($roleAdmin);
        $this->addReference('role_Admin', $roleAdmin);

        $roleEmploye = new Role();
        $roleEmploye->setLibelle('Employe');
        $manager->persist($roleEmploye);
        $this->addReference('role_Employe', $roleEmploye);

        $manager->flush();
    }

    public static function getGroups(): array { return ['role']; }
}