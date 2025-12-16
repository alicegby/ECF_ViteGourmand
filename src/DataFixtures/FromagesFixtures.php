<?php

namespace App\DataFixtures;

use App\Entity\Fromages;
use App\DataFixtures\CategoryCheeseFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class FromagesFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface {
    public function getDependencies(): array
    {
        return [
            CategoryCheeseFixtures::class,
            EmployeFixtures::class
        ];
    }
    public function load(ObjectManager $manager): void {
        $createFromage = function(
            string $titre,
            string $categoryRef,
            string $description,
            int $stock,
            int $minCommande,
            string $prixParFromage,
            string $image,
            string $alt,
            string $employeRef,
        ) use ($manager) {
            $fromage = new Fromages();
            $fromage->setTitreFromage($titre)
                ->setCategory($this->getReference($categoryRef, \App\Entity\CategoryCheese::class))
                ->setDescription($description)
                ->setStock($stock)
                ->setMinCommande($minCommande)
                ->setPrixParFromage($prixParFromage)
                ->setImage($image)
                ->setAlt($alt)
                ->setModifiePar($this->getReference($employeRef, \App\Entity\Employe::class))
                ->setDateModif(new \DateTime());
            
            $manager->persist($fromage);
        };

        $createFromage(
            'Reblochon',
            'vache',
            'Pâte molle à croûte lavée, goût doux et crémeux.',
            14,
            3,
            '6.50',
            'Visuels/Food/Fromage/Reblochon.png',
            'Reblochon - Vite & Gourmand',
            'employe_3'
        );
        $createFromage(
            'Saint-Nectaire',
            'vache',
            'Pâte semi-ferme, saveur légèrement noisette.',
            39,
            3,
            '7.00',
            'Visuels/Food/Fromage/Saint-Nectaire.png',
            'Saint-Nectaire - Vite & Gourmand',
            'employe_3'
        );
        $createFromage(
            'Ossau-Iraty',
            'brebis',
            'Pâte pressée, saveur douce et fruitée.',
            45,
            4,
            '7.50',
            'Visuels/Food/Fromage/Ossau-Iraty.png',
            'Ossau-Iraty - Vite & Gourmand',
            'employe_3'
        );
        $createFromage(
            'Roquefort',
            'brebis',
            'Pâte persillée, goût puissant et salé.',
            84,
            4,
            '9.00',
            'Visuels/Food/Fromage/Roquefort.png',
            'Roquefort - Vite & Gourmand',
            'employe_3'
        );
        $createFromage(
            'Crottin de Chèvre',
            'chevre',
            'Pâte ferme à croûte naturelle, goût subtil et caprin.',
            82,
            5,
            '8.00',
            'Visuels/Food/Fromage/Crottin de Chèvre.png',
            'Crottin de Chèvre - Vite & Gourmand',
            'employe_3'
        );
        $createFromage(
            'Bûche de Chèvre',
            'chevre',
            'Pâte molle et crémeuse, parfumé et léger.',
            72,
            5,
            '7.50',
            'Visuels/Food/Fromage/Buche de Chèvre.png',
            'Bûche de Chèvre - Vite & Gourmand',
            'employe_3'
        );

        $manager->flush(); 
    }
    public static function getGroups(): array { return ['fromages']; }
}