<?php

namespace App\Controller;

use App\Entity\Fromages;
use App\Entity\CategoryCheese;
use App\Form\FromagesType;
use App\Repository\FromagesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class FromagesController extends AbstractController {
    private FromagesRepository $fromagesRepository;
    private EntityManagerInterface $em;

    public function __construct(FromagesRepository $fromagesRepository, EntityManagerInterface $em) {
        $this->fromagesRepository = $fromagesRepository;
        $this->em = $em;
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function create(Request $request): Response {
        $fromage = new Fromages();
        $form = $this->createForm(FromagesType::class, $fromage, [
            'is_edit' => false
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid('fromage_') . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('fromages_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', "Erreur lors de l'upload de l'image.");
                }
                $fromage->setImage('uploads/' . $newFilename);
            }
            $fromage->setModifiePar($this->getUser());
            $fromage->setDateModif(new \DateTime());

            $this->em->persist($fromage);
            $this->em->flush();

            $this->addFlash('succes', 'Fromage créé avec succèes !');
            if ($this->isGranted('ROLE_ADMIN')) {
                    return $this->redirectToRoute('admin_dashboard');
                } else {
                    return $this->redirectToRoute('employe_dashboard');
                }
        }
        return $this->render('admin/fromages/new.html.twig', [
            'form' => $form->createView(),
            'fromage' => $fromage,
        ]);
    }

   #[IsGranted('ROLE_EMPLOYE')]
    public function edit(Fromages $fromage, Request $request): Response {
        $form = $this->createForm(FromagesType::class, $fromage, [
            'is_edit' => true
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid('fromage_') . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('fromages_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', "Erreur lors de l’upload de l’image.");
                }
                $fromage->setImage('uploads/' . $newFilename);
            }
            $fromage->setModifiePar($this->getUser());
            $fromage->setDateModif(new \DateTime());

            $this->em->flush();

            $this->addFlash('success', 'Fromage modifié avec succès !');
            if ($this->isGranted('ROLE_ADMIN')) {
                    return $this->redirectToRoute('admin_dashboard');
                } else {
                    return $this->redirectToRoute('employe_dashboard');
                }
        }
        return $this->render('admin/fromages/form.html.twig', [
            'form' => $form->createView(),
            'fromage' => $fromage,
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function list(Request $request): Response {
        $categoryId = $request->query->get('category');
        $keyword = $request->query->get('keyword');

        $qb = $this->fromagesRepository->createQueryBuilder('f')
            ->leftJoin('f.category', 'c')
            ->addSelect('c');

        if($categoryId) {
            $qb->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }
        if($keyword) {
            $qb->andWhere('f.titreFromage LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword . '%');
        }

        $fromages = $qb->getQuery()->getResult();
        $categories = $this->em->getRepository(CategoryCheese::class)->findAll();

        return $this->render('admin/fromages/list.html.twig', [
            'fromages' => $fromages,
            'categories' => $categories,
            'selectedCategory' => $categoryId,
            'keyword' => $keyword
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function delete(Request $request, Fromages $fromage): Response {
        if (!$this->isCsrfTokenValid('delete'.$fromage->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }
        $this->em->remove($fromage);
        $this->em->flush();

        $this->addFlash('success', 'Fromage supprimé !');
        if ($this->isGranted('ROLE_ADMIN')) {
                    return $this->redirectToRoute('admin_dashboard');
                } else {
                    return $this->redirectToRoute('employe_dashboard');
                }
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function show(Fromages $fromage): Response
    {
        return $this->render('admin/fromages/show.html.twig', [
            'fromage' => $fromage,
        ]);
    } 
}