<?php

namespace App\DataFixtures;

use App\Entity\Reduction;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class ReductionFixtures implements FixtureGroupInterface {
    public function load(ObjectManager $manager): void {
        $createReduction = function(
            string $type,
            ?int $conditionQuantite,
            string $reduction,
            bool $actif = true
        ) use ($manager) {
            $reduc = new Reduction();
            $reduc->setType($type)
                ->setConditionQuantite($conditionQuantite)
                ->setReduction($reduction)
                ->setActif($actif);

            $manager->persist($reduc);
        };
        $createReduction(
            '3_boissons_diff',
            3,
            '10.00',
            true
        );
        $createReduction(
            '3_fromages',
            3,
            '10.00',
            true
        );
        $createReduction(
            '5_personnes_sup',
            5,
            '10.00',
            true
        );

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['reduction'];
    }
}