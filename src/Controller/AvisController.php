<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Form\AvisType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AvisController extends AbstractController {
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function list(): Response {
        $avis = $this->em->getRepository(Avis::class)->findAll();
        return $this->render('admin/avis.list.html.twig', [
            'avis' => $avis,
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function edit(Avis $avis, Request $request): Response {
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Avis mis à jour !');

            return $this->redirectToRoute('avis_list');
        }

        return $this->render('admin/avis/form.html.twig', [
            'form' => $form->createView(),
            'avis' => $avis
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function show(Avis $avis): Response
    {
        return $this->render('admin/avis/show.html.twig', [
            'avis' => $avis
        ]);
    }
}