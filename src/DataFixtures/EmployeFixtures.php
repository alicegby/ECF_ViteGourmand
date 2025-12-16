<?php

namespace App\DataFixtures;

use App\Entity\Employe;
use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EmployeFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
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
        // On récupère le rôle Employe depuis RoleFixtures
        $roleEmploye = $this->getReference('role_Employe', Role::class);

        $employesData = [
            ['nom'=>'Hopper','prenom'=>'Jane','email'=>'jane@mail.fr','telephone'=>'0602020202','password'=>'employe123'],
            ['nom'=>'Byers','prenom'=>'Will','email'=>'will@mail.fr','telephone'=>'0603030303','password'=>'employe456'],
            ['nom'=>'Granger','prenom'=>'Hermione','email'=>'hermione@mail.fr','telephone'=>'0604040404','password'=>'employe789'],
            ['nom'=>'Potter','prenom'=>'Harry','email'=>'harry@mail.fr','telephone'=>'0605050505','password'=>'employe1011'],
        ];

        foreach ($employesData as $i => $data) {
            $employe = new Employe();
            $employe->setNom($data['nom'])
                    ->setPrenom($data['prenom'])
                    ->setEmail($data['email'])
                    ->setTelephone($data['telephone'])
                    ->setActif(true)
                    ->setRole($roleEmploye);

            $hashedPwd = $this->hasher->hashPassword($employe, $data['password']);
            $employe->setMotDePasse($hashedPwd);

            $manager->persist($employe);
            $this->addReference("employe_" . ($i+1), $employe);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['employe'];
    }
}