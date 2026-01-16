<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminSecurityController extends AbstractController
{
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Récupération de l'erreur et du dernier email saisi
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastEmail = $authenticationUtils->getLastUsername();

        if ($this->getUser()) {
            if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
                return $this->redirectToRoute('admin_dashboard');
            } 
            if (in_array('ROLE_EMPLOYE', $this->getUser()->getRoles(), true)) {
                return $this->redirectToRoute('employe_dashboard');
            } 
        }

        // Sinon, affichage du formulaire de login
        return $this->render('admin/security/login.html.twig', [
            'last_email' => $lastEmail,
            'error' => $error,
        ]);
    }

    public function logout(): void
    {
        // Symfony intercepte automatiquement cette route, donc la méthode peut rester vide
        throw new \LogicException('Cette méthode est interceptée par le firewall..');
    }
}