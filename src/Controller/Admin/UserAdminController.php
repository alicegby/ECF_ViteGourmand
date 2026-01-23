<?php

namespace App\Controller\Admin;

use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserAdminController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    public function list(Request $request, UtilisateurRepository $utilisateurRepository): Response
    {
        $keyword = $request->query->get('keyword');

        if ($keyword) {
            // Recherche par nom, prénom ou email
            $utilisateurs = $utilisateurRepository->createQueryBuilder('u')
                ->where('u.nom LIKE :keyword')
                ->orWhere('u.prenom LIKE :keyword')
                ->orWhere('u.email LIKE :keyword')
                ->setParameter('keyword', '%' . $keyword . '%')
                ->orderBy('u.nom', 'ASC')
                ->getQuery()
                ->getResult();
        } else {
            // Aucun filtre, on récupère tous les utilisateurs
            $utilisateurs = $utilisateurRepository->findAll();
        }

        return $this->render('admin/utilisateur/list.html.twig', [
            'utilisateurs' => $utilisateurs,
            'keyword' => $keyword
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    public function show(\App\Entity\Utilisateur $utilisateur): Response
    {
        return $this->render('admin/utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }
}