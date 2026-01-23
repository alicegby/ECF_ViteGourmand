<?php
namespace App\Security;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use App\Entity\Employe;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    { 
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $user = $token->getUser();

        // Si l'utilisateur est un employé inactif
        if ($user instanceof Employe && !$user->isActif()) {
            // Déconnecter immédiatement
            $request->getSession()?->invalidate(); // supprime la session si elle existe

            // Rediriger vers la page de login avec un paramètre "error"
            return new RedirectResponse($this->router->generate('admin_login', [
                'error' => 'inactif'
            ]));
        }

        $roles = $token->getRoleNames();

        if (in_array('ROLE_ADMIN', $roles, true)) {
            return new RedirectResponse($this->router->generate('admin_dashboard'));
        }

        if (in_array('ROLE_EMPLOYE', $roles, true)) {
            return new RedirectResponse($this->router->generate('employe_dashboard'));
        }

        return new RedirectResponse($this->router->generate('homepage'));
    }
}