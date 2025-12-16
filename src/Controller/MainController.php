<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MainController extends AbstractController
{
    public function index(): Response
    {
        return new Response('<h1>Bienvenue sur l\'ECF</h1><p>Page d\'accueil temporaire</p>');
    }
}