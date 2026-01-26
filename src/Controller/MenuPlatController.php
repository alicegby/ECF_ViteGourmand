<?php

namespace App\Controller;

use App\Entity\MenuPlat;
use App\Entity\Menu;
use App\Entity\Plats;
use App\Form\MenuPlatType;
use App\Repository\MenuPlatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class MenuPlatController extends AbstractController {
    private MenuPlatRepository $menuPlatRepository;
    private EntityManagerInterface $em;

    public function __construct(MenuPlatRepository $menuPlatRepository, EntityManagerInterface $em) {
        $this->menuPlatRepository = $menuPlatRepository;
        $this->em = $em;
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function create(Request $request): Response {
        $menuPlat = new MenuPlat();
        $form = $this->createForm(MenuPlatType::class, $menuPlat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($menuPlat);
            $this->em->flush();
            $this->addFlash('success', 'Plat ajouté au menu !');
            if ($this->isGranted('ROLE_ADMIN')) {
                    return $this->redirectToRoute('admin_dashboard');
                } else {
                    return $this->redirectToRoute('employe_dashboard');
                }
        }
        return $this->render('admin/menuplat/new.html.twig', ['form' => $form->createView(),]);
    } 

    #[IsGranted('ROLE_EMPLOYE')]
    public function edit(MenuPlat $menuPlat, Request $request): Response {
        $form = $this->createForm(MenuPlatType::class, $menuPlat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Association Menu/Plat modifiée !');
            if ($this->isGranted('ROLE_ADMIN')) {
                    return $this->redirectToRoute('admin_dashboard');
                } else {
                    return $this->redirectToRoute('employe_dashboard');
                }
        }
        return $this->render('admin/menuplat/form.html.twig', [
            'form' => $form->createView(),
            'menuPlat' => $menuPlat
        ]);
    }

        #[IsGranted('ROLE_EMPLOYE')]
        public function delete(MenuPlat $menuPlat, Request $request): Response {
        if ($this->isCsrfTokenValid('delete' . $menuPlat->getId(), $request->request->get('_token'))) {
            $this->em->remove($menuPlat);
            $this->em->flush();
            $this->addFlash('success', 'Association supprimée !');
        }
        if ($this->isGranted('ROLE_ADMIN')) {
                    return $this->redirectToRoute('admin_dashboard');
                } else {
                    return $this->redirectToRoute('employe_dashboard');
                }
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function list(Request $request): Response
    {
    // Récupération des filtres depuis l'URL 
    $selectedMenu = $request->query->get('menu');
    $keyword = $request->query->get('keyword');

    // QueryBuilder pour récupérer les MenuPlat avec filtres
    $qb = $this->menuPlatRepository->createQueryBuilder('mp')
        ->leftJoin('mp.menu', 'm')
        ->leftJoin('mp.plat', 'p')
        ->addSelect('m', 'p');

    if ($selectedMenu) {
        $qb->andWhere('m.id = :menuId')
           ->setParameter('menuId', $selectedMenu);
    }

    if ($keyword) {
        $qb->andWhere('p.titrePlat LIKE :keyword OR m.nom LIKE :keyword')
           ->setParameter('keyword', '%' . $keyword . '%');
    }

    $menuPlats = $qb->orderBy('mp.ordre', 'ASC')
                    ->getQuery()
                    ->getResult();


    $menus = $this->em->getRepository(Menu::class)->findAll();

    return $this->render('admin/menuplat/list.html.twig', [
        'menuPlats' => $menuPlats,
        'menus' => $menus,
        'selectedMenu' => $selectedMenu,
        'keyword' => $keyword,
    ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function show(MenuPlat $menuPlat): Response
    {
        return $this->render('admin/menuplat/show.html.twig', [
            'menuPlat' => $menuPlat,
        ]);
    }
}