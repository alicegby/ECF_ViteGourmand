<?php

namespace App\DataFixtures;

use App\Entity\Personnel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PersonnelFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface {
    public function getDependencies(): array
    {
        return [
            CategoryPersonnelFixtures::class,
            EmployeFixtures::class,
        ];
    }
    public function load(ObjectManager $manager): void {
        $createPersonnel = function(
            string $titre,
            string $categoryRef,
            string $description,
            int $stock,
            string $prixHeure,
            string $employeRef,
        ) use ($manager) {
            $personnel = new Personnel();
            $personnel->setTitrePersonnel($titre)
                ->setCategory($this->getReference($categoryRef, \App\Entity\CategoryPersonnel::class))
                ->setDescription($description)
                ->setStock($stock)
                ->setPrixHeure($prixHeure)
                ->setModifiePar($this->getReference($employeRef, \App\Entity\Employe::class))
                ->setDateModif(new \DateTime());

            $manager->persist($personnel);
        };

        $createPersonnel(
            'Chef',
            'cuisine',
            'Professionnel chargé de la préparation, cuisson et finition des plats. Supervise la cuisine et garantit la qualité du menu.',
            4,
            '45.00',
            'employe_4'
        );
        $createPersonnel(
            'Second de cuisine',
            'cuisine',
            'Assiste le chef, prépare les ingrédients, gère les cuissons simples et assure le rythme en cuisine lors des gros services.',
            6,
            '30.00',
            'employe_4'
        );
        $createPersonnel(
            'Commis',
            'cuisine',
            'Aide polyvalent : épluchage, découpe, dressage basique, rangement et nettoyage. Idéal pour renforcer l’équipe.',
            12,
            '20.00',
            'employe_4'
        );
        $createPersonnel(
            'Chef de rang',
            'service',
            'Responsable d’un groupe de tables ou du buffet ; assure un service fluide, élégant et professionnel.',
            10,
            '28.00',
            'employe_4'
        );
        $createPersonnel(
            'Serveur / Serveuse',
            'service',
            'Service à table, en cocktail ou buffet : distribution, desserte, gestion des boissons et aide générale.',
            40,
            '20.00',
            'employe_4'
        );
        $createPersonnel(
            'Runner',
            'service',
            'Assure la liaison rapide entre cuisine et salle : transport de plats, débarrassage, tenue du rythme du service.',
            25,
            '18.00',
            'employe_4'
        );
        $createPersonnel(
            'Barman / Barmaid',
            'bar',
            'Préparation et service des cocktails, bières, softs et alcools. Gestion du bar et contact direct avec les invités.',
            10,
            '25.00',
            'employe_4'
        );
        $createPersonnel(
            'Plongeur',
            'support',
            'Gestion de la vaisselle, nettoyage du matériel, maintien de la propreté du poste pendant toute la prestation.',
            10,
            '17.00',
            'employe_4'
        );
        $createPersonnel(
            'Agent de maintenance',
            'support',
            'Gestion de l’électricité, lumière, branchements, petites réparations et sécurité du matériel.',
            4,
            '30.00',
            'employe_4'
        );
        $createPersonnel(
            'Hôte / Hôtesse',
            'accueil',
            'Accueil des invités, orientation, gestion du vestiaire, distribution de badges, liste d’invités, information générale.',
            20,
            '18.00',
            'employe_4'
        );

        $manager->flush();
    }
    public static function getGroups(): array { return ['personnel']; }
}