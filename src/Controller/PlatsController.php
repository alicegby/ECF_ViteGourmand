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
        // On crée un nouvel objet Plat ici, pas besoin de Symfony pour le résoudre
        $plat = new Plats();

        // On crée le formulaire
        $form = $this->createForm(PlatsType::class, $plat, ['is_edit' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Gestion de l'image si présente
                $imageFile = $form->get('image')->getData();
                if ($imageFile) {
                    $newFilename = uniqid('plat_') . '.' . $imageFile->guessExtension();
                    try {
                        $imageFile->move($this->getParameter('plats_images_directory'), $newFilename);
                        $plat->setImage('uploads/' . $newFilename);
                    } catch (\Exception $e) {
                        if ($request->isXmlHttpRequest()) {
                            return $this->json(['success' => false, 'message' => "Erreur upload image"]);
                        }
                        $this->addFlash('error', "Erreur upload image");
                    }
                }

                $plat->setModifiePar($this->getUser());
                $plat->setDateModif(new \DateTime());

                $this->em->persist($plat);
                $this->em->flush();

                if ($request->isXmlHttpRequest()) {
                    return $this->json([
                        'success' => true,
                        'message' => 'Plat créé avec succès !',
                        'redirectUrl' => $this->generateUrl('admin_plats_list')
                    ]);
                }

                $this->addFlash('success', 'Plat créé avec succès !');
                return $this->redirectToRoute('employe_dashboard');
            }

            // Gestion des erreurs pour AJAX
            if ($request->isXmlHttpRequest()) {
                $errors = [];
                foreach ($form->all() as $child) {
                    foreach ($child->getErrors(true) as $error) {
                        $errors[] = $error->getMessage();
                    }
                }
                return $this->json(['success' => false, 'errors' => $errors]);
            }
        }

        // Affichage du formulaire (GET ou après erreur)
        return $this->render('admin/plats/new.html.twig', [
            'plat' => $plat,
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function edit(Plats $plat, Request $request): Response
    {
        $form = $this->createForm(PlatsType::class, $plat, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $imageFile = $form->get('image')->getData();
                if ($imageFile) {
                    $newFilename = uniqid('plat_') . '.' . $imageFile->guessExtension();
                    try {
                        $imageFile->move($this->getParameter('uploads_directory'), $newFilename);
                    } catch (\Exception $e) {
                        if ($request->isXmlHttpRequest()) {
                            return $this->json(['success' => false, 'message' => "Erreur upload image"]);
                        }
                        $this->addFlash('error', "Erreur upload image");
                    }
                    $plat->setImage('uploads/' . $newFilename);
                }

                $plat->setModifiePar($this->getUser());
                $plat->setDateModif(new \DateTime());
                $this->em->flush();

                if ($request->isXmlHttpRequest()) {
                    return $this->json([
                        'success' => true,
                        'message' => 'Plat modifié avec succès !',
                        'redirectUrl' => $this->generateUrl('admin_plats_list')
                    ]);
                }

                $this->addFlash('success', 'Plat modifié avec succès !');
                return $this->redirectToRoute('employe_dashboard');
            } else if ($request->isXmlHttpRequest()) {
                $errors = [];
                foreach ($form->all() as $child) {
                    foreach ($child->getErrors(true) as $error) {
                        $errors[] = $error->getMessage();
                    }
                }
                return $this->json(['success' => false, 'errors' => $errors]);
            }
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('admin/plats/form.html.twig', [
                'plat' => $plat,
                'form' => $form->createView(),
            ]);
        }

        return $this->render('admin/plats/form.html.twig', [
            'plat' => $plat,
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function delete(Plats $plat, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('delete' . $plat->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('employe_dashboard');
        }

        $this->em->remove($plat);
        $this->em->flush();

        $this->addFlash('success', 'Plat supprimé !');
        return $this->redirectToRoute('employe_dashboard');
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