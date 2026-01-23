<?php

namespace App\Security;

use App\Entity\Employe;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if ($user instanceof Employe && !$user->isActif()) {
            throw new CustomUserMessageAuthenticationException(
                'Ce compte employé est désactivé.'
            );
        }
    }

    public function checkPostAuth(
        UserInterface $user,
        ?TokenInterface $token = null
    ): void {
        // Obligatoire, même vide
    }
}