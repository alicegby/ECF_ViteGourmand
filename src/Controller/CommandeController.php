<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Form\CommandeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CommandeController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function list(): Response
    {
        $commandes = $this->em->getRepository(Commande::class)->findAll();

        return $this->render('admin/commande/list.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function edit(Commande $commande, Request $request): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Commande mise à jour !');

            return $this->redirectToRoute('commande_list');
        }

        return $this->render('admin/commande/form.html.twig', [
            'form' => $form->createView(),
            'commande' => $commande
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function show(Commande $commande): Response
    {
        return $this->render('admin/commande/show.html.twig', [
            'commande' => $commande
        ]);
    }
}