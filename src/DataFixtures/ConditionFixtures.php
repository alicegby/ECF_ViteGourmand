<?php

namespace App\DataFixtures;

use App\Entity\Condition;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class ConditionFixtures extends Fixture implements FixtureGroupInterface {
    public function load(ObjectManager $manager): void {
        $conditionsData = [
            "Commande minimum 8 mois à l'avance.",
            "Commande minimum 5 mois à l'avance.",
            "Commande minimum 3 mois à l'avance.",
            "Commande minimum 1 mois à l'avance.",
            "Commande minimum 3 semaines à l'avance.",
            "Commande minimum 2 semaines à l'avance.",
            "Annulation possible jusqu'à 45 jours avant",
            "Annulation possible jusqu'à 30 jours avant.",
            "Annulation possible jusqu'à 15 jours avant.",
            "Annulation possible jusqu'à 10 jours avant.",
            "Annulation possible jusqu'à 7 jours avant",
            "Menus disponibles du 1er au 26 Décembre",
            "Menus disponibles du 1er Mars au 30 Avril",
            "Menus disponibles tout le mois d'Octobre",
            "Menus disponibles du 1er au 31 Décembre"
        ];

        foreach ($conditionsData as $i => $libelle) {
            $condition = new Condition();
            $condition->setLibelle($libelle);

            $manager->persist($condition);

            $this->addReference('condition_' . ($i+1), $condition);
        }

        $manager->flush();   
    }

    // Groupe pour envoi BDD
    public static function getGroups(): array
    {
        return ['conditions'];
    }
} 