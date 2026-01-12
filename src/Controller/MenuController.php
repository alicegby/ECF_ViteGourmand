<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\Theme;
use App\Entity\Regime;
use App\Entity\Condition;
use App\Form\MenuType;
use App\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use DateTime;

class MenuController extends AbstractController
{
    private MenuRepository $menuRepository;
    private EntityManagerInterface $em;

    public function __construct(MenuRepository $menuRepository, EntityManagerInterface $em)
    {
        $this->menuRepository = $menuRepository;
        $this->em = $em;
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function create(Request $request): Response
    {
        $menu = new Menu();
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $menu->setModifiePar($this->getUser());
            $menu->setDateModif(new DateTime());
            $this->em->persist($menu);
            $this->em->flush();

            $this->addFlash('success', 'Menu créé avec succès !');

            return $this->redirectToRoute('menu_list'); 
        }

        return $this->render('admin/menu/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function edit(Menu $menu, Request $request): Response
    {
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $menu->setModifiePar($this->getUser());
            $menu->setDateModif(new DateTime());
            $this->em->flush();

            $this->addFlash('success', 'Menu modifié avec succès !');

            return $this->redirectToRoute('menu_list'); 
        }

        return $this->render('admin/menu/form.html.twig', [
            'form' => $form->createView(),
            'menu' => $menu
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function delete(Menu $menu): Response
    {
        $this->em->remove($menu);
        $this->em->flush();

        $this->addFlash('success', 'Menu supprimé !');

        return $this->redirectToRoute('menu_list'); 
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function list(Request $request): Response
    {
        $themeId = $request->query->get('theme');
        $regimeId = $request->query->get('regime');
        $keyword = $request->query->get('keyword');

        $qb = $this->menuRepository->createQueryBuilder('m')
            ->leftJoin('m.theme', 't')
            ->leftJoin('m.regime', 'r')
            ->addSelect('t', 'r');

        if ($themeId) {
            $qb->andWhere('t.id = :themeId')
                ->setParameter('themeId', $themeId);
        }

        if ($regimeId) {
            $qb->andWhere('r.id = :regimeId')
                ->setParameter('regimeId', $regimeId);
        }

        if ($keyword) {
            $qb->andWhere('m.titreMenu LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword . '%');
        }

        $menu = $qb->getQuery()->getResult();
        $themes = $this->em->getRepository(Theme::class)->findAll();
        $regimes = $this->em->getRepository(Regime::class)->findAll();

        return $this->render('admin/menu/list.html.twig', [
            'menus' => $menu,
            'themes' => $themes,
            'selectedTheme' => $themeId,
            'regimes' => $regimes,
            'selectedRegime' => $regimeId,
            'keyword' => $keyword,
        ]); 
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function show(Menu $menu): Response
    {
        return $this->render('admin/menu/show.html.twig', [
            'menu' => $menu,
        ]);
    }
}