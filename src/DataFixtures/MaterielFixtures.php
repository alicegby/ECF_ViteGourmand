<?php

namespace App\DataFixtures;

use App\Entity\Materiel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class MaterielFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface {
    public function getDependencies(): array
    {
        return [
            CategoryMaterielFixtures::class,
            EmployeFixtures::class,
        ];
    }
    public function load(ObjectManager $manager): void {
        $createMateriel = function(
            string $titre,
            string $categoryRef,
            string $description,
            int $stock,
            string $prixPiece,
            string $image,
            string $alt,
            string $caution,
            string $employeRef,
        ) use ($manager) {
            $materiel = new Materiel();
            $materiel->setTitreMateriel($titre)
                ->setCategory($this->getReference($categoryRef, \App\Entity\CategoryMateriel::class))
                ->setDescription($description)
                ->setStock($stock)
                ->setPrixPiece($prixPiece)
                ->setImage($image)
                ->setAlt($alt)
                ->setCaution($caution)
                ->setModifiePar($this->getReference($employeRef, \App\Entity\Employe::class))
                ->setDateModif(new \DateTime());

            $manager->persist($materiel);
        };

        $createMateriel(
            'Four',
            'cuisine',
            'Four professionnel mobile pour réchauffer, cuire ou maintenir les plats à température lors d’un service. Idéal pour prestations sur site.',
            12,
            '45.00',
            'Visuels/Mobilier/Four.png',
            'Four - Vite & Gourmand',
            '600',
            'employe_2'
        );
        $createMateriel(
            'Plaque de cuisson',
            'cuisine',
            'Plaque induction portable permettant de saisir, mijoter ou maintenir au chaud en extérieur ou en service nomade.',
            18,
            '25.00',
            'Visuels/Mobilier/Plaque de cuisson.png',
            'Plaque de cuisson - Vite & Gourmand',
            '600',
            'employe_2'
        );
        $createMateriel(
            'Réfrigérateur',
            'froid',
            'Armoire réfrigérée mobile pour conserver aliments et boissons à bonne température durant l’événement.',
            10,
            '60.00',
            'Visuels/Mobilier/Réfrigérateur.png',
            'Réfrigérateur - Vite & Gourmand',
            '600',
            'employe_2'
        );
        $createMateriel(
            'Machine à glaçons',
            'froid',
            'Machine à production rapide de glace, idéale pour bars, cocktails et buffets boissons.',
            7,
            '40.00',
            'Visuels/Mobilier/Machine à glaçons.png',
            'Machine à glaçons - Vite & Gourmand',
            '600',
            'employe_2'
        );
        $createMateriel(
            'Congélateur',
            'froid',
            'Coffre ou armoire de congélation mobile pour stockage de glace, desserts ou produits sensibles.',
            6,
            '65.00',
            'Visuels/Mobilier/Congélateur.png',
            'Congélateur - Vite & Gourmand',
            '600',
            'employe_2'
        );
        $createMateriel(
            'Plateau de service',
            'service',
            'Plateaux en inox pour service en salle, distribution ou dressage rapide. ',
            150,
            '1.50',
            'Visuels/Mobilier/Plateau de Service.png',
            'Plateau de service - Vite & Gourmand',
            '600',
            'employe_2'
        );
        $createMateriel(
            'Plat de service',
            'service',
            'Grands plats en inox pour présenter pièces chaudes, viandes, légumes ou desserts.',
            90,
            '2.50',
            'Visuels/Mobilier/Plat de Service.png',
            'Plat de service - Vite & Gourmand',
            '600',
            'employe_2'
        );
        $createMateriel(
            'Table de buffet',
            'mobilier',
            'Tables robustes et élégantes pour disposer buffets, boissons, plats et matériel.',
            40,
            '12.00',
            'Visuels/Mobilier/Table de buffet.png',
            'Table de buffet - Vite & Gourmand',
            '600',
            'employe_2'
        );
        $createMateriel(
            'Table',
            'mobilier',
            'Tables rondes ou rectangulaires pour les repas assis des invités.',
            60,
            '10.00',
            'Visuels/Mobilier/Table.png',
            'Table - Vite & Gourmand',
            '600',
            'employe_2'
        );
        $createMateriel(
            'Chaise',
            'mobilier',
            'Chaises élégantes pour repas assis ou cérémonies.',
            200,
            '1.50',
            'Visuels/Mobilier/Chaise.png',
            'Chaise - Vite & Gourmand',
            '600',
            'employe_2'
        );
        $createMateriel(
            'Nappe',
            'linge',
            'Nappes blanches adaptées aux tables buffet ou repas, qualité traiteur.',
            180,
            '2.00',
            'Visuels/Mobilier/Nappe.png',
            'Nappe - Vite & Gourmand',
            '600',
            'employe_2'
        );
        $createMateriel(
            'Serviette',
            'linge',
            'Serviettes en tissu élégantes, assorties aux nappages.',
            350,
            '0.50',
            'Visuels/Mobilier/Serviette.png',
            'Serviette - Vite & Gourmand',
            '600',
            'employe_2'
        );
        $createMateriel(
            'Assiette',
            'vaisselle',
            'Assiettes plates et creuses en porcelaine pour service des entrées, plats ou desserts.',
            700,
            '0.60',
            'Visuels/Mobilier/Assiette.png',
            'Assiette - Vite & Gourmand',
            '600',
            'employe_2'
        );
        $createMateriel(
            'Couverts',
            'vaisselle',
            'Fourchettes, couteaux, cuillères inox polis, standards ou premium selon l’événement.',
            900,
            '0.40',
            'Visuels/Mobilier/Couverts.png',
            'Couverts - Vite & Gourmand',
            '600',
            'employe_2'
        );
        $createMateriel(
            'Verrerie',
            'vaisselle',
            'Verres à eau, vin, champagne ou cocktails, en verre, transparents et élégants.',
            800,
            '0.50',
            'Visuels/Mobilier/Verre.png',
            'Verrerie - Vite & Gourmand',
            '600',
            'employe_2'
        );
        $createMateriel(
            'Barnum',
            'exterieur',
            'Tentes professionnelles pour événements extérieurs, résistantes au vent et pluie, montage rapide.',
            8,
            '120.00',
            'Visuels/Mobilier/Barnums.png',
            'Barnum - Vite & Gourmand',
            '600',
            'employe_2'
        );
        $createMateriel(
            'Mange-debout',
            'mobilier',
            'Tables hautes idéales pour cocktails, apéritifs ou buffets debout.',
            35,
            '8.00',
            'Visuels/Mobilier/Mange-debout.png',
            'Mange-debout - Vite & Gourmand',
            '600',
            'employe_2'
        );
        $createMateriel(
            'Tabouret',
            'mobilier',
            'Tabourets hauts pour accompagner les mange-debout ou espaces lounge.',
            50,
            '4.00',
            'Visuels/Mobilier/Tabouret.png',
            'Tabouret - Vite & Gourmand',
            '600',
            'employe_2'
        );
        $createMateriel(
            'Seau à Champagne',
            'service',
            'Seaux inox pour maintenir les bouteilles de champagne ou de vin blanc au frais.',
            25,
            '3.00',
            'Visuels/Mobilier/Seau à Champagne.png',
            'Seau à Champagne - Vite & Gourmand',
            '600',
            'employe_2'
        );

        $manager->flush();
    }
    public static function getGroups(): array { return ['materiel']; }
}