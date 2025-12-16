<?php

namespace App\DataFixtures;

use App\Entity\Theme;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class ThemeFixtures extends Fixture implements FixtureGroupInterface {
    public function load(ObjectManager $manager): void
    {
        $themes = ['Classique', 'Noël', 'Pâques', 'Halloween', 'Nouvel An', 'Mariage', 'Enfants', 'Baptême'];

        foreach ($themes as $key => $libelle) {
            $theme = new Theme();
            $theme->setLibelle($libelle);

            $manager->persist($theme);

            $this->addReference('theme_' . $key, $theme);
        }

        $manager->flush();
    }

    // Groupe pour envoi BDD
    public static function getGroups(): array
    {
        return ['theme'];
    }
}