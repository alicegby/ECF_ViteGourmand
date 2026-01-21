<?php

namespace App\Controller;

use App\Entity\CategoryPersonnel;
use App\Form\CategoryPersonnelType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
 
class CategoryPersonnelController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function list(): Response
    {
        $categories = $this->em->getRepository(CategoryPersonnel::class)->findAll();

        return $this->render('admin/categorypersonnel/list.html.twig', [
            'categories' => $categories,
        ]);
    }

   #[IsGranted('ROLE_EMPLOYE')]
    public function create(Request $request): Response
    {
        $category = new CategoryPersonnel();
        $form = $this->createForm(CategoryPersonnelType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($category);
            $this->em->flush();
            $this->addFlash('success', 'Catégorie créée !');

            return $this->redirectToRoute('employe_dashboard');
        }

        return $this->render('admin/categorypersonnel/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function edit(CategoryPersonnel $category, Request $request): Response
    {
        $form = $this->createForm(CategoryPersonnelType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Catégorie modifiée !');

            return $this->redirectToRoute('employe_dashboard');
        }

        return $this->render('admin/categorypersonnel/form.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]);
    }

   #[IsGranted('ROLE_EMPLOYE')]
    public function delete(CategoryPersonnel $category, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $this->em->remove($category);
            $this->em->flush();
            $this->addFlash('success', 'Catégorie supprimée !');
        }

        return $this->redirectToRoute('employe_dashboard');
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function show(CategoryPersonnel $category): Response
    {
        return $this->render('admin/categorypersonnel/show.html.twig', [
            'category' => $category
        ]);
    }
}