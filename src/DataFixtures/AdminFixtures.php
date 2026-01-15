<?php

namespace App\DataFixtures;

use App\Entity\Employe;
use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class AdminFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{

    public function getDependencies(): array
    {
        return [
            RoleFixtures::class,
        ];
    }

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // On récupère le rôle Admin depuis RoleFixtures
        $roleAdmin = $this->getReference('role_Admin', Role::class);

        $adminsData = [
            ['nom' => 'Dubois', 'prenom' => 'José', 'email' => 'jose@mail.fr', 'telephone' => '0600000000', 'password' => 'admin123'],
            ['nom' => 'Dubois', 'prenom' => 'Julie', 'email' => 'julie@mail.fr', 'telephone' => '0601010101', 'password' => 'admin456'],
        ];

        foreach ($adminsData as $i => $data) {
            $admin = new Employe();
            $admin->setNom($data['nom'])
                  ->setPrenom($data['prenom'])
                  ->setEmail($data['email'])
                  ->setTelephone($data['telephone'])
                  ->setActif(true)
                  ->setRole($roleAdmin);

            $hashedPwd = $this->hasher->hashPassword($admin, $data['password']);
            $admin->setPassword($hashedPwd);

            $manager->persist($admin);
            $this->addReference("admin_$i", $admin);
        }

        $manager->flush();
    }
 
    public static function getGroups(): array
    {
        return ['admin'];
    }
}