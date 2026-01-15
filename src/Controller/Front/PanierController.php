<?php

namespace App\Controller\Front;

use App\Repository\MenuRepository;
use App\Repository\PlatsRepository;
use App\Repository\FromagesRepository;
use App\Repository\BoissonsRepository;
use App\Repository\MaterielRepository;
use App\Repository\PersonnelRepository;
use App\Repository\HoraireRepository;
use App\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;

class PanierController extends AbstractController
{
    // PAGE PANIER
    #[IsGranted('ROLE_USER')]
    public function index(
        Request $request,
        MenuRepository $menuRepo,
        PlatsRepository $platRepo,
        FromagesRepository $fromageRepo,
        BoissonsRepository $boissonRepo,
        MaterielRepository $materielRepo,
        PersonnelRepository $personnelRepo,
        HoraireRepository $horaireRepository
    ): Response {

        $session = $request->getSession();

        $menuData = [];
        $menuConditions = [];
        $fromagesData = [];
        $boissonsData = [];
        $materielData = [];
        $personnelData = [];

        $totalMenus = 0;
        $totalExtras = 0;

        // ===== MENUS =====
        foreach ($session->get('menu_add_to_cart', []) as $item) {
            $menu = $menuRepo->find($item['menuId']);
            if (!$menu) continue;

            $plats = [];
            foreach ($item['plats'] as $platId) {
                $plat = $platRepo->find($platId);
                if ($plat) $plats[] = $plat;
            }

            $quantity = (int) $item['quantity'];
            $subtotal = $menu->getPrixParPersonne() * $quantity;
            $totalMenus += $subtotal;

            $menuData[] = [
                'menu' => $menu,
                'quantity' => $quantity,
                'plats' => $plats,
                'subtotal' => $subtotal,
            ];

            // CALCUL DES CONDITIONS
            $conditions = $menu->getConditions() ?? [];
            $messagesCommande = [];
            $messagesAnnulation = [];
            $dateMin = new \DateTime('today');

            // MINIMUM DE COMMANDE
            $minPers = $menu->getNbPersMin();
            $messagesCommande[] = "Quantité minimum pour commande : $minPers menus.";

            foreach ($conditions as $condition) {
                $libelle = strtolower($condition->getLibelle());

                // ANNULATION
                if (str_contains($libelle, 'annulation')) {
                    if (preg_match('/(\d+)\s*(jour|jours|h|heure|heures|semaine|semaines)/', $libelle, $m)) {
                        $valeur = (int) $m[1];
                        $unite = $m[2];
                        if ($valeur > 1 && !str_ends_with($unite, 's')) $unite .= 's';
                        $messagesAnnulation[] = "Annulation possible au minimum $valeur $unite à l'avance.";
                    } else {
                        $messagesAnnulation[] = ucfirst($condition->getLibelle());
                    }
                    continue;
                }

                // DÉLAI MINIMUM COMMANDE
                if (preg_match('/(\d+)\s*(jour|jours|h|heure|heures|semaine|semaines|mois)/', $libelle, $m)) {
                    $valeur = (int) $m[1];
                    $unite = $m[2];
                    $dateMini = clone $dateMin;
                    if (str_contains($unite, 'jour')) {
                        $dateMini->modify("+$valeur days");
                    } elseif (str_contains($unite, 'semaine')) {
                        $dateMini->modify("+".($valeur*7)." days");
                    } elseif (str_contains($unite, 'mois')) {
                        $dateMini->modify("+$valeur months");
                    } else {
                        $dateMini->modify("+$valeur hours");
                    }
                    if ($dateMini > $dateMin) {
                        $dateMin = $dateMini;
                    }
                    $messagesCommande[] = "Ce menu doit être commandé au minimum $valeur $unite à l'avance.";
                }
            }

            // Stocker dateMin comme chaîne pour Twig (évite le non-scalar)
            $menuConditions[$menu->getId()] = [
                'messagesCommande' => $messagesCommande,
                'messagesAnnulation' => $messagesAnnulation,
                'dateMin' => $dateMin->format('Y-m-d'),
            ];
        }

        // ===== FROMAGES =====
        foreach ($session->get('fromages_selections', []) as $sel) {
            $fromage = $fromageRepo->find($sel['id']);
            if (!$fromage) continue;

            $subtotal = $fromage->getPrixParFromage() * $sel['qty'];
            $totalExtras += $subtotal;

            $fromagesData[] = [
                'item' => $fromage,
                'qty' => $sel['qty'],
                'subtotal' => $subtotal,
            ];
        }

        // ===== BOISSONS =====
        foreach ($session->get('boissons_selections', []) as $sel) {
            $boisson = $boissonRepo->find($sel['id']);
            if (!$boisson) continue;

            $subtotal = $boisson->getPrixParBouteille() * $sel['qty'];
            $totalExtras += $subtotal;

            $boissonsData[] = [
                'item' => $boisson,
                'qty' => $sel['qty'],
                'subtotal' => $subtotal,
            ];
        }

        // ===== MATÉRIEL =====
        foreach ($session->get('materiel_selections', []) as $sel) {
            $materiel = $materielRepo->find($sel['id']);
            if (!$materiel) continue;

            $subtotal = $materiel->getPrixPiece() * $sel['qty'];
            $totalExtras += $subtotal;

            $materielData[] = [
                'item' => $materiel,
                'qty' => $sel['qty'],
                'subtotal' => $subtotal,
            ];
        }

        // ===== PERSONNEL =====
        foreach ($session->get('personnel_selections', []) as $sel) {
            $pers = $personnelRepo->find($sel['id']);
            if (!$pers) continue;

            $subtotal = $pers->getPrixHeure() * $sel['qty'];
            $totalExtras += $subtotal;

            $personnelData[] = [
                'item' => $pers,
                'qty' => $sel['qty'],
                'subtotal' => $subtotal,
            ];
        }

        // ===== LIVRAISON =====
        $deliveryFee = 0;
        $deliveryMessage = null;
        /** @var Utilisateur $user */
        $user = $this->getUser();
        $villeUser = strtolower($user->getVille() ?? '');

        $cityDistances = [
            'ambares-et-lagrave'=>15,'ambes'=>20,'artigues-pres-bordeaux'=>8,'bassens'=>11,
            'begles'=>5,'blanquefort'=>11,'bouliac'=>9,'bruges'=>9,'carbon-blanc'=>11,'cenon'=>9,
            'eysines'=>11,'floirac'=>9,'gradignan'=>9,'le bouscat'=>7,'le haillan'=>9,'le taillan-medoc'=>13,
            'lormont'=>9,'martignas-sur-jalle'=>14,'merignac'=>5,'parempuye'=>18,'pessac'=>8,
            'saint-aubin-de-medoc'=>20,'saint-louis-de-montferrand'=>18,'saint-vincent-de-paul'=>20,'talence'=>4,'villenave-d-ornon'=>7,
        ];

        if (!isset($cityDistances[$villeUser])) {
            $deliveryMessage = "Nous ne livrons pas en dehors de l'agglomération de Bordeaux.";
        } else {
            $km = $cityDistances[$villeUser];
            $deliveryFee = 5 + ($km * 0.59);
        }

        // ===== REDUCTION =====
        $totalPersons = 0;
        $minPersonsRequired = 0;
        foreach ($menuData as $menuItem) {
            $totalPersons += $menuItem['quantity'];
            $minPersonsRequired += $menuItem['menu']->getNbPersMin();
        }

        $reductionMenus = 0;
        if ($totalPersons >= ($minPersonsRequired + 5)) {
            $reductionMenus = $totalMenus * 0.10;
        }

        // ===== TOTAL GENERAL =====
        $totalGeneral = ($totalMenus - $reductionMenus) + $totalExtras + $deliveryFee;

        // ===== HEURES MIN/MAX POUR TWIG =====
        $earliestDate = new \DateTime('today');
        foreach ($menuConditions as $cond) {
            $condDate = new \DateTime($cond['dateMin']);
            if ($condDate > $earliestDate) $earliestDate = $condDate;
        }
        $earliestDateString = $earliestDate->format('Y-m-d');

        $jourSemaine = (int) $earliestDate->format('N');
        $horaireDuJour = $horaireRepository->findOneBy(['jour' => $jourSemaine]);

        $heureMin = $horaireDuJour ? $horaireDuJour->getHeureOuverture()->format('H:i') : '10:00';
        $heureMax = $horaireDuJour ? $horaireDuJour->getHeureFermeture()->format('H:i') : '18:00';

        $horairesCollection = [];
        foreach ($horaireRepository->findAllOrdered() as $horaire) {
            $horairesCollection[$horaire->getJour()] = [
                'min' => $horaire->getHeureOuverture()->format('H:i'),
                'max' => $horaire->getHeureFermeture()->format('H:i')
            ];
        }

        $session->set('menuConditions', $menuConditions);

        return $this->render('front/panier.html.twig', [
            'menuData' => $menuData,
            'menuConditions' => $menuConditions,
            'fromagesData' => $fromagesData,
            'boissonsData' => $boissonsData,
            'materielData' => $materielData,
            'personnelData' => $personnelData,
            'totalGeneral' => $totalGeneral,
            'deliveryFee' => $deliveryFee,
            'deliveryMessage' => $deliveryMessage,
            'reductionMenus' => $reductionMenus,
            'heureMin' => $heureMin,
            'heureMax' => $heureMax,
            'earliestDate' => $earliestDateString,
            'horairesCollection' => $horairesCollection,
        ]);
    }

