<?php

namespace App\Controller;

use App\Entity\Condition;
use App\Form\ConditionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ConditionController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function list(): Response
    {
        $conditions = $this->em->getRepository(Condition::class)->findAll();
        return $this->render('admin/condition/list.html.twig', [
            'conditions' => $conditions,
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function create(Request $request): Response
    {
        $condition = new Condition();
        $form = $this->createForm(ConditionType::class, $condition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($condition);
            $this->em->flush();
            $this->addFlash('success', 'Condition créée avec succès !');
            return $this->redirectToRoute('condition_list');
        }

        return $this->render('admin/condition/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function edit(Condition $condition, Request $request): Response
    {
        $form = $this->createForm(ConditionType::class, $condition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Condition modifiée avec succès !');
            return $this->redirectToRoute('condition_list');
        }

        return $this->render('admin/condition/form.html.twig', [
            'form' => $form->createView(),
            'condition' => $condition,
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function delete(Condition $condition, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$condition->getId(), $request->request->get('_token'))) {
            $this->em->remove($condition);
            $this->em->flush();
            $this->addFlash('success', 'Condition supprimée !');
        }
        return $this->redirectToRoute('condition_list');
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function show(Condition $condition): Response
    {
        return $this->render('admin/condition/show.html.twig', [
            'condition' => $condition,
        ]);
    }
}