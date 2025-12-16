<?php

namespace App\Controller;

use App\Entity\CategoryCheese;
use App\Form\CategoryCheeseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CategoryCheeseController extends AbstractController {
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em =$em;
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function list(): Response {
        $categories = $this->em->getRepository(CategoryCheese::class)->findAll();
        return $this->render('admin/categorycheese/list.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function create(Request $request): Response {
        $category = new CategoryCheese();
        $form = $this->createForm(CategoryCheeseType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($category);
            $this->em->flush();
            $this->addFlash('success', 'Catégorie créée !');
            return $this->redirectToRoute('categorycheese_list');
        }
        return $this->render('admin/categorycheese/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function edit(CategoryCheese $category, Request $request): Response
    {
        $form = $this->createForm(CategoryCheeseType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Catégorie modifiée !');

            return $this->redirectToRoute('categorycheese_list');
        }

        return $this->render('admin/categorycheese/form.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function delete(CategoryCheese $category, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $this->em->remove($category);
            $this->em->flush();
            $this->addFlash('success', 'Catégorie supprimée !');
        }

        return $this->redirectToRoute('categorycheese_list');
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function show(CategoryCheese $category): Response
    {
        return $this->render('admin/categorycheese/show.html.twig', [
            'category' => $category
        ]);
    }
}