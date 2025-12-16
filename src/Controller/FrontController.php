<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\MenuRepository;

class FrontController extends AbstractController {
    public function homepage(): Response { return $this->render('front/home.html.twig'); }

    public function menuList(MenuRepository $repo): Response {
        $menus = $repo->findAll();
        return $this->render('front/menus_list.html.twig', [
            'menus' => $menus,
        ]);
    }
    public function contact(): Response { return $this->render('front/contact.html.twig'); }

    public function panier(): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('front/panier.html.twig');
    }

    public function login(): Response { return $this->render('front/login.html.twig'); }

    public function cgv(): Response { return $this->render('front/cgv.html.twig'); }

    public function privacy(): Response { return $this->render('front/privacy.html.twig'); }

    public function faq(): Response { return $this->render('front/faq.html.twig'); }

    public function reviews(): Response { return $this->render('front/reviews.html.twig'); }
}