    // COMPTEUR PANIER
    public function count(Request $request): JsonResponse
    {
        $session = $request->getSession();
        $menuCount = array_sum(array_map(fn($item) => $item['quantity'], $session->get('menu_add_to_cart', [])));
        $fromageCount = array_sum(array_map(fn($item) => $item['qty'], $session->get('fromages_selections', [])));
        $boissonCount = array_sum(array_map(fn($item) => $item['qty'], $session->get('boissons_selections', [])));
        $materielCount = array_sum(array_map(fn($item) => $item['qty'], $session->get('materiel_selections', [])));
        $personnelCount = array_sum(array_map(fn($item) => $item['qty'], $session->get('personnel_selections', [])));

        $total = $menuCount + $fromageCount + $boissonCount + $materielCount + $personnelCount;

        return new JsonResponse(['total' => $total]);
    }

    // RESET PANIER
    #[IsGranted('ROLE_USER')]
    public function reset(Request $request): JsonResponse
    {
        $session = $request->getSession();
        $session->remove('menu_add_to_cart');
        $session->remove('fromages_selections');
        $session->remove('boissons_selections');
        $session->remove('materiel_selections');
        $session->remove('personnel_selections');

        return new JsonResponse(['success' => true]);
    }

    // VALIDATION COMMANDE
    #[IsGranted('ROLE_USER')]
    public function validation(
        Request $request,
        MenuRepository $menuRepo,
        PlatsRepository $platRepo,
        FromagesRepository $fromageRepo,
        BoissonsRepository $boissonRepo,
        MaterielRepository $materielRepo,
        PersonnelRepository $personnelRepo,
        EntityManagerInterface $entityManager
    ): Response {
        $session = $request->getSession();

        // ===== Récupération du panier et options =====
        $menuAddToCart = $session->get('menu_add_to_cart', []);
        $fromagesSelections = $session->get('fromages_selections', []);
        $boissonsSelections = $session->get('boissons_selections', []);
        $materielSelections = $session->get('materiel_selections', []);
        $personnelSelections = $session->get('personnel_selections', []);

        // ===== Calcul du nombre total de personnes =====
        $totalPersons = 0;
        foreach ($menuAddToCart as $item) {
            $totalPersons += (int) $item['quantity'];
        }

        if ($totalPersons === 0) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('panier');
        }

