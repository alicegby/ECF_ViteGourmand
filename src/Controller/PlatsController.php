<?php

namespace App\Controller;

use App\Entity\Allergenes;
use App\Entity\CategoryFood;
use App\Entity\Plats;
use App\Form\PlatsType;
use App\Repository\PlatsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PlatsController extends AbstractController
{
    private PlatsRepository $platsRepository;
    private EntityManagerInterface $em;

    public function __construct(PlatsRepository $platsRepository, EntityManagerInterface $em)
    {
        $this->platsRepository = $platsRepository;
        $this->em = $em;
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function create(Request $request): Response
    {
        $plat = new Plats();
        $form = $this->createForm(PlatsType::class, $plat, [
            'is_edit' => false
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // --- IMAGE UPLOAD ---
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid('plat_') . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', "Erreur lors de l’upload de l’image.");
                }
                $plat->setImage('uploads/' . $newFilename);
            }

            // --- METADATA ---
            $plat->setModifiePar($this->getUser());
            $plat->setDateModif(new \DateTime());

            $this->em->persist($plat);
            $this->em->flush();

            $this->addFlash('success', 'Plat créé avec succès !');
            return $this->redirectToRoute('plats_list');
        }

        return $this->render('admin/plats/form.html.twig', [
            'form' => $form->createView(),
            'plat' => $plat,
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function edit(Plats $plat, Request $request): Response
    {
        $form = $this->createForm(PlatsType::class, $plat, [
            'is_edit' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // --- IMAGE UPLOAD SI NOUVELLE IMAGE ---
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid('plat_') . '.' . $imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', "Erreur lors de l’upload de l’image.");
                }
                $plat->setImage('uploads/' . $newFilename);
            }

            $plat->setModifiePar($this->getUser());
            $plat->setDateModif(new \DateTime());

            $this->em->flush();

            $this->addFlash('success', 'Plat modifié avec succès !');
            return $this->redirectToRoute('plats_list');
        }

        return $this->render('admin/plats/form.html.twig', [
            'form' => $form->createView(),
            'plat' => $plat,
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function delete(Plats $plat, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('delete' . $plat->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('plats_list');
        }

        $this->em->remove($plat);
        $this->em->flush();

        $this->addFlash('success', 'Plat supprimé !');
        return $this->redirectToRoute('plats_list');
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function list(Request $request): Response
    {
        $categoryId = $request->query->get('category');
        $allergeneId = $request->query->get('allergene');
        $keyword = $request->query->get('keyword');

        $qb = $this->platsRepository->createQueryBuilder('p')
            ->leftJoin('p.category', 'c')
            ->leftJoin('p.allergenes', 'a')
            ->addSelect('c', 'a');

        if ($categoryId) {
            $qb->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }

        if ($allergeneId) {
            $qb->andWhere(':allergeneId MEMBER OF p.allergenes')
                ->setParameter('allergeneId', $allergeneId);
        }

        if ($keyword) {
            $qb->andWhere('p.titrePlat LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword . '%');
        }

        $plats = $qb->getQuery()->getResult();
        $categories = $this->em->getRepository(CategoryFood::class)->findAll();
        $allergenes = $this->em->getRepository(Allergenes::class)->findAll();

        return $this->render('admin/plats/list.html.twig', [
            'plats' => $plats,
            'categories' => $categories,
            'allergenes' => $allergenes,
            'selectedCategory' => $categoryId,
            'selectedAllergene' => $allergeneId,
            'keyword' => $keyword,
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function show(Plats $plat): Response
    {
        return $this->render('admin/plats/show.html.twig', [
            'plat' => $plat,
        ]);
    }
}