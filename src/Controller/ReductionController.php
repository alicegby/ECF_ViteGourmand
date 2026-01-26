<?php

namespace App\Controller;

use App\Entity\Reduction;
use App\Form\ReductionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ReductionController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function list(): Response
    {
        $reductions = $this->em->getRepository(Reduction::class)->findAll();

        return $this->render('admin/reduction/list.html.twig', [
            'reductions' => $reductions,
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function create(Request $request): Response
    {
        $reduction = new Reduction();
        $form = $this->createForm(ReductionType::class, $reduction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($reduction);
            $this->em->flush();
            $this->addFlash('success', 'Réduction créée avec succès !');

            if ($this->isGranted('ROLE_ADMIN')) {
                    return $this->redirectToRoute('admin_dashboard');
                } else {
                    return $this->redirectToRoute('employe_dashboard');
                }
        }

        return $this->render('admin/reduction/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function edit(Reduction $reduction, Request $request): Response
    {
        $form = $this->createForm(ReductionType::class, $reduction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Réduction modifiée avec succès !');

            if ($this->isGranted('ROLE_ADMIN')) {
                    return $this->redirectToRoute('admin_dashboard');
                } else {
                    return $this->redirectToRoute('employe_dashboard');
                }
        }

        return $this->render('admin/reduction/form.html.twig', [
            'form' => $form->createView(),
            'reduction' => $reduction
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function delete(Reduction $reduction, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reduction->getId(), $request->request->get('_token'))) {
            $this->em->remove($reduction);
            $this->em->flush();
            $this->addFlash('success', 'Réduction supprimée !');
        }

        if ($this->isGranted('ROLE_ADMIN')) {
                    return $this->redirectToRoute('admin_dashboard');
                } else {
                    return $this->redirectToRoute('employe_dashboard');
                }
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function show(Reduction $reduction): Response
    {
        return $this->render('admin/reduction/show.html.twig', [
            'reduction' => $reduction
        ]); 
    }
}