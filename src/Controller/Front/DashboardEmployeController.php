<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
 
class DashboardEmployeController extends AbstractController
{
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYE');

        return $this->render('employe/dashboard.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}