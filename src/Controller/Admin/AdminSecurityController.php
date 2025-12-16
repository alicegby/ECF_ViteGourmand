<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class AdminSecurityController extends AbstractController
{
    public function login(): Response
    {
        return $this->render('admin/security/login.html.twig');
    }

    public function logout(): void
    {
        // Symfony gère le logout automatiquement via security.yaml
        throw new \Exception('This should never be reached!');
    }
}