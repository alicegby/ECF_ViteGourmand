<?php

namespace App\Controller;
 
use App\Entity\Personnel;
use App\Entity\CategoryPersonnel;
use App\Form\PersonnelType;
use App\Repository\PersonnelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PersonnelController extends AbstractController {
    private PersonnelRepository $personnelRepository;
    private EntityManagerInterface $em;

    public function __construct(PersonnelRepository $personnelRepository, EntityManagerInterface $em)
    {
        $this->personnelRepository = $personnelRepository;
        $this->em = $em;
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function create(Request $request): Response
    {
        $personnel = new Personnel();
        $form = $this->createForm(PersonnelType::class, $personnel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($personnel);
            $this->em->flush();

            $this->addFlash('success', 'Personnel créé avec succès !');
            return $this->redirectToRoute('employe_dashboard');
        }

        return $this->render('admin/personnel/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function edit(Personnel $personnel, Request $request): Response
    {
        $form = $this->createForm(PersonnelType::class, $personnel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Personnel modifié avec succès !');
            return $this->redirectToRoute('employe_dashboard');
        }

        return $this->render('admin/personnel/form.html.twig', [
            'form' => $form->createView(),
            'personnel' => $personnel,
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function delete(Personnel $personnel, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$personnel->getId(), $request->request->get('_token'))) {
            $this->em->remove($personnel);
            $this->em->flush();
            $this->addFlash('success', 'Personnel supprimé !');
        }
        return $this->redirectToRoute('employe_dashboard');
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function list(Request $request): Response
    {
        $categoryId = $request->query->get('category');
        $keyword = $request->query->get('keyword');

        $qb = $this->personnelRepository->createQueryBuilder('ps')
            ->leftJoin('ps.category', 'c')
            ->addSelect('c');

        if ($categoryId) {
            $qb->andWhere('c.id = :categoryId')
               ->setParameter('categoryId', $categoryId);
        }

        if ($keyword) {
            $qb->andWhere('ps.titrePersonnel LIKE :keyword OR ps.description LIKE :keyword')
               ->setParameter('keyword', '%'.$keyword.'%');
        }

        $personnels = $qb->getQuery()->getResult();
        $categories = $this->em->getRepository(CategoryPersonnel::class)->findAll();

        return $this->render('admin/personnel/list.html.twig', [
            'personnels' => $personnels,
            'categories' => $categories,
            'selectedCategory' => $categoryId,
            'keyword' => $keyword,
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function show(Personnel $personnel): Response
    {
        return $this->render('admin/personnel/show.html.twig', [
            'personnel' => $personnel,
        ]);
    }
}