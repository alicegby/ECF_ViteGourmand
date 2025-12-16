<?php

namespace App\DataFixtures;

use App\Entity\Boissons;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class BoissonsFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface {
    public function getDependencies(): array
    {
        return [
            CategoryDrinkFixtures::class,
            EmployeFixtures::class,
        ];
    }
    public function load(ObjectManager $manager): void {
        $createBoisson = function(
            string $titre,
            string $categoryRef,
            string $description,
            int $stock,
            int $qteParPers,
            int $minCommande,
            string $prixParBouteille,
            string $image,
            string $alt,
            string $employeRef,
        ) use ($manager) {
            $boisson = new Boissons();
            $boisson->setTitreBoisson($titre)
                    ->setCategory($this->getReference($categoryRef, \App\Entity\CategoryDrink::class))
                    ->setDescription($description)
                    ->setStock($stock)
                    ->setQteParPers($qteParPers)
                    ->setMinCommande($minCommande)
                    ->setPrixParBouteille($prixParBouteille)
                    ->setImage($image)
                    ->setAlt($alt)
                    ->setModifiePar($this->getReference($employeRef, \App\Entity\Employe::class))
                    ->setDateModif(new \DateTime());

            $manager->persist($boisson);
        };

        $createBoisson(
            'Rouge Éclat',
            'vinrouge',
            'Intense et lumineux, pour un vin vif et fruité.',
            567,
            5,
            2,
            '17.90',
            'Visuels/Food/Boisson/Rouge Éclat.png',
            'Rouge Éclat - Vin Rouge - Vite & Gourmand',
            'employe_1'
        );
        $createBoisson(
            'Coeur de Vigne',
            'vinrouge',
            'Vin raffiné, profond et authentique.',
            436,
            5,
            2,
            '21.90',
            'Visuels/Food/Boisson/Coeur de Vigne.png',
            'Coeur de Vigne - Vin Rouge - Vite & Gourmand',
            'employe_1'
        );
        $createBoisson(
            'Nuit d\'Automne',
            'vinrouge',
            'Velouté et chaleureux, aux notes de fruits mûrs.',
            396,
            5,
            2,
            '19.90',
            'Visuels/Food/Boisson/Nuit d\'Automne.png',
            'Nuit d\'Automne - Vin Rouge - Vite & Gourmand',
            'employe_1'
        );
        $createBoisson(
            'Perle de Vigne',
            'vinblanc',
            'Élégant et raffiné, avec des notes minérales délicates.',
            294,
            6,
            2,
            '18.90',
            'Visuels/Food/Boisson/Perle de Vigne.png',
            'Perle de Vigne - Vin Blanc - Vite & Gourmand',
            'employe_1'
        );
        $createBoisson(
            'Rayon de Lune',
            'vinblanc',
            'Élégant et léger, parfait pour un moment raffiné.',
            392,
            6,
            2,
            '20.90',
            'Visuels/Food/Boisson/Rayon de Lune.png',
            'Rayon de Lune - Vin Blanc - Vite & Gourmand',
            'employe_1'
        );
        $createBoisson(
            'Blanc Enchanté',
            'vinblanc',
            'Aromatique et harmonieux, pour une dégustation magique.',
            459,
            6,
            2,
            '22.90',
            'Visuels/Food/Boisson/Blanc Enchanté.png',
            'Blanc Enchanté - Vin Blanc - Vite & Gourmand',
            'employe_1'
        );
        $createBoisson(
            'Lumière d\'Or',
            'champagne',
            'Frais et éclatant, aux arômes floraux et fruités.',
            311,
            7,
            4,
            '44.90',
            'Visuels/Food/Boisson/Lumière d\'Or.png',
            'Lumière d\'Or - Champagne - Vite & Gourmand',
            'employe_1'
        );
        $createBoisson(
            'Étoile Filante',
            'champagne',
            'Léger, vif et éclatant, parfait pour un toast.',
            214,
            7,
            4,
            '49.90',
            'Visuels/Food/Boisson/Étoile Filante.png',
            'Étoile Filante - Champagne - Vite & Gourmand',
            'employe_1'
        );
        $createBoisson(
            'Clarté d\'Été',
            'rose',
            'Frais et gourmand, idéal pour un repas ensoleillé.',
            975,
            6,
            2,
            '16.90',
            'Visuels/Food/Boisson/Clarté d\'Été.png',
            'Clarté d\'Été - Rosé - Vite & Gourmand',
            'employe_2'
        );
        $createBoisson(
            'Rosée du Matin',
            'rose',
            'Léger et fruité, parfait pour l\'apéritif ou l\'été.',
            568,
            6,
            2,
            '15.90',
            'Visuels/Food/Boisson/Rosée du Matin.png',
            'Rosée du Matin - Rosé - Vite & Gourmand',
            'employe_3'
        );
        $createBoisson(
            'Éclat de Pêche',
            'rose',
            'Doux et parfumé, aux notes de fruits frais.',
            205,
            6,
            2,
            '17.90',
            'Visuels/Food/Boisson/Éclat de Pêche.png',
            'Éclat de Pêche - Rosé - Vite & Gourmand',
            'employe_2'
        );
        $createBoisson(
            'Écume Cristalline',
            'eaugaz',
            'Fines bulles et fraîcheur.',
            2702,
            3,
            5,
            '3.50',
            'Visuels/Food/Boisson/Écume Cristalline.png',
            'Écume Cristalline - Eau Gazeuse - Vite & Gourmand',
            'employe_1'
        );
        $createBoisson(
            'Source Cristalline',
            'eauplate',
            'Pure et claire.',
            2465,
            3,
            5,
            '2.50',
            'Visuels/Food/Boisson/Source Cristalline.png',
            'Source Cristalline - Eau Plate - Vite & Gourmand',
            'employe_1'
        );
        $createBoisson(
            'Fizz Pop',
            'soda',
            'Cola pétillant et dynamique.',
            1034,
            4,
            3,
            '4.00',
            'Visuels/Food/Boisson/Fizz Pop.png',
            'Fizz Pop - Soda - Vite & Gourmand',
            'employe_1'
        );
        $createBoisson(
            'Sun Splash',
            'soda',
            'Soda aux agrumes, vitaminé et frais.',
            1623,
            4,
            3,
            '4.90',
            'Visuels/Food/Boisson/Sun Splash.png',
            'Sun Splash - Soda - Vite & Gourmand',
            'employe_4'
        );
        $createBoisson(
            'Tea Twist Fresh',
            'soda',
            'Thé glacé fruité et surprenant.',
            1485,
            4,
            3,
            '4.50',
            'Visuels/Food/Boisson/Tea Twist Fresh.png',
            'Tea Twist Fresh - Soda - Vite & Gourmand',
            'employe_1'
        );
        $createBoisson(
            'Lemon Zing',
            'soda',
            'Limonade acidulée et pétillante.',
            1840,
            4,
            3,
            '3.90',
            'Visuels/Food/Boisson/Lemon Zing.png',
            'Lemon Zing - Soda - Vite & Gourmand',
            'employe_1'
        );
        $createBoisson(
            'Thé Noir',
            'the',
            'Classique et corsé.',
            2345,
            1,
            1,
            '2.50',
            'Visuels/Food/Boisson/Thé Noir.png',
            'Thé Noir - Vite & Gourmand',
            'employe_2'
        );
        $createBoisson(
            'Thé Vert',
            'the',
            'Frais et léger.',
            2568,
            1,
            1,
            '2.50',
            'Visuels/Food/Boisson/Thé Vert.png',
            'Thé Vert - Vite & Gourmand',
            'employe_2'
        );
        $createBoisson(
            'Infusion Verveine',
            'infusion',
            'Frais et léger.',
            1684,
            1,
            1,
            '3.50',
            'Visuels/Food/Boisson/Infusion Verveine.png',
            'Infusion Verveine - Vite & Gourmand',
            'employe_2'
        );
        $createBoisson(
            'Infusion Citron & Miel',
            'infusion',
            'Frais et léger.',
            1940,
            1,
            1,
            '3.50',
            'Visuels/Food/Boisson/Infusion Citron & Miel.png',
            'Infusion Citron & Miel - Vite & Gourmand',
            'employe_2'
        );
        $createBoisson(
            'Café',
            'cafe',
            'Intense et chaud.',
            3045,
            1,
            1,
            '2.50',
            'Visuels/Food/Boisson/Café.png',
            'Café - Vite & Gourmand',
            'employe_2'
        );
        $createBoisson(
            'Décaféiné',
            'cafe',
            'Intense et chaud.',
            2998,
            1,
            1,
            '2.50',
            'Visuels/Food/Boisson/Décafféiné.png',
            'Décaféiné - Vite & Gourmand',
            'employe_2'
        );
        $createBoisson(
            'Brume Astrale',
            'biere',
            'Bière blanche artisanale, légère et rafraîchissante, aux notes d’agrumes et de coriandre.',
            184,
            1,
            12,
            '4.20',
            'Visuels/Food/Boisson/Brume Astrale.png',
            'Brume Astrale - Bière - Vite & Gourmand',
            'employe_2'
        );
        $createBoisson(
            'Nocturne des Houblons',
            'biere',
            'IPA ambrée aux arômes tropicaux et légèrement résineux.',
            132,
            1,
            12,
            '4.80',
            'Visuels/Food/Boisson/Nocturne des Houblons.png',
            'Nocturne des Houblons - Bière - Vite & Gourmand',
            'employe_2'
        );

        $manager->flush();
    }
    public static function getGroups(): array {
        return ['boissons'];
    }
}