<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontDashboardController extends AbstractController
{
    public function admin(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Redirige vers ton vrai dashboard admin
        return $this->redirectToRoute('admin_dashboard');
    }

    public function employe(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        // Redirige vers ton vrai dashboard employé
        return $this->redirectToRoute('employe_dashboard');
    }

    public function user(): Response {
        $this->denyAccessUnlessGranted('ROLE_USER');
        return $this->redirectToRoute('dashboard_user');
    }
}