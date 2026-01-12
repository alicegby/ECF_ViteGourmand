<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends AbstractController
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastEmail = $authenticationUtils->getLastUsername();

        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();

        // Si l'utilisateur vient de la page d'accueil, on définit le dashboard comme cible
        $fromHome = $request->query->get('from_home'); // exemple : /login?from_home=1
        if ($fromHome) {
            $session->set('_security_utilisateurs.target_path', $this->generateUrl('dashboard_user'));
        }

        return $this->render('security/login.html.twig', [
            'last_email' => $lastEmail,
            'error' => $error,
        ]);
    }

    public function logout(): void
    {
        // Symfony gère automatiquement la déconnexion
    }
}