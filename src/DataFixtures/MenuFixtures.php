<?php

namespace App\DataFixtures;

use App\Entity\Menu;
use App\Entity\Condition;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MenuFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface {
    public function getDependencies(): array
    {
        return [
            ThemeFixtures::class,
            RegimeFixtures::class,
            ConditionFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $createMenu = function(
            string $nom,
            string $themeRef,
            string $regimeRef,
            int $nbPersMin,
            int $stock,
            string $prixParPers,
            string $description,
            string $employeRef,
            array $conditionsRef,
            ?string $refKey = null
        ) use ($manager) {
            $menu = new Menu();
            $menu->setNom($nom)
                ->setTheme($this->getReference($themeRef, \App\Entity\Theme::class))
                ->setRegime($this->getReference($regimeRef, \App\Entity\Regime::class))
                ->setNbPersMin($nbPersMin)
                ->setStock($stock)
                ->setPrixParPersonne($prixParPers)
                ->setDescription($description)
                ->setModifiePar($this->getReference($employeRef, \App\Entity\Employe::class))
                ->setDateModif(new \DateTime());
            
            foreach ($conditionsRef as $condRef) {
                $condition = $this->getReference($condRef, \App\Entity\Condition::class);
                $menu->addCondition($condition);
            }
            $manager->persist($menu);

            if ($refKey !== null) {
                $this->addReference($refKey, $menu);
            }
        };

    $createMenu(
        'Saveurs Enchantées',
        'theme_1',
        'regime_0',
        15,
        500,
        '30.90',
        'Festin chaleureux aux saveurs féeriques pour célébrer Noël.',
        'employe_1',
        ['condition_3', 'condition_8', 'condition_12'],
        'menu_noel'
    );
    $createMenu(
        'Le Festin des Cloches',
        'theme_2',
        'regime_0',
        12,
        431,
        '26.90',
        'Menu printanier frais et gourmand.',
        'employe_1',
        ['condition_4', 'condition_9', 'condition_13'],
        'menu_paques'
    );
    $createMenu(
        'Saveurs & Sortilèges',
        'theme_3',
        'regime_0',
        10,
        226,
        '19.90',
        'Délices mystérieux aux notes épicées.',
        'employe_1',
        ['condition_4', 'condition_9', 'condition_14'],
        'menu_halloween'
    );
    $createMenu(
        'Minuit Étincelant',
        'theme_4',
        'regime_0',
        10,
        150,
        '34.90',
        'Menu festif et raffiné pour célébrer.',
        'employe_3',
        ['condition_2', 'condition_8', 'condition_15'],
        'menu_nouvelan'
    );
    $createMenu(
        'Lune de Miel',
        'theme_5',
        'regime_0',
        40,
        300,
        '72.00',
        'Élégance gourmande pour un jour unique.',
        'employe_1',
        ['condition_1', 'condition_7'],
        'menu_mariage'
    );
    $createMenu(
        'Les Aventuriers',
        'theme_6',
        'regime_0',
        6,
        120,
        '15.90',
        'Menu ludique, coloré et facile à aimer.',
        'employe_1',
        ['condition_6', 'condition_11'],
        'menu_enfant'
    );
    $createMenu(
        'Lumière & Tendresse',
        'theme_7',
        'regime_0',
        12,
        180,
        '29.90',
        'Douceurs raffinées pour une fête sereine.',
        'employe_2',
        ['condition_4', 'condition_9'],
        'menu_bapteme'
    );
    $createMenu(
        'Festin Carnivore',
        'theme_0',
        'regime_0',
        7,
        97,
        '20.90',
        'Saveurs généreuses pour amateurs de viande.',
        'employe_1',
        ['condition_5', 'condition_9'],
        'menu_carnivore'
    );
    $createMenu(
        'Sapori d\'Italia',
        'theme_0',
        'regime_0',
        5,
        32,
        '16.90',
        'Voyage gourmand au cœur de l’Italie.',
        'employe_2',
        ['condition_5', 'condition_9'],
        'menu_italie'
    );
    $createMenu(
        'Jardin des Délices',
        'theme_0',
        'regime_1',
        5,
        87,
        '14.90',
        'Fraîcheur végétarienne pleine de couleurs.',
        'employe_4',
        ['condition_6', 'condition_10'],
        'menu_vege'
    );
    $createMenu(
        'Évasion Asiatique',
        'theme_0',
        'regime_0',
        8,
        103,
        '17.90',
        'Saveurs d\'Asie, parfumées et dépaysantes.',
        'employe_3',
        ['condition_5', 'condition_9'],
        'menu_asie'
    );

    $createMenu(
        'Symphonie Maritime',
        'theme_0',
        'regime_0',
        7,
        255,
        '17.90',
        'Menu iodé, frais et élégant.',
        'employe_1',
        ['condition_5', 'condition_9'],
        'menu_mer'
    );

    $createMenu(
        'Palette Végétale',
        'theme_0',
        'regime_2',
        5,
        57,
        '20.90',
        'Créations vegan riches en saveurs.',
        'employe_1',
        ['condition_6', 'condition_10'],
        'menu_vegan'
    );

    $createMenu(
        'Nature Sereine',
        'theme_0',
        'regime_3',
        14,
        421,
        '23.90',
        'Menu sans gluten, sain et raffiné.',
        'employe_2',
        ['condition_5', 'condition_9'],
        'menu_gluten'
    );

    $createMenu(
        'Délicatesse',
        'theme_0',
        'regime_4',
        14,
        276,
        '28.90',
        'Douceurs sans lactose, léger et fin.',
        'employe_2',
        ['condition_5', 'condition_9'],
        'menu_lactose'
    );
    $manager->flush();
    }
    public static function getGroups(): array {
        return ['menus'];
    }
}