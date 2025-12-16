<?php

namespace App\DataFixtures;

use App\Entity\Allergenes;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class AllergenesFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        // Création des allergènes avec références explicites
        $arachide = new Allergenes();
        $arachide->setLibelle('Arachide');
        $arachide->setIcone('Visuels/Allergènes/Arachide.png');
        $manager->persist($arachide);
        $this->addReference('arachide', $arachide);

        $celeri = new Allergenes();
        $celeri->setLibelle('Céleri');
        $celeri->setIcone('Visuels/Allergènes/Celeri.png');
        $manager->persist($celeri);
        $this->addReference('celeri', $celeri);

        $gluten = new Allergenes();
        $gluten->setLibelle('Céréale (Gluten)');
        $gluten->setIcone('Visuels/Allergènes/Gluten.png');
        $manager->persist($gluten);
        $this->addReference('gluten', $gluten);

        $crustace = new Allergenes();
        $crustace->setLibelle('Crustacé');
        $crustace->setIcone('Visuels/Allergènes/Crustacés.png');
        $manager->persist($crustace);
        $this->addReference('crustace', $crustace);

        $fruitCoque = new Allergenes();
        $fruitCoque->setLibelle('Fruit à coque');
        $fruitCoque->setIcone('Visuels/Allergènes/FruitsCoquespng');
        $manager->persist($fruitCoque);
        $this->addReference('fruitCoque', $fruitCoque);

        $lait = new Allergenes();
        $lait->setLibelle('Lait');
        $lait->setIcone('Visuels/Allergènes/Milk.png');
        $manager->persist($lait);
        $this->addReference('lait', $lait);

        $legumineuse = new Allergenes();
        $legumineuse->setLibelle('Légumineuse');
        $legumineuse->setIcone('Visuels/Allergènes/Légumineuses.png');
        $manager->persist($legumineuse);
        $this->addReference('legumineuse', $legumineuse);

        $lupin = new Allergenes();
        $lupin->setLibelle('Lupin');
        $lupin->setIcone('Visuels/Allergènes/Lupin.png');
        $manager->persist($lupin);
        $this->addReference('lupin', $lupin);

        $moutarde = new Allergenes();
        $moutarde->setLibelle('Moutarde');
        $moutarde->setIcone('Visuels/Allergènes/Moutarde.png');
        $manager->persist($moutarde);
        $this->addReference('moutarde', $moutarde);

        $oeuf = new Allergenes();
        $oeuf->setLibelle('Oeuf');
        $oeuf->setIcone('Visuels/Allergènes/Egg.png');
        $manager->persist($oeuf);
        $this->addReference('oeuf', $oeuf);

        $poisson = new Allergenes();
        $poisson->setLibelle('Poisson');
        $poisson->setIcone('Visuels/Allergènes/Poisson.png');
        $manager->persist($poisson);
        $this->addReference('poisson', $poisson);

        $soja = new Allergenes();
        $soja->setLibelle('Soja');
        $soja->setIcone('Visuels/Allergènes/Soja.png');
        $manager->persist($soja);
        $this->addReference('soja', $soja);

        // Enregistrement final
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['allergenes'];
    }
}