        // ===== Récupération de l’utilisateur =====
        /** @var Utilisateur $user */
        $user = $this->getUser();

        // ===== CALCUL DES FRAIS DE LIVRAISON =====
        $fraisLivraison = 0;

        $villeUser = strtolower($user->getVille() ?? '');

        $cityDistances = [
            'ambares-et-lagrave'=>15,'ambes'=>20,'artigues-pres-bordeaux'=>8,'bassens'=>11,
            'begles'=>5,'blanquefort'=>11,'bouliac'=>9,'bruges'=>9,'carbon-blanc'=>11,'cenon'=>9,
            'eysines'=>11,'floirac'=>9,'gradignan'=>9,'le bouscat'=>7,'le haillan'=>9,'le taillan-medoc'=>13,
            'lormont'=>9,'martignas-sur-jalle'=>14,'merignac'=>5,'parempuye'=>18,'pessac'=>8,
            'saint-aubin-de-medoc'=>20,'saint-louis-de-montferrand'=>18,'saint-vincent-de-paul'=>20,
            'talence'=>4,'villenave-d-ornon'=>7,
        ];

        if (isset($cityDistances[$villeUser])) {
            $km = $cityDistances[$villeUser];
            $fraisLivraison = 5 + ($km * 0.59);
        } else {
            $km = 0;
        }

