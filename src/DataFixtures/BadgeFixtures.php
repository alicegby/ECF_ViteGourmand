<?php

namespace App\DataFixtures;

use App\Entity\Badge;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class BadgeFixtures extends Fixture implements FixtureGroupInterface {
    public function load(ObjectManager $manager): void {
        $badgesData = [
        [
            'nom' => 'Première Commande',
            'description' => 'Vous avez passé votre première commande chez Vite & Gourmand !',
            'icone' => 'Visuels/Badge/premiere_commande.png',
            'condition' => 'Créer un compte et passer sa première commande.',
            'actif' => true,
        ],
        [
            'nom' => 'Gourmand Confirmé',
            'description' => 'Vous avez commandé plus de 5 menus !',
            'icone' => 'Visuels/Badge/gourmand_confirmé.png',
            'condition' => 'Dès 5 commandes minimum passées.',
            'actif' => true,
        ],
        [
            'nom' => 'Client VIP',
            'description' => 'Vous avez commandé plus de 10 menus !',
            'icone' => 'Visuels/Badge/client_vip.png',
            'condition' => 'Dès 10 commandes minimum passées.',
            'actif' => true,
        ],
        [
            'nom' => 'Explorateurs des saveurs',
            'description' => 'Vous avez commander toutes les options d\'un même menu !',
            'icone' => 'Visuels/Badge/explorateurs_des_saveurs.png',
            'condition' => 'Dès 2 plats différents commandés sur le même menu.',
            'actif' => true,
        ],
        [
            'nom' => 'Critique',
            'description' => 'Vous avez laissé 5 avis détaillées et acceptées.',
            'icone' => 'Visuels/Badge/critique.png',
            'condition' => 'Rédigaction de 5 avis différents.',
            'actif' => true,
        ],
    ];

    foreach ($badgesData as $data) {
        $badge = new Badge();
        $badge->setNom($data['nom'])
            ->setDescription($data['description'])
            ->setIcone($data['icone'])
            ->setConditionObtention($data['condition'])
            ->setActif($data['actif']);

            $manager->persist($badge);
    }

    $manager->flush();
    }
    public static function getGroups(): array
    {
        return ['badge'];
    }
}