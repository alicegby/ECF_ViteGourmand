<?php

namespace App\Controller\Front;

use App\Entity\Commande;
use App\Repository\PlatsRepository;
use App\Repository\FromagesRepository;
use App\Repository\BoissonsRepository;
use App\Repository\MaterielRepository;
use App\Repository\PersonnelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommandeUserController extends AbstractController
{
    public function edit(
        Commande $commande,
        Request $request,
        EntityManagerInterface $em,
        PlatsRepository $platRepo,
        FromagesRepository $fromageRepo,
        BoissonsRepository $boissonRepo,
        MaterielRepository $materielRepo,
        PersonnelRepository $personnelRepo
    ): Response {
        $remise = 0;

        if ($request->isMethod('POST')) {

            // Tout le POST en tableau
            $postData = $request->request->all();

            // 1️⃣ Modifier le nombre de personnes
            $nbPersonne = (int) ($postData['nbPersonne'] ?? $commande->getNbPersonne());
            $commande->setNbPersonne($nbPersonne);

            // 2️⃣ Modifier les plats existants
            $platsData = $postData['plats'] ?? [];
            foreach ($commande->getCommandePlats() as $commandePlat) {
                $catId = $commandePlat->getPlat()->getCategory()->getId();
                if (isset($platsData[$catId])) {
                    $plat = $platRepo->find($platsData[$catId]);
                    if ($plat) {
                        $commandePlat->setPlat($plat);
                    }
                }
            }

            // 3️⃣ Modifier les options existantes
            foreach ($commande->getCommandeFromages() as $cf) {
                $qty = (int) ($postData['fromages'][$cf->getFromage()->getId()] ?? $cf->getQuantite());
                $cf->setQuantite($qty);
            }

            foreach ($commande->getCommandeBoissons() as $cb) {
                $qty = (int) ($postData['boissons'][$cb->getBoisson()->getId()] ?? $cb->getQuantite());
                $cb->setQuantite($qty);
            }

            foreach ($commande->getCommandeMateriels() as $cm) {
                $qty = (int) ($postData['materiel'][$cm->getMateriel()->getId()] ?? $cm->getQuantite());
                $cm->setQuantite($qty);
            }

            foreach ($commande->getCommandePersonnels() as $cp) {
                $qty = (int) ($postData['personnel'][$cp->getPersonnel()->getId()] ?? $cp->getHeures());
                $cp->setHeures($qty);
            }

            // 4️⃣ Recalculer le prix total
            $total = $commande->getNbPersonne() * $commande->getMenu()->getPrixParPersonne();

            // Ajouter les options
            foreach ($commande->getCommandeFromages() as $cf) {
                $total += $cf->getQuantite() * $cf->getFromage()->getPrixParFromage();
            }
            foreach ($commande->getCommandeBoissons() as $cb) {
                $total += $cb->getQuantite() * $cb->getBoisson()->getPrixParBouteille();
            }
            foreach ($commande->getCommandeMateriels() as $cm) {
                $total += $cm->getQuantite() * $cm->getMateriel()->getPrixPiece();
            }
            foreach ($commande->getCommandePersonnels() as $cp) {
                $total += $cp->getHeures() * $cp->getPersonnel()->getPrixHeure();
            }

            // 🔹 Calcul de la remise automatique
            $nbPersMin = $commande->getMenu()->getNbPersMin();
            $nbPers = $commande->getNbPersonne();
            $remise = 0;

            if ($nbPers >= $nbPersMin + 5) {
                $remise = $total * 0.10; // 10% de remise
                $total -= $remise;
            }

            $commande->setPrixTotal($total);

            // 5️⃣ Persister et flush
            $em->persist($commande);
            $em->flush();

            $this->addFlash('success', 'Commande mise à jour avec succès !');

            return $this->redirectToRoute('commande_user_edit', ['id' => $commande->getId()]);
        }

        $platsSelectionnesIndex = [];
        foreach ($commande->getCommandePlats() as $cp) {
            $catId = $cp->getPlat()->getCategory()->getId();
            $platsSelectionnesIndex[$catId] = $cp->getPlat();
        }

        // Récupérer uniquement les plats du menu
        $platsParCategorieMenu = [];
        foreach ($commande->getMenu()->getMenuPlats() as $menuPlat) {
            $plat = $menuPlat->getPlat();
            $catId = $plat->getCategory()->getId();
            $platsParCategorieMenu[$catId][] = $plat;
        }

        // Récupérer toutes les options
        $fromages = $fromageRepo->findAll();
        $boissons = $boissonRepo->findAll();
        $materiels = $materielRepo->findAll();
        $personnels = $personnelRepo->findAll();

        // Construire un tableau associatif avec la quantité déjà choisie dans la commande
        $fromagesData = [];
        foreach ($fromages as $fromage) {
            $cf = $commande->getCommandeFromages()->filter(fn($c) => $c->getFromage() === $fromage)->first();
            $fromagesData[] = [
                'fromage' => $fromage,
                'quantite' => $cf ? $cf->getQuantite() : 0
            ];
        }

        $boissonsData = [];
        foreach ($boissons as $boisson) {
            $cb = $commande->getCommandeBoissons()->filter(fn($c) => $c->getBoisson() === $boisson)->first();
            $boissonsData[] = [
                'boisson' => $boisson,
                'quantite' => $cb ? $cb->getQuantite() : 0
            ];
        }

        $materielsData = [];
        foreach ($materiels as $materiel) {
            $cm = $commande->getCommandeMateriels()->filter(fn($c) => $c->getMateriel() === $materiel)->first();
            $materielsData[] = [
                'materiel' => $materiel,
                'quantite' => $cm ? $cm->getQuantite() : 0
            ];
        }

        $personnelsData = [];
        foreach ($personnels as $personnel) {
            $cp = $commande->getCommandePersonnels()->filter(fn($c) => $c->getPersonnel() === $personnel)->first();
            $personnelsData[] = [
                'personnel' => $personnel,
                'heures' => $cp ? $cp->getHeures() : 0
            ];
        }

        // 6️⃣ Rendu du formulaire
        return $this->render('user/edit.html.twig', [
            'commande' => $commande,
            'platsSelectionnes' => $platsSelectionnesIndex,
            'platsParCategorie' => $platsParCategorieMenu,
            'fromagesData' => $fromagesData,
            'boissonsData' => $boissonsData,
            'materielsData' => $materielsData,
            'personnelsData' => $personnelsData,
            'remise' => $remise,
        ]);
    }
}