        // ===== Récupération de la date et heure de livraison =====
        $dateLivraisonInput = $request->request->get('dateLivraison');
        $heureLivraisonInput = $request->request->get('heureLivraison');

        if (!$dateLivraisonInput || !$heureLivraisonInput) {
            $this->addFlash('error', 'Merci de renseigner la date et l’heure de livraison.');
            return $this->redirectToRoute('panier');
        }

        try {
            $datetimeLivraison = new \DateTime($dateLivraisonInput . ' ' . $heureLivraisonInput);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Format de date ou d’heure invalide.');
            return $this->redirectToRoute('panier');
        }

        // ===== Création de la commande =====
        $commande = new \App\Entity\Commande();
        $commande->setClient($user);
        $commande->setDateCommande(new \DateTime());
        $commande->setDateModif(new \DateTime());
        $commande->setAdresseLivraison($user->getAdressePostale() ?? '');
        $commande->setCodePostalLivraison($user->getCodePostal() ?? '');
        $commande->setVilleLivraison($user->getVille() ?? '');
        $commande->setDateLivraison($datetimeLivraison);
        $commande->setHeureLivraison($datetimeLivraison);
        $commande->setNbPersonne($totalPersons);
        $commande->setNumeroCommande('CMD-' . strtoupper(uniqid()));

        // ===== Calcul du prix des menus =====
        $prixMenus = 0;
        $menusNoms = [];
        foreach ($menuAddToCart as $item) {
            $menu = $menuRepo->find($item['menuId']);
            if (!$menu) continue;

            $menusNoms[] = $menu->getNom();
            $prixMenus += $menu->getPrixParPersonne() * $item['quantity'];
        }
        $commande->setMenu($menu);
        $commande->setPrixMenu($prixMenus);

        // ===== Calcul du montant des options ==== //
        $montantOptions = 0;

        foreach ($fromagesSelections as $sel) {
            $fromage = $fromageRepo->find($sel['id']);
            if ($fromage) {
                $montantOptions += $fromage->getPrixParFromage() * $sel['qty'];

                $commandeFromage = new \App\Entity\CommandeFromage();
                $commandeFromage->setCommande($commande); // lien avec la commande
                $commandeFromage->setFromage($fromage);
                $commandeFromage->setQuantite($sel['qty']);
                $commandeFromage->setPrixUnitaire($fromage->getPrixParFromage());

                $entityManager->persist($commandeFromage);
            }
        }

