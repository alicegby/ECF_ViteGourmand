<?php

namespace App\Controller\Front;

use App\Entity\Menu;
use App\Repository\MenuRepository;
use App\Repository\MenuPlatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MenuController extends AbstractController
{
    public function detail(Menu $menu, MenuRepository $menuRepo, MenuPlatRepository $menuPlatRepo): Response
    { 
        $menuPrev = $menuRepo->createQueryBuilder('m')
            ->where('m.id < :id')
            ->setParameter('id', $menu->getId())
            ->orderBy('m.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult(); 

        $menuNext = $menuRepo->createQueryBuilder('m')
            ->where('m.id > :id')
            ->setParameter('id', $menu->getId())
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $menuPlats = $menuPlatRepo->findByMenuOrdered($menu->getId());
        $plats = array_map(fn($mp) => $mp->getPlat(), $menuPlats);

        $menuStock = $menu->getStock();

        return $this->render('front/menu_detail.html.twig', [
            'menu' => $menu,
            'menuPrev' => $menuPrev,
            'menuNext' => $menuNext,
            'plats' => $plats,
            'menuStock' => $menuStock,
            'minPersons' => $menu->getNbPersMin(),
        ]);
    }

    // =====================
    // AJAX pour ajouter au panier
    // =====================
    public function addToCart(Request $request): JsonResponse
    {
        $session = $request->getSession();

        $data = json_decode($request->getContent(), true);

        $menuId = $data['menuId'] ?? null;
        $quantity = $data['quantity'] ?? 1;
        $plats = $data['plats'] ?? [];

        if (!$menuId || empty($plats)) {
            return new JsonResponse(['success' => false, 'message' => 'Données invalides']);
        }

        // Récupération du panier en session (ou création si inexistant)
        $cart = $session->get('cart', []);

        // Si le menu existe déjà dans le panier, on incrémente la quantité
        $found = false;
        foreach ($cart as &$item) {
            if ($item['menuId'] == $menuId && $item['plats'] === $plats) {
                $item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $cart[] = [
                'menuId' => $menuId,
                'quantity' => $quantity,
                'plats' => $plats
            ];
        }

        $session->set('menu_add_to_cart', $cart);

        return new JsonResponse(['success' => true]);
    }
}