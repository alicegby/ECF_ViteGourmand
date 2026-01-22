<?php

namespace App\Controller\Front;

use App\Entity\Commande;
use App\Entity\CommandePlat;
use App\Entity\CommandeFromage;
use App\Entity\CommandeBoisson;
use App\Entity\CommandeMateriel;
use App\Entity\CommandePersonnel;
use App\Entity\Plats;
use App\Entity\Fromages;
use App\Entity\Boissons;
use App\Entity\Materiel;
use App\Entity\Personnel;
use App\Form\CommandeEditType;
use App\Entity\StatutCommande;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CommandeUserController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    public function annuler(Commande $commande, EntityManagerInterface $em, Request $request): Response
    {
        $statutAnnulee = $em->getRepository(StatutCommande::class)
            ->findOneBy(['libelle' => 'Annulée']);
        if (!$statutAnnulee) throw $this->createNotFoundException('Statut "Annulée" introuvable.');

        $commande->setStatutCommande($statutAnnulee);
        $commande->setDateModif(new \DateTime());
        $em->flush();

        if ($request->isXmlHttpRequest()) {
            return $this->json([
                'success' => true,
                'message' => 'Commande annulée avec succès',
                'commandeId' => $commande->getId(),
            ]);
        }

        $this->addFlash('success', 'Commande annulée avec succès.');
        return $this->redirectToRoute('dashboard_user');
    }

    #[IsGranted('ROLE_USER')]
    public function edit(Commande $commande, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CommandeEditType::class, $commande, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $request->isMethod('POST')) {
            // --- NB PERSONNES ---
            $commande->setNbPersonne($form->get('nbPersonne')->getData());

            // --- Plats ---
            foreach ($commande->getCommandePlats() as $cp) $em->remove($cp);
            $menuPlatsIds = $request->request->all('menu_plats') ?? [];
            foreach ($menuPlatsIds as $platId) {
                $plat = $em->getRepository(Plats::class)->find($platId);
                if ($plat) {
                    $cp = new CommandePlat();
                    $cp->setCommande($commande)->setPlat($plat);
                    $em->persist($cp);
                }
            }

            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->json(['success' => true, 'message' => 'Commande mise à jour avec succès']);
            }

            $this->addFlash('success', 'Commande mise à jour avec succès');
            return $this->redirectToRoute('dashboard_user');
        }

        return $this->render('user/edit.html.twig', [
            'commande' => $commande,
            'form' => $form->createView(),
            'menu' => $commande->getMenu(),
        ]);
    }
}