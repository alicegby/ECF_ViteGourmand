<?php

namespace App\Controller;

use App\Entity\Materiel;
use App\Entity\CategoryMateriel;
use App\Form\MaterielType;
use App\Repository\MaterielRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MaterielController extends AbstractController {
    private MaterielRepository $materielRepository;
    private EntityManagerInterface $em;

    public function __construct(MaterielRepository $materielRepository, EntityManagerInterface $em)
    {
        $this->materielRepository = $materielRepository;
        $this->em = $em;
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function create(Request $request): Response {
        $materiel = new Materiel();
        $form = $this->createForm(MaterielType::class, $materiel, [
            'is_edit' => false
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid('materiel_') . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', "Erreur lors de l’upload de l’image.");
                }
                $materiel->setImage('uploads/' . $newFilename);
            }
            $materiel->setModifiePar($this->getUser());
            $materiel->setDateModif(new \DateTime());

            $this->em->persist($materiel);
            $this->em->flush();

            $this->addFlash('success', 'Matériel créé avec succès !');
            return $this->redirectToRoute('materiel_list');
        }
        return $this->render('admin/materiel/form.html.twig', [
            'form' => $form->createView(),
            'materiel' => $materiel,
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function edit(Materiel $materiel, Request $request): Response {
        $form = $this->createForm(MaterielType::class, $materiel, [
            'is_edit' => true
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid('materiel_') . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', "Erreur lors de l’upload de l’image.");
                }
                $materiel->setImage('uploads/' . $newFilename);
            }
            $materiel->setModifiePar($this->getUser());
            $materiel->setDateModif(new \DateTime());

            $this->em->flush();

            $this->addFlash('success', 'Materiel modifié avec succès !');
            return $this->redirect('materiel_list');
        }
        return $this->render('admin/materiel/form.html.twig', [
            'form' => $form->createView(),
            'materiel' => $materiel,
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function list(Request $request): Response {
        $categoryId = $request->query->get('category');
        $keyword = $request->query->get('keyword');

        $qb = $this->materielRepository->createQueryBuilder('mt')
            ->leftJoin('mt.category', 'c')
            ->addSelect('c');

        if($categoryId) {
            $qb->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }
        if($keyword) {
            $qb->andWhere('mt.titreMateriel LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword . '%');
        }

        $materiels = $qb->getQuery()->getResult();
        $categories = $this->em->getRepository(CategoryMateriel::class)->findAll();

        return $this->render('admin/materiel/list.html.twig', [
            'materiels' => $materiels,
            'categories' => $categories,
            'selectedCategory' => $categoryId,
            'keyword' => $keyword
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function show(Materiel $materiel): Response
    {
        return $this->render('admin/materiel/show.html.twig', [
            'materiel' => $materiel,
        ]);
    }
}