<?php

namespace App\Controller;

use App\Entity\Boissons;
use App\Entity\CategoryDrink;
use App\Form\BoissonsType;
use App\Repository\BoissonsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BoissonsController extends AbstractController {
    private BoissonsRepository $boissonsRepository;
    private EntityManagerInterface $em;

    public function __construct(BoissonsRepository $boissonsRepository, EntityManagerInterface $em)
    {
        $this->boissonsRepository = $boissonsRepository;
        $this->em = $em;
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function create(Request $request): Response {
        $boisson = new Boissons();
        $form = $this->createForm(BoissonsType::class, $boisson, [
            'is_edit' => false
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid('boisson_') . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', "Erreur lors de l’upload de l’image.");
                }
                $boisson->setImage('uploads/' . $newFilename);
            }
            $boisson->setModifiePar($this->getUser());
            $boisson->setDateModif(new \DateTime());

            $this->em->persist($boisson);
            $this->em->flush();

            $this->addFlash('success', 'Boisson créée avec succès !');
            return $this->redirectToRoute('boissons_list');
        }
        return $this->render('admin/boissons/form.html.twig', [
            'form' => $form->createView(),
            'boisson' => $boisson,
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function edit(Boissons $boisson, Request $request): Response {
        $form = $this->createForm(BoissonsType::class, $boisson, [
            'is_edit' => true
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid('boisson_') . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', "Erreur lors de l’upload de l’image.");
                }
                $boisson->setImage('uploads/' . $newFilename);
            }
            $boisson->setModifiePar($this->getUser());
            $boisson->setDateModif(new \DateTime());

            $this->em->flush();

            $this->addFlash('success', 'Boisson modifiée avec succès !');
            return $this->redirect('boissons_list');
        }
        return $this->render('admin/boissons/form.html.twig', [
            'form' => $form->createView(),
            'boisson' => $boisson,
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function list(Request $request): Response {
        $categoryId = $request->query->get('category');
        $keyword = $request->query->get('keyword');

        $qb = $this->boissonsRepository->createQueryBuilder('b')
            ->leftJoin('b.category', 'c')
            ->addSelect('c');

        if($categoryId) {
            $qb->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }
        if($keyword) {
            $qb->andWhere('b.titreBoisson LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword . '%');
        }

        $boissons = $qb->getQuery()->getResult();
        $categories = $this->em->getRepository(CategoryDrink::class)->findAll();

        return $this->render('admin/boissons/list.html.twig', [
            'boissons' => $boissons,
            'categories' => $categories,
            'selectedCategory' => $categoryId,
            'keyword' => $keyword
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function show(Boissons $boisson): Response
    {
        return $this->render('admin/boissons/show.html.twig', [
            'boisson' => $boisson,
        ]);
    }
}