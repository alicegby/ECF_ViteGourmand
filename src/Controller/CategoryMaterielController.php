<?php

namespace App\Controller;

use App\Entity\CategoryMateriel;
use App\Form\CategoryMaterielType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CategoryMaterielController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function list(): Response
    {
        $categories = $this->em->getRepository(CategoryMateriel::class)->findAll();

        return $this->render('admin/categorymateriel/list.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function create(Request $request): Response
    {
        $category = new CategoryMateriel();
        $form = $this->createForm(CategoryMaterielType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($category);
            $this->em->flush();
            $this->addFlash('success', 'Catégorie créée !');

            return $this->redirectToRoute('categorymateriel_list');
        }

        return $this->render('admin/categorymateriel/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function edit(CategoryMateriel $category, Request $request): Response
    {
        $form = $this->createForm(CategoryMaterielType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Catégorie modifiée !');

            return $this->redirectToRoute('categorymateriel_list');
        }

        return $this->render('admin/categorymateriel/form.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function delete(CategoryMateriel $category, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $this->em->remove($category);
            $this->em->flush();
            $this->addFlash('success', 'Catégorie supprimée !');
        }

        return $this->redirectToRoute('categorymateriel_list');
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function show(CategoryMateriel $category): Response
    {
        return $this->render('admin/categorymateriel/show.html.twig', [
            'category' => $category
        ]);
    }
}