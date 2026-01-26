<?php

namespace App\Controller; 

use App\Entity\CategoryDrink;
use App\Form\CategoryDrinkType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CategoryDrinkController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function list(): Response
    {
        $categories = $this->em->getRepository(CategoryDrink::class)->findAll();

        return $this->render('admin/categorydrink/list.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function create(Request $request): Response
    {
        $category = new CategoryDrink();
        $form = $this->createForm(CategoryDrinkType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($category);
            $this->em->flush();
            $this->addFlash('success', 'Catégorie créée !');

            if ($this->isGranted('ROLE_ADMIN')) {
                    return $this->redirectToRoute('admin_dashboard');
                } else {
                    return $this->redirectToRoute('employe_dashboard');
                }
        }

        return $this->render('admin/categorydrink/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function edit(CategoryDrink $category, Request $request): Response
    {
        $form = $this->createForm(CategoryDrinkType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Catégorie modifiée !');

            if ($this->isGranted('ROLE_ADMIN')) {
                    return $this->redirectToRoute('admin_dashboard');
                } else {
                    return $this->redirectToRoute('employe_dashboard');
                }
        }

        return $this->render('admin/categorydrink/form.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function delete(CategoryDrink $category, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $this->em->remove($category);
            $this->em->flush();
            $this->addFlash('success', 'Catégorie supprimée !');
        }

        if ($this->isGranted('ROLE_ADMIN')) {
                    return $this->redirectToRoute('admin_dashboard');
                } else {
                    return $this->redirectToRoute('employe_dashboard');
                }
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function show(CategoryDrink $category): Response
    {
        return $this->render('admin/categorydrink/show.html.twig', [
            'category' => $category
        ]);
    }
}