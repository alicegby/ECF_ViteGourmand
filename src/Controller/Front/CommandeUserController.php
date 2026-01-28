<?php

namespace App\Controller\Front;

use App\Entity\Commande;
use App\Entity\CommandeFromage;
use App\Entity\CommandeBoisson;
use App\Entity\CommandeMateriel;
use App\Entity\CommandePersonnel;
use App\Entity\StatutCommande;
use App\Repository\PlatsRepository;
use App\Repository\FromagesRepository;
use App\Repository\BoissonsRepository;
use App\Repository\MaterielRepository;
use App\Repository\PersonnelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
        PersonnelRepository $personnelRepo,
        CsrfTokenManagerInterface $csrfTokenManager
    ): Response {
        $remise = 0;

        if ($request->isMethod('POST')) {
            $postData = $request->request->all();

            // ----- Mise à jour Nombre de personnes -----
            $nbPersonne = (int) ($postData['nbPersonne'] ?? $commande->getNbPersonne());
            $commande->setNbPersonne($nbPersonne);

            // ----- Mise à jour Plats -----
            $platsData = $postData['plats'] ?? [];
            foreach ($commande->getCommandePlats() as $cp) {
                $catId = $cp->getPlat()->getCategory()->getId();
                if (isset($platsData[$catId])) {
                    $plat = $platRepo->find($platsData[$catId]);
                    if ($plat) $cp->setPlat($plat);
                }
            }

            // ----- Mise à jour Options -----
            $this->syncCommandeOptions($commande, $postData['fromages'] ?? [], $commande->getCommandeFromages(), $fromageRepo, CommandeFromage::class, $em);
            $this->syncCommandeOptions($commande, $postData['boissons'] ?? [], $commande->getCommandeBoissons(), $boissonRepo, CommandeBoisson::class, $em);
            $this->syncCommandeOptions($commande, $postData['materiel'] ?? [], $commande->getCommandeMateriels(), $materielRepo, CommandeMateriel::class, $em);

            // ----- Mise à jour Personnel -----
            foreach ($postData['personnel_qty'] ?? [] as $id => $qty) {
                $heures = (int) ($postData['personnel_hours'][$id] ?? 0);
                $cp = $commande->getCommandePersonnels()->filter(fn($c) => $c->getPersonnel()->getId() == $id)->first();

                if (!$cp && ((int)$qty > 0 || $heures > 0)) {
                    $cp = new CommandePersonnel();
                    $cp->setCommande($commande);
                    $cp->setPersonnel($personnelRepo->find($id));
                    $commande->getCommandePersonnels()->add($cp);
                    $em->persist($cp);
                }

                if ($cp) {
                    $cp->setQuantite((int)$qty);
                    $cp->setHeures($heures);
                }
            }

            $em->flush();

            // ----- Validation minimum côté serveur -----
            $errors = [];

            if ($commande->getNbPersonne() < $commande->getMenu()->getNbPersMin()) {
                $errors[] = "Nombre minimum de personnes pour ce menu : {$commande->getMenu()->getNbPersMin()}";
            }

            foreach ($commande->getCommandeFromages() as $cf) {
                if ($cf->getQuantite() < $cf->getFromage()->getMinCommande()) {
                    $errors[] = "{$cf->getFromage()->getTitreFromage()} : minimum {$cf->getFromage()->getMinCommande()}";
                }
            }

            foreach ($commande->getCommandeBoissons() as $cb) {
                if ($cb->getQuantite() < $cb->getBoisson()->getMinCommande()) {
                    $errors[] = "{$cb->getBoisson()->getTitreBoisson()} : minimum {$cb->getBoisson()->getMinCommande()}";
                }
            }

            if (!empty($errors)) {
                if ($request->isXmlHttpRequest()) {
                    return $this->json(['success' => false, 'errors' => $errors]);
                }
                $this->addFlash('error', implode("\n", $errors));
                return $this->redirectToRoute('commande_user_edit', ['id' => $commande->getId()]);
            }

            // ----- Calcul total et remise -----
            $total = $commande->getNbPersonne() * $commande->getMenu()->getPrixParPersonne();
            foreach ($commande->getCommandeFromages() as $cf) $total += $cf->getQuantite() * $cf->getFromage()->getPrixParFromage();
            foreach ($commande->getCommandeBoissons() as $cb) $total += $cb->getQuantite() * $cb->getBoisson()->getPrixParBouteille();
            foreach ($commande->getCommandeMateriels() as $cm) $total += $cm->getQuantite() * $cm->getMateriel()->getPrixPiece();
            foreach ($commande->getCommandePersonnels() as $cp) $total += $cp->getQuantite() * $cp->getHeures() * $cp->getPersonnel()->getPrixHeure();

            $remise = 0;
            if ($commande->getNbPersonne() >= $commande->getMenu()->getNbPersMin() + 5) {
                $remise = $total * 0.10;
                $total -= $remise;
            }

            $commande->setPrixTotal($total);
            $em->persist($commande);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'success' => true,
                    'prixTotal' => round($commande->getPrixTotal(), 2),
                    'remise' => round($remise, 2)
                ]);
            }

            $this->addFlash('success', 'Commande mise à jour avec succès !');
            return $this->redirectToRoute('dashboard_user');
        }

        // ----- GET : préparer données pour Twig -----
        $platsSelectionnesIndex = [];
        foreach ($commande->getCommandePlats() as $cp) {
            $platsSelectionnesIndex[$cp->getPlat()->getCategory()->getId()] = $cp->getPlat();
        }

        $platsParCategorieMenu = [];
        foreach ($commande->getMenu()->getMenuPlats() as $menuPlat) {
            $catId = $menuPlat->getPlat()->getCategory()->getId();
            $platsParCategorieMenu[$catId][] = $menuPlat->getPlat();
        }

        $fromagesData = [];
        foreach ($fromageRepo->findAll() as $fromage) {
            $cf = $commande->getCommandeFromages()->filter(fn($c) => $c->getFromage() === $fromage)->first();
            $fromagesData[] = [
                'fromage' => $fromage,
                'quantite' => $cf ? $cf->getQuantite() : 0
            ];
        }

        $boissonsData = [];
        foreach ($boissonRepo->findAll() as $boisson) {
            $cb = $commande->getCommandeBoissons()->filter(fn($c) => $c->getBoisson() === $boisson)->first();
            $boissonsData[] = [
                'boisson' => $boisson,
                'quantite' => $cb ? $cb->getQuantite() : 0
            ];
        }

        $materielsData = [];
        foreach ($materielRepo->findAll() as $materiel) {
            $cm = $commande->getCommandeMateriels()->filter(fn($c) => $c->getMateriel() === $materiel)->first();
            $materielsData[] = [
                'materiel' => $materiel,
                'quantite' => $cm ? $cm->getQuantite() : 0
            ];
        }

        $personnelsData = [];
        foreach ($personnelRepo->findAll() as $personnel) {
            $cp = $commande->getCommandePersonnels()->filter(fn($c) => $c->getPersonnel() === $personnel)->first();
            $personnelsData[] = [
                'personnel' => $personnel,
                'heures' => $cp ? $cp->getHeures() : 0,
                'quantite' => $cp ? $cp->getQuantite() : 0
            ];
        }

        // ----- Génération du token CSRF pour suppression -----
        $deleteToken = $csrfTokenManager->getToken('delete_commande'.$commande->getId())->getValue();

        return $this->render('user/edit.html.twig', [
            'commande' => $commande,
            'platsSelectionnes' => $platsSelectionnesIndex,
            'platsParCategorie' => $platsParCategorieMenu,
            'fromagesData' => $fromagesData,
            'boissonsData' => $boissonsData,
            'materielsData' => $materielsData,
            'personnelsData' => $personnelsData,
            'remise' => $remise,
            'deleteToken' => $deleteToken,
        ]);
    }

    // --------------------- Méthode utilitaire ---------------------
    private function syncCommandeOptions(
        Commande $commande,
        array $submittedData,
        $existingItems,
        $repo,
        string $className,
        EntityManagerInterface $em
    ): void {
        $processedIds = [];

        foreach ($submittedData as $itemId => $qty) {
            $qty = (int) $qty;
            $processedIds[] = (int)$itemId;

            $existing = $existingItems->filter(function($item) use ($itemId) {
                if (method_exists($item, 'getFromage')) return $item->getFromage() && $item->getFromage()->getId() == $itemId;
                if (method_exists($item, 'getBoisson')) return $item->getBoisson() && $item->getBoisson()->getId() == $itemId;
                if (method_exists($item, 'getMateriel')) return $item->getMateriel() && $item->getMateriel()->getId() == $itemId;
                return false;
            })->first();

            if ($qty === 0) {
                if ($existing) {
                    $em->remove($existing);
                    $existingItems->removeElement($existing);
                }
                continue;
            }

            if (!$existing) {
                $entity = $repo->find($itemId);
                if (!$entity) continue;

                $newItem = new $className();
                $newItem->setCommande($commande);

                if ($className === CommandeFromage::class) $newItem->setFromage($entity);
                if ($className === CommandeBoisson::class) $newItem->setBoisson($entity);
                if ($className === CommandeMateriel::class) $newItem->setMateriel($entity);

                $existingItems->add($newItem);
                $em->persist($newItem);
                $existing = $newItem;
            }

            if (method_exists($existing, 'setQuantite')) $existing->setQuantite($qty);
        }

        foreach ($existingItems as $item) {
            $id = null;
            if (method_exists($item, 'getFromage') && $item->getFromage()) $id = $item->getFromage()->getId();
            if (method_exists($item, 'getBoisson') && $item->getBoisson()) $id = $item->getBoisson()->getId();
            if (method_exists($item, 'getMateriel') && $item->getMateriel()) $id = $item->getMateriel()->getId();

            if ($id !== null && !in_array($id, $processedIds, true)) {
                $em->remove($item);
                $existingItems->removeElement($item);
            }
        }
    }

    // --------------------- Suppression d'une commande ---------------------
    public function delete(
        Commande $commande,
        Request $request,
        EntityManagerInterface $em,
        CsrfTokenManagerInterface $csrfTokenManager
    ): JsonResponse|RedirectResponse {
        
        try {
            // Vérification CSRF
            $token = $request->request->get('_token');
            if (!$csrfTokenManager->isTokenValid(new CsrfToken('delete_commande'.$commande->getId(), $token))) {
                return $this->json(['success' => false, 'message' => 'Token CSRF invalide'], 403);
            }

            // Vérification utilisateur
            if ($commande->getClient() !== $this->getUser()) {
                return $this->json(['success' => false, 'message' => 'Vous ne pouvez pas supprimer cette commande.'], 403);
            }

            // Récupération du statut "Annulée"
            $statutAnnule = $em->getRepository(\App\Entity\StatutCommande::class)
                            ->findOneBy(['libelle' => 'Annulée']);

            if (!$statutAnnule) {
                return $this->json(['success' => false, 'message' => 'Le statut "Annulée" n’existe pas en base.'], 404);
            }

            $commande->setStatutCommande($statutAnnule);
            $em->flush();

            return $this->json(['success' => true]);
            
        } catch (\Throwable $e) {
            // Toujours renvoyer JSON même en erreur
            return $this->json([
                'success' => false,
                'message' => 'Erreur serveur : ' . $e->getMessage()
            ], 500);
        }
    }
}