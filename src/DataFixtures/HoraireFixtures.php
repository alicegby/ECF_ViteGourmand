<?php

namespace App\DataFixtures;

use App\Entity\Horaire;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class HoraireFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface {
    public function getDependencies(): array
    {
        return [
            EmployeFixtures::class
        ];
    }

    public function load(ObjectManager $manager): void {
        $createHoraire = function(
            string $jour,
            string $ouverture,
            string $fermeture,
            string $employeRef,
        ) use ($manager) {
            $horaire = new Horaire();
            $horaire->setJour($jour)
                ->setHeureOuverture(new \DateTime($ouverture))
                ->setHeureFermeture(new \DateTime($fermeture))
                ->setModifiePar($this->getReference($employeRef, \App\Entity\Employe::class))
                ->setDateModif(new \DateTime());

            $manager->persist($horaire);
        };
        $jours = [
            ['jour' => 'Lundi',    'ouverture' => '14:00', 'fermeture' => '18:00', 'employe' => 'employe_1'],
            ['jour' => 'Mardi',    'ouverture' => '10:00', 'fermeture' => '18:00', 'employe' => 'employe_2'],
            ['jour' => 'Mercredi', 'ouverture' => '10:00', 'fermeture' => '18:00', 'employe' => 'employe_3'],
            ['jour' => 'Jeudi',    'ouverture' => '10:00', 'fermeture' => '18:00', 'employe' => 'employe_4'],
            ['jour' => 'Vendredi', 'ouverture' => '10:00', 'fermeture' => '20:00', 'employe' => 'employe_1'],
            ['jour' => 'Samedi',   'ouverture' => '10:00', 'fermeture' => '20:00', 'employe' => 'employe_2'],
            ['jour' => 'Dimanche', 'ouverture' => '10:00', 'fermeture' => '20:00', 'employe' => 'employe_3'],
        ];

        // Boucle pour créer chaque horaire
        foreach ($jours as $data) {
            $createHoraire($data['jour'], $data['ouverture'], $data['fermeture'], $data['employe']);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['horaires'];
    }
}