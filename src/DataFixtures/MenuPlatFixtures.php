<?php

namespace App\DataFixtures;

use App\Entity\MenuPlat;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class MenuPlatFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface {
    public function getDependencies(): array {
        return [
            MenuFixtures::class,
            PlatsFixtures::class,
        ];
    }
    public function load(ObjectManager $manager): void {
        $createMenuPlat = function(string $menuRef, string $platRef, int $ordre) use ($manager) {
            $menuPlat = new MenuPlat();
            $menuPlat->setMenu($this->getReference($menuRef, \App\Entity\Menu::class));
            $menuPlat->setPlat($this->getReference($platRef, \App\Entity\Plats::class));
            $menuPlat->setOrdre($ordre);

            $manager->persist($menuPlat);
        };
        
        // Menu 1 - Saveurs Enchantées
        $createMenuPlat('menu_noel', 'foiegras', 1);
        $createMenuPlat('menu_noel', 'veloute_potimarron', 2);
        $createMenuPlat('menu_noel', 'chapon', 3);
        $createMenuPlat('menu_noel', 'saumon_roti', 4);
        $createMenuPlat('menu_noel', 'buche', 5);
        $createMenuPlat('menu_noel', 'mousse_marron', 6);

        // Menu 2 - Le Festin des Cloches
        $createMenuPlat('menu_paques', 'asperge', 1);
        $createMenuPlat('menu_paques', 'tartelette_legumes', 2);
        $createMenuPlat('menu_paques', 'agneau', 3);
        $createMenuPlat('menu_paques', 'saumon_papillotte_legumes', 4);
        $createMenuPlat('menu_paques', 'nid_paques', 5);
        $createMenuPlat('menu_paques', 'carrot_cake', 6);

        // Menu 3 - Saveurs & Sortilèges
        $createMenuPlat('menu_halloween', 'veloute_potimarron', 1);
        $createMenuPlat('menu_halloween', 'salade_noire', 2);
        $createMenuPlat('menu_halloween', 'lasagnes', 3);
        $createMenuPlat('menu_halloween', 'risotto_noir', 4);
        $createMenuPlat('menu_halloween', 'cupcakes', 5);
        $createMenuPlat('menu_halloween', 'mousse_potion', 6);

        // Menu 4 - Minuit Étincelant
        $createMenuPlat('menu_nouvelan', 'foiegras', 1);
        $createMenuPlat('menu_nouvelan', 'tartare_saumon_yuzu', 2);
        $createMenuPlat('menu_nouvelan', 'saumon_croute_herbe', 3);
        $createMenuPlat('menu_nouvelan', 'magret', 4);
        $createMenuPlat('menu_nouvelan', 'buche', 5);
        $createMenuPlat('menu_nouvelan', 'dome', 6);

        // Menu 5 - Lune de Miel
        $createMenuPlat('menu_mariage', 'foiegras', 1);
        $createMenuPlat('menu_mariage', 'tartare_saumon_yuzu', 2);
        $createMenuPlat('menu_mariage', 'bille', 3);
        $createMenuPlat('menu_mariage', 'volaille_cidre', 4);
        $createMenuPlat('menu_mariage', 'lotte_emulsion', 5);
        $createMenuPlat('menu_mariage', 'saumon_sesame', 6);
        $createMenuPlat('menu_mariage', 'pm_choux', 7);
        $createMenuPlat('menu_mariage', 'pm_macarons', 8);
        $createMenuPlat('menu_mariage', 'pm_cupcakes', 9);

        // Menu 6 - Les Aventuriers
        $createMenuPlat('menu_enfant', 'mini_brochette', 1);
        $createMenuPlat('menu_enfant', 'oeuf', 2);
        $createMenuPlat('menu_enfant', 'nuggets', 3);
        $createMenuPlat('menu_enfant', 'burger', 4);
        $createMenuPlat('menu_enfant', 'mousse_potion', 5);
        $createMenuPlat('menu_enfant', 'brownie', 6);

        // Menu 7 - Lumière & Tendresse
        $createMenuPlat('menu_bapteme', 'bille', 1);
        $createMenuPlat('menu_bapteme', 'salade_poire', 2);
        $createMenuPlat('menu_bapteme', 'lotte_emulsion', 3);
        $createMenuPlat('menu_bapteme', 'risotto', 4);
        $createMenuPlat('menu_bapteme', 'pana_cotta', 5);
        $createMenuPlat('menu_bapteme', 'choux', 6);

        // Menu 8 - Festin Carnivore
        $createMenuPlat('menu_carnivore', 'carpaccio', 1);
        $createMenuPlat('menu_carnivore', 'oignons', 2);
        $createMenuPlat('menu_carnivore', 'magret', 3);
        $createMenuPlat('menu_carnivore', 'cote_boeuf', 4);
        $createMenuPlat('menu_carnivore', 'tarte_tatin', 5);
        $createMenuPlat('menu_carnivore', 'mousse_corse', 6);

        // Menu 9 - Sapori d'Italia
        $createMenuPlat('menu_italie', 'carpaccio', 1);
        $createMenuPlat('menu_italie', 'bruschetta', 2);
        $createMenuPlat('menu_italie', 'tagliatelles', 3);
        $createMenuPlat('menu_italie', 'osso_buco', 4);
        $createMenuPlat('menu_italie', 'tiramisu', 5);
        $createMenuPlat('menu_italie', 'cannoli', 6);

        // Menu 10 - Jardin des Délices
        $createMenuPlat('menu_vege', 'quinoa', 1);
        $createMenuPlat('menu_vege', 'roules_courgettes', 2);
        $createMenuPlat('menu_vege', 'curry', 3);
        $createMenuPlat('menu_vege', 'tatin_legumes', 4);
        $createMenuPlat('menu_vege', 'carrot_cake', 5);
        $createMenuPlat('menu_vege', 'tarte_tatin', 6);

        // Menu 11 - Évasion Asiatique
        $createMenuPlat('menu_asie', 'gyozas', 1);
        $createMenuPlat('menu_asie', 'rouleaux_ptps', 2);
        $createMenuPlat('menu_asie', 'poulet_saute', 3);
        $createMenuPlat('menu_asie', 'boeuf_saute', 4);
        $createMenuPlat('menu_asie', 'perles_coco', 5);
        $createMenuPlat('menu_asie', 'mochi', 6);

        // Menu 12 - Symphonie Maritime
        $createMenuPlat('menu_mer', 'tartare_saumon', 1);
        $createMenuPlat('menu_mer', 'ceviche', 2);
        $createMenuPlat('menu_mer', 'saumon_papillotte_legumes', 3);
        $createMenuPlat('menu_mer', 'dorade', 4);
        $createMenuPlat('menu_mer', 'pana_cotta', 5);
        $createMenuPlat('menu_mer', 'tarte_tatin', 6);

        // Menu 13 - Palette Végétale
        $createMenuPlat('menu_vegan', 'betterave_avocat', 1);
        $createMenuPlat('menu_vegan', 'houmous', 2);
        $createMenuPlat('menu_vegan', 'ragout', 3);
        $createMenuPlat('menu_vegan', 'pates_lentilles', 4);
        $createMenuPlat('menu_vegan', 'mousse', 5);
        $createMenuPlat('menu_vegan', 'tatin_abricot', 6);

        // Menu 14 - Nature Sereine 
        $createMenuPlat('menu_gluten', 'veloute_potimarron', 1);
        $createMenuPlat('menu_gluten', 'oeuf', 2);
        $createMenuPlat('menu_gluten', 'saumon_croute_herbe', 3);
        $createMenuPlat('menu_gluten', 'volaille_cidre', 4);
        $createMenuPlat('menu_gluten', 'mousse_corse', 5);
        $createMenuPlat('menu_gluten', 'sorbets', 6);

        // Menu 15 - Délicatesse
        $createMenuPlat('menu_lactose', 'veloute_champi', 1);
        $createMenuPlat('menu_lactose', 'brochettes', 2);
        $createMenuPlat('menu_lactose', 'volaille_patatedouce', 3);
        $createMenuPlat('menu_lactose', 'saumon_agrume', 4);
        $createMenuPlat('menu_lactose', 'tartelette_fruits', 5);
        $createMenuPlat('menu_lactose', 'sorbets', 6);

        $manager->flush();
    }
    public static function getGroups(): array
    {
        return ['menuplat'];
    }
}