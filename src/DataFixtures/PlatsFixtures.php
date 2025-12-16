<?php

namespace App\DataFixtures;

use App\Entity\Plats;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PlatsFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface {
    public function getDependencies(): array
    {
        return [
            CategoryFoodFixtures::class,
            EmployeFixtures::class,
            AllergenesFixtures::class,
        ];
    }
    public function load(ObjectManager $manager): void {
        $createPlat = function(
            string $titre,
            string $categoryRef,
            string $description,
            int $stock,
            string $image,
            string $alt,
            string $employeRef,
            array $allergenesRef,
            ?string $refKey = null
        ) use ($manager) {
            $plat = new Plats();
            $plat->setTitrePlat($titre)
                ->setCategory($this->getReference($categoryRef, \App\Entity\CategoryFood::class))
                ->setDescription($description)
                ->setStock($stock)
                ->setImage($image)
                ->setAltTexte($alt) 
                ->setModifiePar($this->getReference($employeRef, \App\Entity\Employe::class))
                ->setDateModif(new \DateTime());

            foreach ($allergenesRef as $allRef) {
                $allergene = $this->getReference($allRef, \App\Entity\Allergenes::class);
                $plat->addAllergene($allergene); 
            }
            $manager->persist($plat);

            if ($refKey !== null) {
                $this->addReference($refKey, $plat);
            }
        };

        $createPlat(
            'Foie Gras sur toast brioché',
            'entree',
            'Délicieux foie gras fondant servi sur un toast légèrement brioché et grillé, accompagné d’une confiture de figues qui sublime chaque bouchée. Une entrée riche et festive pour démarrer le repas.',
            170,
            'Visuels/Food/Plats/Foie Gras sur toast brioché.png',
            'Foie gras - Vite & Gourmand',
            'employe_1',
            ['gluten', 'lait'],
            'foiegras'
        );

        $createPlat(
            'Velouté de potimarron aux éclats de châtaignes',
            'entree',
            'Soupe douce et crémeuse au potimarron, relevée d’une pointe de muscade et décorée de graines grillées pour le croquant. Parfait pour réchauffer les invités.',
            120,
            'Visuels/Food/Plats/Velouté de potimarron aux éclats de châtaignes.png',
            'Velouté de potimarron - Vite & Gourmand',
            'employe_1',
            ['lait'],
            'veloute_potimarron'
        );

        $createPlat(
            'Chapon rôti, purée de marrons et sauce aux airelles',
            'plat',
            'Chapon tendre et doré au four, accompagné d’une purée de marrons onctueuse et d’une sauce aux airelles légèrement acidulée pour équilibrer les saveurs. Un classique de Noël qui réchauffe les cœurs.',
            120,
            'Visuels/Food/Plats/Chapon rôti, purée de marrons et sauce aux airelles.png',
            'Châpon rôti, purée de marrons - Vite & Gourmand',
            'employe_1',
            ['lait'],
            'chapon'
        );

        $createPlat(
            'Saumon rôti aux agrumes et épices de Noël',
            'plat',
            'Saumon rôti aux agrumes et épices de Noël, délicatement parfumé à l’orange, clémentine, cannelle et gingembre, accompagné d’une purée de potimarron vanillée et d’éclats de noisettes torréfiées, pour un festin raffiné et coloré.',
            110,
            'Visuels/Food/Plats/Saumon rôti aux agrumes et épices de Noël.png',
            'Saumon rôti - Vite & Gourmand',
            'employe_1',
            ['lait', 'poisson', 'fruitCoque'],
            'saumon_roti'
        );

        $createPlat(
            'Bûche glacée au chocolat et fruits rouges',
            'dessert',
            'Bûche glacée aux textures contrastées : chocolat onctueux et coulis de fruits rouges acidulés, décorée de petits éclats croquants pour un effet visuel magique sur la table.',
            155,
            'Visuels/Food/Plats/Bûche glacée au chocolat et fruits rouges.png',
            'Bûche glacée - Vite & Gourmand',
            'employe_1',
            ['lait', 'oeuf', 'fruitCoque'],
            'buche'
        );

        $createPlat(
            'Mousse de marrons et éclats de chocolat blanc',
            'dessert',
            'Mousse onctueuse de marrons, délicatement sucrée, parsemée d’éclats de chocolat blanc croquants, pour un dessert de Noël gourmand, élégant et plein de douceur.',
            60,
            'Visuels/Food/Plats/Mousse de marrons et éclats de chocolat blanc.png',
            'Mousse de marrons - Vite & Gourmand',
            'employe_2',
            ['lait', 'oeuf'],
            'mousse_marron'
        );

        $createPlat(
            'Asperges Mimosa',
            'entree',
            'Asperges fraîches légèrement cuites, nappées d’un émulsion de jaune d’œuf mimosa et d’un filet d’huile d’olive. Frais et printanier, parfait pour réveiller les papilles.',
            60,
            'Visuels/Food/Plats/Asperge mimosa.png',
            'Asperges Mimosa - Vite & Gourmand',
            'employe_2',
            ['moutarde', 'oeuf'],
            'asperge'
        );

        $createPlat(
            'Tartelette aux légumes printaniers et fromage frais',
            'entree',
            'Tartelette aux légumes printaniers colorés, garnie de fromage frais crémeux et d’herbes fines, pour une entrée légère, savoureuse et pleine de fraîcheur, idéale pour éveiller les papilles dès le début du repas.',
            55,
            'Visuels/Food/Plats/Tartelette aux légumes printaniers et fromage frais.png',
            'Tartelette aux légumes - Vite & Gourmand',
            'employe_2',
            ['lait', 'oeuf', 'gluten'],
            'tartelette_legumes'
        );

        $createPlat(
            'Gigot d’agneau rôti, gratin dauphinois',
            'plat',
            'Gigot d’agneau tendre et parfumé aux herbes, servi avec un gratin dauphinois crémeux à souhait. Une combinaison qui fait la fête aux saveurs et aux textures.',
            100,
            'Visuels/Food/Plats/Gigot d’agneau rôti, gratin dauphinois.png',
            'Gigot d’agneau rôti - Vite & Gourmand',
            'employe_3',
            ['lait', 'oeuf', 'gluten'],
            'agneau'
        );

        $createPlat(
            'Saumon en papillote aux légumes nouveaux et citron',
            'plat',
            'Saumon en papillote, délicatement cuit avec légumes nouveaux et citron frais, parfumé aux herbes, pour un plat léger, savoureux et élégant qui met en valeur les saveurs naturelles du poisson et des légumes.',
            160,
            'Visuels/Food/Plats/Saumon en papillote aux légumes nouveaux et citron.png',
            'Saumon en papillotte - Vite & Gourmand',
            'employe_3',
            ['lait', 'poisson'],
            'saumon_papillotte_legumes'
        );

        $createPlat(
            'Nid de Pâques au chocolat et praliné',
            'dessert',
            'Un nid croustillant en chocolat noir et praliné, garni de petits œufs de Pâques. Gourmand et ludique, il émerveillera petits et grands.',
            85,
            'Visuels/Food/Plats/Nid de Pâques au chocolat et praliné.png',
            'Nid de Pâques en chocolat - Vite & Gourmand',
            'employe_3',
            ['lait', 'oeuf', 'fruitCoque'],
            'nid_paques'
        );

        $createPlat(
            'Carrot cake glacé au cream cheese',
            'dessert',
            'Carrot cake moelleux, délicatement glacé au cream cheese onctueux, parfumé aux épices douces et aux noix, pour un dessert gourmand, réconfortant et coloré qui évoque la chaleur et la fête.',
            61,
            'Visuels/Food/Plats/Carrot cake glacé au cream cheese.png',
            'Carrot Cake - Vite & Gourmand',
            'employe_3',
            ['lait', 'oeuf', 'fruitCoque', 'gluten'],
            'carrot_cake'
        );

        $createPlat(
            'Salade noire aux graines de sésame et vinaigrette sanguine (betterave)',
            'entree',
            'Salade noire originale, composée de légumes et graines de sésame, accompagnée d’une vinaigrette à la betterave rouge, pour un plat frais, coloré et surprenant, parfait pour un menu d’Halloween ou automnal.',
            40,
            'Visuels/Food/Plats/Salade noire aux graines de sésame et vinaigrette sanguine (betterave).png',
            'Salade noire aux graines de sésame - Vite & Gourmand',
            'employe_3',
            [],
            'salade_noire'
        );

        $createPlat(
            'Lasagnes',
            'plat',
            'Lasagnes généreuses et fondantes, garnies de sauce tomate parfumée, légumes ou viande selon la version, et recouvertes de fromage gratiné, pour un plat réconfortant, gourmand et familial.',
            70,
            'Visuels/Food/Plats/Lasagnes.png',
            'Lasagnes - Vite & Gourmand',
            'employe_3',
            ['gluten', 'lait', 'oeuf'],
            'lasagnes'
        );

        $createPlat(
            'Risotto noir à l’encre de seiche et parmesan',
            'plat',
            'Risotto crémeux à l’encre de seiche, parfumé et délicatement coloré, parsemé de parmesan râpé, pour un plat élégant, raffiné et visuellement surprenant, parfait pour un repas festif ou original.',
            60,
            'Visuels/Food/Plats/Risotto noir à l’encre de seiche et parmesan.png',
            'Risotto noir à l\'encre de seiche - Vite & Gourmand',
            'employe_3',
            ['lait', 'crustace'],
            'risotto_noir'
        );

        $createPlat(
            'Cupcakes araignées au chocolat',
            'dessert',
            'Cupcakes moelleux au chocolat, décorés d’araignées en chocolat, pour un dessert ludique et gourmand, parfait pour un menu d’Halloween ou une fête effrayante et colorée.',
            35,
            'Visuels/Food/Plats/Cupcakes araignées au chocolat.png',
            'Cupcakes araignées au chocolat - Vite & Gourmand',
            'employe_3',
            ['lait', 'gluten', 'oeuf'],
            'cupcakes'
        );

        $createPlat(
            'Mousse au chocolat noir « potion magique »',
            'dessert',
            'Mousse au chocolat noir intense, légère et aérienne, servie comme une « potion magique » pour un dessert ludique, élégant et gourmand, parfait pour surprendre les invités lors d’un menu d’Halloween.',
            31,
            'Visuels/Food/Plats/Mousse au chocolat noir « potion magique ».png',
            'Mousse au chocolat noir « potion magique » - Vite & Gourmand',
            'employe_4',
            ['lait', 'oeuf'],
            'mousse_potion'
        );

        $createPlat(
            'Tartare de saumon aux agrumes et perles de yuzu',
            'entree',
            'Tartare de saumon frais, parfumé aux agrumes et parsemé de perles de yuzu, pour une entrée légère, raffinée et pleine de fraîcheur, parfaite pour un menu de Nouvel An ou un repas festif.',
            75,
            'Visuels/Food/Plats/Tartare de saumon aux agrumes et perles de yuzu.png',
            'Tartare de saumon aux agrumes - Vite & Gourmand',
            'employe_4',
            ['poisson'],
            'tartare_saumon_yuzu'
        );

        $createPlat(
            'Saumon en croûte d’herbes, purée de céleri-rave',
            'plat',
            'Saumon en croûte d’herbes fraîches, accompagné d’une purée onctueuse de céleri-rave, pour un plat raffiné, savoureux et parfumé, idéal pour un repas de fête ou un menu de Nouvel An élégant.',
            105,
            'Visuels/Food/Plats/Saumon en croûte d’herbes, purée de céleri-rave.png',
            'Saumon en croûte d’herbes - Vite & Gourmand',
            'employe_4',
            ['poisson', 'lait', 'oeuf'],
            'saumon_croute_herbe'
        );

        $createPlat(
            'Magret de canard aux fruits rouges et miel',
            'plat',
            'Magret de canard tendre, nappé d’une sauce aux fruits rouges et miel, pour un plat sucré-salé raffiné, gourmand et élégant, idéal pour un repas festif ou un menu de Nouvel An.',
            40,
            'Visuels/Food/Plats/Magret de canard aux fruits rouges et miel.png',
            'Magret de canard aux fruits rouges et miel - Vite & Gourmand',
            'employe_4',
            [],
            'magret'
        );

        $createPlat(
            'Mini dômes au chocolat et framboise',
            'dessert',
            'Mini dômes au chocolat noir, cœur framboise fondant, pour un dessert élégant, gourmand et raffiné, parfait pour un menu de fête ou un repas de Nouvel An haut en couleurs et saveurs.',
            15,
            'Visuels/Food/Plats/Mini dômes au chocolat et framboise.png',
            'Mini dômes au chocolat et framboise - Vite & Gourmand',
            'employe_4',
            ['lait', 'oeuf'],
            'dome'
        );

        $createPlat(
            'Billes de truite au cœur gelé de concombre et citron',
            'entree',
            'Billes de truite délicates, garnies d’un cœur gelé au concombre et citron, pour une entrée fraîche, raffinée et pleine de légèreté, parfaite pour un menu de mariage ou un repas festif élégant.',
            40,
            'Visuels/Food/Plats/Billes de truite au cœur gelé de concombre et citron.png',
            'Billes de truite au cœur gelé de concombre et citron - Vite & Gourmand',
            'employe_4',
            ['poisson'],
            'bille'
        );

        $createPlat(
            'Filet de volaille, sauce cidre-crème et salicorne',
            'plat',
            'Filet de volaille tendre, nappé d’une sauce onctueuse au cidre et accompagné de salicorne croquante, pour un plat raffiné, savoureux et élégant, parfait pour un menu de mariage ou un repas festif.',
            70,
            'Visuels/Food/Plats/Filet de volaille, sauce cidre-crème et salicorne.png',
            'Filet de volaille, sauce cidre-crème et salicorne - Vite & Gourmand',
            'employe_4',
            ['lait'],
            'volaille_cidre'
        );

        $createPlat(
            'Pavé de lotte, émulsion légère et légumes glacés',
            'plat',
            'Pavé de lotte délicatement poché, nappé d’une émulsion légère et accompagné de légumes glacés colorés, pour un plat raffiné, savoureux et élégant, parfait pour un menu de mariage ou un repas de fête.',
            45,
            'Visuels/Food/Plats/Pavé de lotte, émulsion légère et légumes glacés.png',
            'Pavé de lotte, émulsion légère et légumes glacés - Vite & Gourmand',
            'employe_4',
            ['poisson', 'lait'],
            'lotte_emulsion'
        );

        $createPlat(
            'Saumon en croûte de sésame, tombée de fenouil',
            'plat',
            'Saumon délicat en croûte de sésame, accompagné d’une tombée de fenouil fondante et parfumée, pour un plat raffiné, léger et élégant, idéal pour un menu mariage ou un repas festif.',
            25,
            'Visuels/Food/Plats/Saumon en croûte de sésame, tombée de fenouil.png',
            'Saumon en croûte de sésame, tombée de fenouil - Vite & Gourmand',
            'employe_4',
            ['poisson'],
            'saumon_sesame'
        );

        $createPlat(
            'Pièce Montée de choux à la Vanille',
            'dessert',
            'Pièce montée de choux garnis d’une crème légère à la vanille, dressée avec élégance pour un dessert spectaculaire, gourmand et raffiné, parfait pour clore un menu de mariage ou un repas festif.',
            40,
            'Visuels/Food/Plats/Pièce Montée de choux à la vanille.png',
            'Pièce Montée de choux à la vanille - Vite & Gourmand',
            'employe_4',
            ['lait', 'oeuf', 'gluten'],
            'pm_choux'
        );

        $createPlat(
            'Pièce Montée de macarons Framboise / Chocolat',
            'dessert',
            'Pièce montée élégante de macarons framboise et chocolat, alliant croquant et fondant, pour un dessert raffiné, coloré et délicieusement gourmand, parfait pour sublimer un menu de mariage ou une grande occasion.',
            40,
            'Visuels/Food/Plats/Pièce Montée de macarons Framboise _ Chocolat.png',
            'Pièce Montée de macarons Framboise _ Chocolat - Vite & Gourmand',
            'employe_4',
            ['fruitCoque', 'oeuf', 'lait'],
            'pm_macarons'
        );

        $createPlat(
            'Pièce Montée de cupcakes Fruits Rouges / Chocolat',
            'dessert',
            'Pièce montée de cupcakes aux fruits rouges et au chocolat, moelleux et gourmands, décorés avec élégance pour offrir un dessert spectaculaire, coloré et festif, parfait pour un mariage ou une grande célébration.',
            40,
            'Visuels/Food/Plats/Pièce Montée de Cupcakes fruits rouges _ chocolat.png',
            'Pièce Montée de Cupcakes fruits rouges _ chocolat - Vite & Gourmand',
            'employe_4',
            ['gluten', 'lait', 'oeuf'],
            'pm_cupcakes'
        );

        $createPlat(
            'Mini brochettes de tomates cerises et mozzarella',
            'entree',
            'Mini brochettes fraîches de tomates cerises et mozzarella, simples et colorées, pour une bouchée légère, estivale et gourmande, parfaite pour un cocktail ou une entrée raffinée.',
            30,
            'Visuels/Food/Plats/Mini brochettes de tomates cerises et mozzarella.png',
            'Mini brochettes de tomates cerises et mozzarella - Vite & Gourmand',
            'employe_2',
            ['lait'],
            'mini_brochette'
        );

        $createPlat(
            'Œufs mimosa',
            'entree',
            'Œufs mimosa classiques et gourmands, garnis d’une farce crémeuse relevée d’une touche de moutarde, pour une entrée fraîche, simple et toujours appréciée lors d’un buffet ou d’un repas festif.',
            90,
            'Visuels/Food/Plats/Œufs mimosa.png',
            'Œufs mimosa - Vite & Gourmand',
            'employe_1',
            ['oeuf', 'moutarde'],
            'oeuf'
        );

        $createPlat(
            'Nuggets maison et sauce BBQ',
            'plat',
            'Petits morceaux de poulet dorés et croustillants, servis avec une sauce barbecue fumée maison. Idéal pour un début de repas convivial et réconfortant.',
            30,
            'Visuels/Food/Plats/Nuggets maison et sauce BBQ.png',
            'Nuggets maison et sauce BBQ - Vite & Gourmand',
            'employe_3',
            ['gluten', 'lait', 'oeuf'],
            'nuggets'
        );

        $createPlat(
            'Burger gourmand avec frites maison',
            'plat',
            'Pain brioché moelleux, steak juteux, fromage fondant, légumes frais et sauce maison, accompagné de frites croustillantes. Un classique américain qui fait toujours plaisir.',
            30,
            'Visuels/Food/Plats/Burger gourmand avec frites maison.png',
            'Burger gourmand avec frites maison - Vite & Gourmand',
            'employe_3',
            ['gluten', 'lait', 'oeuf'],
            'burger'
        );

        $createPlat(
            'Brownie au chocolat et noix',
            'dessert',
            'Gâteau fondant au chocolat noir avec éclats de noix croquants, idéal pour les amateurs de chocolat intense.',
            30,
            'Visuels/Food/Plats/Brownie au chocolat et noix.png',
            'Brownie au chocolat et noix - Vite & Gourmand',
            'employe_1',
            ['lait', 'oeuf', 'fruitCoque'],
            'brownie'
        );

        $createPlat(
            'Salade de jeunes pousses et poires rôties',
            'entree',
            'Salade de jeunes pousses, poires rôties au miel et touche de fruits secs, pour une entrée fraîche, douce et élégante, parfaite en toutes saisons avec son équilibre sucré-salé délicat.',
            50,
            'Visuels/Food/Plats/Salade de jeunes pousses et poires rôties.png',
            'Salade de jeunes pousses et poires rôties - Vite & Gourmand',
            'employe_4',
            [],
            'salade_poire'
        );

        $createPlat(
            'Risotto aux petits légumes et parmesan',
            'plat',
            'Risotto crémeux aux petits légumes de saison, délicatement parfumé et parsemé de parmesan râpé, pour un plat réconfortant, coloré et raffiné, parfait en entrée ou plat principal gourmand et élégant.',
            50,
            'Visuels/Food/Plats/Risotto aux petits légumes et parmesan.png',
            'Risotto aux petits légumes et parmesan - Vite & Gourmand',
            'employe_4',
            ['lait'],
            'risotto'
        );

        $createPlat(
            'Panna cotta à la vanille et coulis de fruits rouges',
            'dessert',
            'Panna cotta crémeuse à la vanille servie avec un coulis acidulé de fruits rouges, pour un dessert élégant et frais.',
            40,
            'Visuels/Food/Plats/Panna cotta vanille et coulis de fruits rouges.png',
            'Panna cotta vanille et coulis de fruits rouges - Vite & Gourmand',
            'employe_2',
            ['lait'],
            'pana_cotta'
        );

        $createPlat(
            'Petits choux à la crème',
            'dessert',
            'Petits choux légers et moelleux, garnis d’une crème onctueuse et délicatement parfumée, pour un dessert gourmand, élégant et raffiné, parfait pour clore un repas festif ou un menu de mariage.',
            35,
            'Visuels/Food/Plats/Petits choux à la crème.png',
            'Petits choux à la crème - Vite & Gourmand',
            'employe_1',
            ['lait', 'oeuf', 'gluten'],
            'choux'
        );

        $createPlat(
            'Carpaccio de bœuf, roquette et parmesan',
            'entree',
            'Fines tranches de bœuf cru, fondantes en bouche, relevées par la fraîcheur de la roquette et des copeaux de parmesan. Une entrée élégante et savoureuse.',
            30,
            'Visuels/Food/Plats/Carpaccio de bœuf, roquette et parmesan.png',
            'Carpaccio de bœuf, roquette et parmesan - Vite & Gourmand',
            'employe_2',
            ['lait'],
            'carpaccio'
        );

        $createPlat(
            'Oignons confits et foie gras en verrine',
            'entree',
            'Oignons confits doux et légèrement sucrés, servis avec un foie gras fondant en verrine, pour une entrée élégante, raffinée et festive, parfaite pour un menu de Noël ou un repas de fête.',
            20,
            'Visuels/Food/Plats/Oignons confits et foie gras en verrine.png',
            'Oignons confits et foie gras en verrine - Vite & Gourmand',
            'employe_4',
            [],
            'oignons'
        );

        $createPlat(
            'Côte de bœuf grillée, légumes rôtis',
            'plat',
            'Côte de bœuf juteuse et parfaitement grillée, accompagnée de légumes de saison rôtis au four et parfumés aux herbes. Un plat gourmand et réconfortant.',
            30,
            'Visuels/Food/Plats/Côte de bœuf grillée, légumes rôtis.png',
            'Côte de bœuf grillée, légumes rôtis - Vite & Gourmand',
            'employe_4',
            [],
            'cote_boeuf'
        );

        $createPlat(
            'Tarte tatin aux pommes',
            'dessert',
            'Pommes caramélisées fondantes sur une pâte croustillante, servies tièdes avec éventuellement une boule de glace vanille. Un classique qui séduit toujours.',
            15,
            'Visuels/Food/Plats/Tarte tatin aux pommes.png',
            'Tarte tatin aux pommes - Vite & Gourmand',
            'employe_4',
            ['gluten', 'lait'],
            'tarte_tatin'
        );

        $createPlat(
            'Mousse au chocolat noir corsé',
            'dessert',
            'Mousse au chocolat noir corsé, légère et aérienne, pour un dessert intense et gourmand, parfait pour conclure un repas sur une note élégante et raffinée.',
            92,
            'Visuels/Food/Plats/Mousse au chocolat noir corsé.png',
            'Mousse au chocolat noir corsé - Vite & Gourmand',
            'employe_3',
            ['lait', 'oeuf'],
            'mousse_corse'
        );

        $createPlat(
            'Bruschetta tomate-basilic',
            'entree',
            'Pain grillé garni de tomates fraîches, basilic parfumé et huile d’olive extra vierge. Une entrée fraîche, colorée et pleine de soleil italien.',
            8,
            'Visuels/Food/Plats/Bruschetta tomate-basilic.png',
            'Bruschetta tomate-basilic - Vite & Gourmand',
            'employe_1',
            ['gluten'],
            'bruschetta'
        );

        $createPlat(
            'Tagliatelles fraîches aux champignons et crème au parmesan',
            'plat',
            'Pâtes fraîches maison, légèrement al dente, enrobées d’une sauce crémeuse au parmesan et champignons sautés, parfumées à l’ail et aux herbes. Un plat réconfortant, parfait pour les amateurs de cuisine italienne traditionnelle.',
            12,
            'Visuels/Food/Plats/Tagliatelles fraîches aux champignons et crème au parmesan.png',
            'Tagliatelles fraîches aux champignons et crème au parmesan - Vite & Gourmand',
            'employe_1',
            ['gluten', 'lait', 'oeuf'],
            'tagliatelles'
        );

        $createPlat(
            'Osso buco à la milanaise',
            'plat',
            'Osso buco à la milanaise, jarret de veau mijoté lentement avec légumes et vin blanc, nappé de gremolata citronnée, pour un plat tendre, parfumé et savoureux, parfait pour un repas gourmand et raffiné.',
            10,
            'Visuels/Food/Plats/Osso buco à la milanaise.png',
            'Osso buco à la milanaise - Vite & Gourmand',
            'employe_2',
            [],
            'osso_buco'
        );

        $createPlat(
            'Tiramisu',
            'dessert',
            'Mascarpone onctueux alterné avec des biscuits imbibés de café et saupoudré de cacao. Un dessert élégant, fondant et typiquement italien.',
            6,
            'Visuels/Food/Plats/Tiramisu.png',
            'Tiramisu - Vite & Gourmand',
            'employe_1',
            ['gluten', 'lait', 'oeuf'],
            'tiramisu'
        );

        $createPlat(
            'Cannoli siciliens',
            'dessert',
            'Délicieux cannoli siciliens : une coque croustillante dorée, garnie d’une crème de ricotta légèrement sucrée, parfumée aux zestes d’agrumes. Un dessert gourmand et ensoleillé, typique de la Sicile.',
            6,
            'Visuels/Food/Plats/Cannoli siciliens.png',
            'Cannoli siciliens - Vite & Gourmand',
            'employe_1',
            ['gluten', 'lait', 'oeuf'],
            'cannoli'
        );

        $createPlat(
            'Gyozas vapeur aux crevettes',
            'entree',
            'Pâte fine et moelleuse enveloppant des crevettes juteuses et parfumées aux herbes, servis avec une sauce soja légère.',
            25,
            'Visuels/Food/Plats/Gyozas vapeur aux crevettes.png',
            'Gyozas vapeur aux crevettes - Vite & Gourmand',
            'employe_2',
            ['gluten', 'crustace', 'soja'],
            'gyozas'
        );

        $createPlat(
            'Rouleaux de printemps aux crevettes et légumes croquants',
            'entree',
            'Rouleaux de printemps frais, garnis de crevettes juteuses et de légumes croquants, servis avec une sauce légère, pour une entrée colorée, légère et pleine de fraîcheur.',
            25,
            'Visuels/Food/Plats/Rouleaux de printemps aux crevettes et légumes croquants.png',
            'Rouleaux de printemps aux crevettes et légumes croquants - Vite & Gourmand',
            'employe_1',
            ['crustace', 'arachide'],
            'rouleaux_ptps'
        );

        $createPlat(
            'Poulet sauté au gingembre et légumes croquants',
            'plat',
            'Poulet tendre et légumes croquants sautés dans une sauce au gingembre et soja, légèrement relevée. Un plat équilibré et plein de saveurs.',
            26,
            'Visuels/Food/Plats/Poulet sauté au gingembre et légumes croquants.png',
            'Poulet sauté au gingembre et légumes croquants - Vite & Gourmand',
            'employe_1',
            ['gluten', 'soja'],
            'poulet_saute'
        );

        $createPlat(
            'Bœuf sauté au gingembre et légumes croquants',
            'plat',
            'Bœuf tendre sauté au gingembre frais, accompagné de légumes croquants et colorés, pour un plat parfumé, équilibré et gourmand, parfait pour un menu asiatique raffiné et savoureux.',
            26,
            'Visuels/Food/Plats/.png',
            ' - Vite & Gourmand',
            'employe_1',
            ['soja'],
            'boeuf_saute'
        );

        $createPlat(
            'Perles de coco',
            'dessert',
            'Boulettes de riz gluant parfumées à la noix de coco et au sucre, fondantes en bouche, légères et délicatement sucrées.',
            26,
            'Visuels/Food/Plats/Perles de coco.png',
            'Perles de coco - Vite & Gourmand',
            'employe_1',
            [],
            'perles_coco'
        );

        $createPlat(
            'Mochis japonais aux fruits',
            'dessert',
            'Mochis japonais moelleux, garnis de fruits frais et savoureux, pour un dessert léger, coloré et original, parfait pour conclure un repas asiatique sur une note douce et élégante.',
            26,
            'Visuels/Food/Plats/Mochis japonais aux fruits.png',
            'Mochis japonais aux fruits - Vite & Gourmand',
            'employe_1',
            [],
            'mochi'
        );

        $createPlat(
            'Salade de quinoa, légumes grillés et feta',
            'entree',
            'Quinoa léger mélangé à des légumes grillés colorés et fromage feta émietté, assaisonné d’un filet d’huile d’olive et de citron.',
            25,
            'Visuels/Food/Plats/Salade de quinoa, légumes grillés et feta.png',
            'Salade de quinoa, légumes grillés et feta - Vite & Gourmand',
            'employe_1',
            ['lait'],
            'quinoa'
        );

        $createPlat(
            'Roulés de courgette au fromage de chèvre et noix',
            'entree',
            'Roulés de courgette délicats, garnis de fromage de chèvre crémeux et de noix croquantes, pour une entrée légère, colorée et raffinée, idéale pour un menu végétarien ou gourmet.',
            20,
            'Visuels/Food/Plats/Roulés de courgette au fromage de chèvre et noix.png',
            'Roulés de courgette au fromage de chèvre et noix - Vite & Gourmand',
            'employe_1',
            ['lait', 'fruitCoque'],
            'roules_courgettes'
        );

        $createPlat(
            'Curry de légumes au lait de coco',
            'plat',
            'Mélange de légumes de saison mijotés dans une sauce coco parfumée aux épices douces, servi avec du riz basmati.',
            21,
            'Visuels/Food/Plats/Curry de légumes au lait de coco.png',
            'Curry de légumes au lait de coco - Vite & Gourmand',
            'employe_1',
            ['fruitCoque'],
            'curry'
        );

        $createPlat(
            'Tarte tatin de légumes et fromage fondant',
            'dessert',
            'Tarte tatin de légumes caramélisés, surmontée d’un fromage fondant, pour un plat gourmand, coloré et élégant, parfait pour un menu végétarien ou un repas raffiné.',
            21,
            'Visuels/Food/Plats/Tarte tatin de légumes et fromage fondant.png',
            'Tarte tatin de légumes et fromage fondant - Vite & Gourmand',
            'employe_1',
            ['gluten', 'lait'],
            'tatin_legumes'
        );

        $createPlat(
            'Tartare de betterave et avocat',
            'entree',
            'Betteraves fondantes et avocat crémeux assaisonnés de citron et huile d’olive, présenté de façon élégante et colorée.',
            15,
            'Visuels/Food/Plats/Tartare de betterave et avocat.png',
            'Tartare de betterave et avocat - Vite & Gourmand',
            'employe_2',
            [],
            'betterave_avocat'
        );

        $createPlat(
            'Houmous aux poivrons grillés',
            'entree',
            'Purée de pois chiches onctueuse, relevée d’huile d’olive et de paprika, accompagnée de poivrons rouges grillés et légèrement sucrés. Parfait pour éveiller les papilles avec des saveurs du Moyen-Orient.',
            12,
            'Visuels/Food/Plats/Houmous aux poivrons grillés.png',
            'Houmous aux poivrons grillés - Vite & Gourmand',
            'employe_1',
            ['legumineuse'],
            'houmous'
        );

        $createPlat(
            'Ragoût de lentilles aux légumes',
            'plat',
            'Lentilles mijotées avec carottes, poireaux et aromates, riches en protéines et en saveurs, pour un plat complet et réconfortant.',
            15,
            'Visuels/Food/Plats/Ragoût de lentilles aux légumes.png',
            'Ragoût de lentilles aux légumes - Vite & Gourmand',
            'employe_3',
            ['legumineuse'],
            'ragout'
        );

        $createPlat(
            'Pâtes lentilles corail aux tomates confites, épinards et tofu fumé',
            'plat',
            'Pâtes gourmandes de lentilles corail aux tomates confites, épinards frais et tofu fumé, pour un plat vegan, savoureux et parfumé, parfait pour un menu 100% végétal ou un déjeuner léger et coloré.',
            15,
            'Visuels/Food/Plats/Pâtes lentilles corail aux tomates confites, épinards et tofu fumé.png',
            'Pâtes lentilles corail aux tomates confites, épinards et tofu fumé - Vite & Gourmand',
            'employe_3',
            ['legumineuse', 'soja'],
            'pates_lentilles'
        );

        $createPlat(
            'Mousse au chocolat à l’aquafaba',
            'dessert',
            'Mousse légère et aérienne à base de jus de pois chiches et chocolat noir, fondante et étonnamment onctueuse.',
            15,
            'Visuels/Food/Plats/Mousse au chocolat à l’aquafaba brûlée au chocolat.png',
            'Mousse au chocolat à l’aquafaba brûlée au chocolat - Vite & Gourmand',
            'employe_1',
            ['legumineuse'],
            'mousse'
        );

        $createPlat(
            'Tarte tatin purée de cajou abricot',
            'dessert',
            'Tarte tatin aux abricots caramélisés, surmontée d’une purée de cajou onctueuse, pour un dessert vegan, gourmand et raffiné, parfait pour un menu 100 % végétal et fruité.',
            15,
            'Visuels/Food/Plats/Tarte tatin purée de cajou abricot.png',
            'Tarte tatin purée de cajou abricot - Vite & Gourmand',
            'employe_1',
            ['fruitCoque'],
            'tatin_abricot'
        );

        $createPlat(
            'Tartare de saumon aux herbes',
            'entree',
            'Tartare de saumon frais délicatement assaisonné aux herbes fines, pour une entrée légère, parfumée et raffinée, idéale pour un menu de fête ou un repas gourmand et élégant.',
            40,
            'Visuels/Food/Plats/Tartare de saumon aux herbes.png',
            'Tartare de saumon aux herbes - Vite & Gourmand',
            'employe_3',
            ['poisson'],
            'tartare_saumon'
        );

        $createPlat(
            'Ceviche de cabillaud, citron vert et coriandre',
            'entree',
            'Ceviche de cabillaud frais, mariné au citron vert et parsemé de coriandre, pour une entrée légère, acidulée et parfumée, idéale pour un menu poisson frais et élégant.',
            35,
            'Visuels/Food/Plats/Ceviche de cabillaud, citron vert et coriandre.png',
            'Ceviche de cabillaud, citron vert et coriandre - Vite & Gourmand',
            'employe_4',
            ['poisson'],
            'ceviche'
        );

        $createPlat(
            'Filet de dorade au four, purée de céleri',
            'plat',
            'Filet de dorade délicatement rôti au four, accompagné d’une purée onctueuse de céleri, pour un plat raffiné, léger et savoureux, parfait pour un menu poisson élégant et gourmand.',
            60,
            'Visuels/Food/Plats/Filet de dorade au four, purée de céleri.png',
            'Filet de dorade au four, purée de céleri - Vite & Gourmand',
            'employe_1',
            ['poisson'],
            'dorade'
        );

        $createPlat(
            'Assortiment de sorbets maison',
            'dessert',
            'Assortiment de sorbets maison aux fruits frais, légers et rafraîchissants, pour un dessert coloré, fruité et élégant, idéal pour conclure un repas sur une note fraîche et gourmande.',
            70,
            'Visuels/Food/Plats/Assortiment de sorbets maison.png',
            'Assortiment de sorbets maison - Vite & Gourmand',
            'employe_1',
            [],
            'sorbets'
        );

        $createPlat(
            'Velouté de champignons et lait de coco ',
            'entree',
            'Velouté onctueux de champignons, enrichi de lait de coco parfumé aux herbes, pour une entrée chaude, gourmande et réconfortante, parfaite pour un menu vegan ou végétarien élégant.',
            60,
            'Visuels/Food/Plats/Velouté de champignons et lait de coco.png',
            'Velouté de champignons et lait de coco - Vite & Gourmand',
            'employe_1',
            [],
            'veloute_champi'
        );

        $createPlat(
            'Brochettes de légumes grillés avec houmous',
            'entree',
            'Brochettes de légumes grillés colorés, servies avec un houmous crémeux, pour une entrée ou un plat léger, savoureux et plein de fraîcheur, idéal pour un menu vegan ou végétarien gourmand.',
            50,
            'Visuels/Food/Plats/Brochettes de légumes grillés avec houmous.png',
            'Brochettes de légumes grillés avec houmous - Vite & Gourmand',
            'employe_2',
            ['legumineuse'],
            'brochettes'
        );

        $createPlat(
            'Filet de volaille rôti, purée de patate douce au lait d’amande',
            'plat',
            'Filet de volaille rôti, accompagné d’une purée onctueuse de patate douce au lait d’amande, pour un plat gourmand, parfumé et élégant, parfait pour un menu festif ou familial.',
            45,
            'Visuels/Food/Plats/Filet de volaille rôti, purée de patate douce au lait d’amande.png',
            'Filet de volaille rôti, purée de patate douce au lait d’amande - Vite & Gourmand',
            'employe_3',
            ['fruitCoque'],
            'volaille_patatedouce'
        );

        $createPlat(
            'Saumon en papillote aux agrumes et herbes fraîches',
            'plat',
            'Saumon en papillote parfumé aux agrumes et herbes fraîches, cuit doucement pour préserver sa tendreté, pour un plat léger, raffiné et savoureux, parfait pour un menu de fête ou un repas élégant.',
            40,
            'Visuels/Food/Plats/Saumon en papillote aux agrumes et herbes fraîches.png',
            'Saumon en papillote aux agrumes et herbes fraîches - Vite & Gourmand',
            'employe_1',
            ['poisson'],
            'saumon_agrume'
        );

        $createPlat(
            'Tartelette aux fruits sur pâte sans lactose',
            'dessert',
            'Tartelette aux fruits frais, sur une pâte croustillante sans lactose, pour un dessert léger, coloré et gourmand, parfait pour un menu adapté aux intolérances ou un goûter élégant.',
            40,
            'Visuels/Food/Plats/Tartelette aux fruits rouges.png',
            'Tartelette aux fruits rouges - Vite & Gourmand',
            'employe_1',
            ['fruitCoque'],
            'tartelette_fruits'
        );

        $manager->flush();

    }

    public static function getGroups(): array
    {
        return ['plats'];
    }
}