        foreach ($boissonsSelections as $sel) {
            $boisson = $boissonRepo->find($sel['id']);
            if ($boisson) {
                $montantOptions += $boisson->getPrixParBouteille() * $sel['qty'];

                $commandeBoisson = new \App\Entity\CommandeBoisson();
                $commandeBoisson->setCommande($commande);
                $commandeBoisson->setBoisson($boisson);
                $commandeBoisson->setQuantite($sel['qty']);
                $commandeBoisson->setPrixUnitaire($boisson->getPrixParBouteille());

                $entityManager->persist($commandeBoisson);
            }
        }

        foreach ($materielSelections as $sel) {
            $materiel = $materielRepo->find($sel['id']);
            if ($materiel) {
                $montantOptions += $materiel->getPrixPiece() * $sel['qty'];

                $commandeMateriel = new \App\Entity\CommandeMateriel();
                $commandeMateriel->setCommande($commande);
                $commandeMateriel->setMateriel($materiel);
                $commandeMateriel->setQuantite($sel['qty']);
                $commandeMateriel->setPrixUnitaire($materiel->getPrixPiece());

                $entityManager->persist($commandeMateriel);
            }
        }

        foreach ($personnelSelections as $sel) {
            $pers = $personnelRepo->find($sel['id']);
            if ($pers) {
                $montantOptions += $pers->getPrixHeure() * $sel['qty'];

                $commandePersonnel = new \App\Entity\CommandePersonnel();
                $commandePersonnel->setCommande($commande);
                $commandePersonnel->setPersonnel($pers);
                $commandePersonnel->setHeures($sel['qty']); // correspond au nombre d'heures
                $commandePersonnel->setPrixUnitaire($pers->getPrixHeure());

                $entityManager->persist($commandePersonnel);
            }
        }

        $commande->setMontantOptions($montantOptions);

        // ===== Calcul de la réduction =====
        $minPersonsRequired = 0;

        foreach ($menuAddToCart as $item) {
            $menu = $menuRepo->find($item['menuId']);
            if (!$menu) continue;

            $minPersonsRequired += $menu->getNbPersMin();
        }

        $montantReduction = 0;
        if ($totalPersons >= ($minPersonsRequired + 5)) {
            $montantReduction = $prixMenus * 0.10;
        }

        $commande->setMontantReduction($montantReduction);

        // ===== Frais Livraison =====
        $commande->setDistanceKm($km);
        $commande->setFraisLivraison($fraisLivraison);
        
        // ===== Calcul du prix total =====
        $prixTotal = ($prixMenus - $montantReduction) + $montantOptions + $fraisLivraison;

        $commande->setPrixTotal($prixTotal);

        // ===== Statut commande =====
        $statutEnAttente = $entityManager
            ->getRepository(\App\Entity\StatutCommande::class)
            ->findOneBy(['libelle' => 'En attente']);

        if (!$statutEnAttente) {
            throw new \RuntimeException('Le statut "En attente" est introuvable en base !');
        }

        $commande->setStatutCommande($statutEnAttente);

        // ===== Enregistrement de la commande =====
        $entityManager->persist($commande);

        foreach ($menuAddToCart as $item) {
            foreach ($item['plats'] as $platId) {
                $plat = $platRepo->find($platId);
                if (!$plat) continue;

                $commandePlat = new \App\Entity\CommandePlat();
                $commandePlat->setCommande($commande);
                $commandePlat->setPlat($plat);
                $commandePlat->setCategoryPlat($plat->getCategory()); // si tu as un lien CategoryPlat dans Plat

                $entityManager->persist($commandePlat);
            }
        }

        $entityManager->flush();

        // ===== Reset panier =====
        $session->remove('menu_add_to_cart');
        $session->remove('fromages_selections');
        $session->remove('boissons_selections');
        $session->remove('materiel_selections');
        $session->remove('personnel_selections');

        $this->addFlash('success', 'Commande envoyée !');
        return $this->redirectToRoute('dashboard_user');
    }
}