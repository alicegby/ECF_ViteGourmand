<?php

namespace App\Controller;

use App\Entity\StatutCommande;
use App\Form\StatutCommandeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class StatutCommandeController extends AbstractController {
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em =$em;
    }
 
    #[IsGranted('ROLE_EMPLOYE')]
    public function list(): Response {
        $statutsCommande = $this->em->getRepository(StatutCommande::class)->findAll();
        return $this->render('admin/statutcommande/list.html.twig', [
            'statutsCommande' => $statutsCommande,
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function create(Request $request): Response {
        $statutCommande = new StatutCommande();
        $form = $this->createForm(StatutCommandeType::class, $statutCommande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($statutCommande);
            $this->em->flush();
            $this->addFlash('success', 'Statut créé !');
            return $this->redirectToRoute('statutcommande_list');
        }
        return $this->render('admin/statutcommande/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function edit(StatutCommande $statutCommande, Request $request): Response
    {
        $form = $this->createForm(StatutCommandeType::class, $statutCommande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Statut modifié !');

            return $this->redirectToRoute('statutcommande_list');
        }

        return $this->render('admin/statutcommande/form.html.twig', [
            'form' => $form->createView(),
            'statutCommande' => $statutCommande
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function delete(StatutCommande $statutCommande, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $statutCommande->getId(), $request->request->get('_token'))) {
            $this->em->remove($statutCommande);
            $this->em->flush();
            $this->addFlash('success', 'Statut supprimé !');
        }

        return $this->redirectToRoute('statutcommande_list');
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function show(StatutCommande $statutCommande): Response
    {
        return $this->render('admin/statutcommande/show.html.twig', [
           'statutCommande' => $statutCommande
        ]);
    }
}