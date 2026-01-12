<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request; 
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Utilisateur;
use App\Entity\UtilisateurBadge;
use App\Entity\Commande;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; 
use Symfony\Component\Form\Extension\Core\Type\TextType; 
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DashboardUserController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[IsGranted('ROLE_USER')]
    public function index(Request $request): Response
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();

        // Badge et commandes
        $badge = $this->em->getRepository(UtilisateurBadge::class)
                          ->findOneBy(['utilisateur' => $user], ['dateObtention' => 'DESC']);

        $commandes = $this->em->getRepository(Commande::class)
                               ->findBy(['client' => $user], ['dateCommande' => 'DESC']);

        // Formulaire utilisateur pour édition inline
        $userForm = $this->createFormBuilder($user)
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('email', EmailType::class)
            ->add('telephone', TextType::class)
            ->add('adressePostale', TextType::class)
            ->add('codePostal', TextType::class)
            ->add('ville', TextType::class)
            ->add('submit', SubmitType::class, ['label' => 'Sauvegarder'])
            ->getForm();

        $userForm->handleRequest($request);

        // Soumission AJAX pour mise à jour inline
        if ($userForm->isSubmitted() && $userForm->isValid() && $request->isXmlHttpRequest()) {
            $this->em->flush();

            return new JsonResponse([
                'success' => true,
                'user' => [
                    'nom' => $user->getNom(),
                    'prenom' => $user->getPrenom(),
                    'email' => $user->getEmail(),
                    'telephone' => $user->getTelephone(),
                    'adressePostale' => $user->getAdressePostale(),
                    'codePostal' => $user->getCodePostal(),
                    'ville' => $user->getVille(),
                ],
            ]);
        }

        return $this->render('user/index.html.twig', [
            'user' => $user,
            'badge' => $badge,
            'commandes' => $commandes,
            'userForm' => $userForm->createView(), 
        ]);
    }

    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        /** @var Utilisateur $user */
        $user = $this->getUser();

        $form = $this->createFormBuilder($user)
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ->add('email', EmailType::class)
            ->add('telephone', TextType::class)
            ->add('adressePostale', TextType::class)
            ->add('codePostal', TextType::class)
            ->add('ville', TextType::class)
            ->add('submit', SubmitType::class, ['label' => 'Modifier'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Informations mises à jour !');

            return $this->redirectToRoute('dashboard_user');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}