<?php

namespace App\DataFixtures;

use App\Entity\ImageMenu;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ImageMenuFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface {
    public function getDependencies(): array
    {
        return [MenuFixtures::class];
    }
    public function load(ObjectManager $manager): void {
        $createImage = function(
            string $menuRef,
            string $url,
            string $alt,
            int $ordre,
            bool $principale,
        ) use ($manager) {
            $img = new ImageMenu();
            $img->setMenu($this->getReference($menuRef, \App\Entity\Menu::class))
                ->setUrl($url)
                ->setAltText($alt)
                ->setOrdre($ordre)
                ->setEstPrincipale($principale);

            $manager->persist($img);
        };

        // Menu 1 - Saveurs Enchantées "menu_noel"
        $createImage('menu_noel', 'Visuels/Menu/Saveurs Echantées 1.png', 'Entrée - Menu Saveurs Echantées - Vite & Gourmand', 1, true);
        $createImage('menu_noel', 'Visuels/Menu/Saveurs Echantées 2.png', 'Plat - Menu Saveurs Echantées - Vite & Gourmand', 2, false);
        $createImage('menu_noel', 'Visuels/Menu/Saveurs Echantées 3.png', 'Dessert - Menu Saveurs Echantées - Vite & Gourmand', 3, false);

        // Menu 2 - Le Festin des Cloches 'menu_paques'
        $createImage('menu_paques', 'Visuels/Menu/Le Festin des Cloches 1.png', 'Entrée - Menu Le Festin des Cloches - Vite & Gourmand', 1, true);
        $createImage('menu_paques', 'Visuels/Menu/Le Festin des Cloches 2.png', 'Plat - Menu Le Festin des Cloches - Vite & Gourmand', 2, false);
        $createImage('menu_paques', 'Visuels/Menu/Le Festin des Cloches 3.png', 'Dessert - Menu Le Festin des Cloches - Vite & Gourmand', 3, false);

        // Menu 3 - Saveurs & Sortilèges 'menu_halloween'
        $createImage('menu_halloween', 'Visuels/Menu/Saveurs & Sortilèges 1.png', 'Entrée - Menu Saveurs & Sortilèges - Vite & Gourmand', 1, true);
        $createImage('menu_halloween', 'Visuels/Menu/Saveurs & Sortilèges 2.png', 'Plat - Menu Saveurs & Sortilèges - Vite & Gourmand', 2, false);
        $createImage('menu_halloween', 'Visuels/Menu/Saveurs & Sortilèges 3.png', 'Dessert - Menu Saveurs & Sortilèges - Vite & Gourmand', 3, false);

        // Menu 4 - Minuit Étincelant 'menu_nouvelan'
        $createImage('menu_nouvelan', 'Visuels/Menu/Minuit Étincelant 1.png', 'Entrée - Menu Minuit Étincelant - Vite & Gourmand', 1, true);
        $createImage('menu_nouvelan', 'Visuels/Menu/Minuit Étincelant 2.png', 'Plat - Menu Minuit Étincelant - Vite & Gourmand', 2, false);
        $createImage('menu_nouvelan', 'Visuels/Menu/Minuit Étincelant 3.png', 'Dessert - Menu Minuit Étincelant - Vite & Gourmand', 3, false);

        // Menu 5 - Lune de Miel 'menu_mariage'
        $createImage('menu_mariage', 'Visuels/Menu/Lune de Miel 1.png', 'Entrée - Menu Lune de Miel - Vite & Gourmand', 1, true);
        $createImage('menu_mariage', 'Visuels/Menu/Lune de Miel 2.png', 'Plat - Menu Lune de Miel - Vite & Gourmand', 2, false);
        $createImage('menu_mariage', 'Visuels/Menu/Lune de Miel 3.png', 'Dessert - Menu Lune de Miel - Vite & Gourmand', 3, false);

        // Menu 6 - Les Aventuriers 'menu_enfant'
        $createImage('menu_enfant', 'Visuels/Menu/Les Aventuriers 1.png', 'Entrée - Menu Les Aventuriers - Vite & Gourmand', 1, true);
        $createImage('menu_enfant', 'Visuels/Menu/Les Aventuriers 2.png', 'Plat - Menu Les Aventuriers - Vite & Gourmand', 2, false);
        $createImage('menu_enfant', 'Visuels/Menu/Les Aventuriers 3.png', 'Dessert - Menu Les Aventuriers - Vite & Gourmand', 3, false);

        // Menu 7 - Lumière & Tendresse 'menu_bapteme'
        $createImage('menu_bapteme', 'Visuels/Menu/Lumière & Tendresse 1.png', 'Entrée - Menu Lumière & Tendresse - Vite & Gourmand', 1, true);
        $createImage('menu_bapteme', 'Visuels/Menu/Lumière & Tendresse 2.png', 'Plat - Menu Lumière & Tendresse - Vite & Gourmand', 2, false);
        $createImage('menu_bapteme', 'Visuels/Menu/Lumière & Tendresse 3.png', 'Dessert - Menu Lumière & Tendresse - Vite & Gourmand', 3, false);

        // Menu 8 - Festin Carnivore 'menu_carnivore'
        $createImage('menu_carnivore', 'Visuels/Menu/Festin Carnivore 1.png', 'Entrée - Menu Festin Carnivore - Vite & Gourmand', 1, true);
        $createImage('menu_carnivore', 'Visuels/Menu/Festin Carnivore 2.png', 'Plat - Menu Festin Carnivore - Vite & Gourmand', 2, false);
        $createImage('menu_carnivore', 'Visuels/Menu/Festin Carnivore 3.png', 'Dessert - Menu Festin Carnivore - Vite & Gourmand', 3, false);

        // Menu 9 - Sapori d'Italia 'menu_italie'
        $createImage('menu_italie', 'Visuels/Menu/Sapori d\'Italia 1.png', 'Entrée - Menu Sapori d\'Italia - Vite & Gourmand', 1, true);
        $createImage('menu_italie', 'Visuels/Menu/Sapori d\'Italia 2.png', 'Plat - Menu Sapori d\'Italia - Vite & Gourmand', 2, false);
        $createImage('menu_italie', 'Visuels/Menu/Sapori d\'Italia 3.png', 'Dessert - Menu Sapori d\'Italia - Vite & Gourmand', 3, false);

        // Menu 10 - Jardin des Délices 'menu_vege'
        $createImage('menu_vege', 'Visuels/Menu/Jardin des Délices 1.png', 'Entrée - Menu Jardin des Délices - Vite & Gourmand', 1, true);
        $createImage('menu_vege', 'Visuels/Menu/Jardin des Délices 2.png', 'Plat - Menu Jardin des Délices - Vite & Gourmand', 2, false);
        $createImage('menu_vege', 'Visuels/Menu/Jardin des Délices 3.png', 'Dessert - Menu Jardin des Délices - Vite & Gourmand', 3, false);

        // Menu 11 - Évasion Asiatique 'menu_asie'
        $createImage('menu_asie', 'Visuels/Menu/Évasion Asiatique 1.png', 'Entrée - Menu Évasion Asiatique - Vite & Gourmand', 1, true);
        $createImage('menu_asie', 'Visuels/Menu/Évasion Asiatique 2.png', 'Plat - Menu Évasion Asiatique - Vite & Gourmand', 2, false);
        $createImage('menu_asie', 'Visuels/Menu/Évasion Asiatique 3.png', 'Dessert - Menu Évasion Asiatique - Vite & Gourmand', 3, false);

        // Menu 12 - Symphonie Maritime 'menu_mer'
        $createImage('menu_mer', 'Visuels/Menu/Symphonie Maritime 1.png', 'Entrée - Menu Symphonie Maritime - Vite & Gourmand', 1, true);
        $createImage('menu_mer', 'Visuels/Menu/Symphonie Maritime 2.png', 'Plat - Menu Symphonie Maritime - Vite & Gourmand', 2, false);
        $createImage('menu_mer', 'Visuels/Menu/Symphonie Maritime 3.png', 'Dessert - Menu Symphonie Maritime - Vite & Gourmand', 3, false);

        // Menu 13 - Palette Végétale 'menu_vegan'
        $createImage('menu_vegan', 'Visuels/Menu/Palette Végétale 1.png', 'Entrée - Menu Palette Végétale - Vite & Gourmand', 1, true);
        $createImage('menu_vegan', 'Visuels/Menu/Palette Végétale 2.png', 'Plat - Menu Palette Végétale - Vite & Gourmand', 2, false);
        $createImage('menu_vegan', 'Visuels/Menu/Palette Végétale 3.png', 'Dessert - Menu Palette Végétale - Vite & Gourmand', 3, false);

        // Menu 14 - Nature Sereine 'menu_gluten'
        $createImage('menu_gluten', 'Visuels/Menu/Nature Sereine 1.png', 'Entrée - Menu Nature Sereine - Vite & Gourmand', 1, true);
        $createImage('menu_gluten', 'Visuels/Menu/Nature Sereine 2.png', 'Plat - Menu Nature Sereine - Vite & Gourmand', 2, false);
        $createImage('menu_gluten', 'Visuels/Menu/Nature Sereine 3.png', 'Dessert - Menu Nature Sereine - Vite & Gourmand', 3, false);

        // Menu 15 - Délicatesse 'menu_lactose'
        $createImage('menu_lactose', 'Visuels/Menu/Délicatesse 1.png', 'Entrée - Menu Délicatesse - Vite & Gourmand', 1, true);
        $createImage('menu_lactose', 'Visuels/Menu/Délicatesse 2.png', 'Plat - Menu Délicatesse - Vite & Gourmand', 2, false);
        $createImage('menu_lactose', 'Visuels/Menu/Délicatesse 3.png', 'Dessert - Menu Délicatesse - Vite & Gourmand', 3, false);

        $manager->flush();
    }

    public static function getGroups(): array { return ['imagemenu']; }
}