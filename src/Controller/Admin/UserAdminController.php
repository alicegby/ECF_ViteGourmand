<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class UserAdminController extends AbstractController
{
    public function index(): Response
    {
        // On renvoie juste un texte pour tester que la classe existe
        return new Response('UserAdminController OK');
    }
}