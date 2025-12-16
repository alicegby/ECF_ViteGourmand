<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $usersData = [
            [
                'nom' => 'Weasley',
                'prenom' => 'Ron',
                'email' => 'ron.weasley@exemple.com',
                'telephone' => '0606060606',
                'adressePostale' => '12, rue des Canons de Chudley',
                'codePostal' => '33000',
                'ville' => 'Bordeaux',
                'mdp' => 'mdp123',
            ],
            [
                'nom' => 'Targaryen',
                'prenom' => 'Daenerys',
                'email' => 'dany.dragons@exemple.com',
                'telephone' => '0607070707',
                'adressePostale' => '1, rue des Dragons',
                'codePostal' => '33000',
                'ville' => 'Bordeaux',
                'mdp' => 'mdp456',
            ],
            [
                'nom' => 'Snow',
                'prenom' => 'Jon',
                'email' => 'jon.snow@mail.com',
                'telephone' => '0608080808',
                'adressePostale' => '2, rue du Mur',
                'codePostal' => '33560',
                'ville' => 'Sainte-Eulalie',
                'mdp' => 'mdp789',
            ],
            [
                'nom' => 'Bing',
                'prenom' => 'Chandler',
                'email' => 'chanandler@mail.com',
                'telephone' => '0609090909',
                'adressePostale' => '134, rue de New York',
                'codePostal' => '33000',
                'ville' => 'Bordeaux',
                'mdp' => 'mdp456',
            ],
            [
                'nom' => 'Harrington',
                'prenom' => 'Steve',
                'email' => 'stevelebghawkings@exemple.com',
                'telephone' => '0610101010',
                'adressePostale' => '42, rue de Hawkings',
                'codePostal' => '33600',
                'ville' => 'Pessac',
                'mdp' => 'mdp456',
            ],
        ];

        foreach ($usersData as $data) {
            $user = new Utilisateur();
            $user->setNom($data['nom']);
            $user->setPrenom($data['prenom']);
            $user->setEmail($data['email']);
            $user->setTelephone($data['telephone']);
            $user->setAdressePostale($data['adressePostale']);
            $user->setCodePostal($data['codePostal']);
            $user->setVille($data['ville']);
            $user->setDateCreation(new \DateTime());
            $user->setMotDePasse($this->hasher->hashPassword($user, $data['mdp']));

            $manager->persist($user);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['user'];
    }
}