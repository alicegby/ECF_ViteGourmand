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
use App\Entity\Badge;
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

        // Récupération des badges
        $badgeRepo = $this->em->getRepository(Badge::class);
        $badgePremiereCommande = $badgeRepo->findOneBy(['nom' => 'Première Commande']);
        $badgeGourmandConfirme = $badgeRepo->findOneBy(['nom' => 'Gourmand Confirmé']);
        $badgeClientVIP = $badgeRepo->findOneBy(['nom' => 'Client VIP']);
        $badgeExplorateursSaveurs = $badgeRepo->findOneBy(['nom' => 'Explorateurs des saveurs']);
        $badgeCritique = $badgeRepo->findOneBy(['nom' => 'Critique']);

        // Récupération de toutes les commandes de l'utilisateur
        $commandes = $this->em->getRepository(Commande::class)
                            ->findBy(['client' => $user]);

        $totalCommandes = 0;
        $avisAcceptees = 0;

        foreach ($commandes as $commande) {
            if (!$commande->isAccepted()) continue;

            $totalCommandes++;

            // Attribution des badges
            if ($totalCommandes === 1 && !$user->hasBadge($badgePremiereCommande)) {
                $this->addBadgeIfMissing($user, $badgePremiereCommande);
            }
            if ($totalCommandes >= 5 && !$user->hasBadge($badgeGourmandConfirme)) {
                $this->addBadgeIfMissing($user, $badgeGourmandConfirme);
            }
            if ($totalCommandes >= 10 && !$user->hasBadge($badgeClientVIP)) {
                $this->addBadgeIfMissing($user, $badgeClientVIP);
            }
            // Badge "Explorateurs des saveurs"
            foreach ($commande->getMenu() as $menu) {
                $platsCommandes = [];
                foreach ($menu->getPlats() as $plat) {
                    $platsCommandes[$plat->getId()] = true;
                }
                if (count($platsCommandes) >= 2) {
                    $this->addBadgeIfMissing($user, $badgeExplorateursSaveurs);
                }
            }

            // Badge "Critique"
            foreach ($commande->getAvis() as $avis) {
                if ($avis->isAccepted()) $avisAcceptees++;
            }
        }

        if ($avisAcceptees >= 5) $this->addBadgeIfMissing($user, $badgeCritique);

        // Sauvegarde toutes les nouvelles attributions
        $this->em->flush();

        // Récupération du dernier badge débloqué
        $badge = $this->em->getRepository(UtilisateurBadge::class)
                          ->findOneBy(['utilisateur' => $user], ['dateObtention' => 'DESC']);

        // Séparation des commandes gamifiées, en cours et terminées
        $statutsGamifiables = [
            'En préparation',
            'En livraison',
            'Livrée',
            'En attente de retour du matériel'
        ];

        $commandesGamifiees = [];
        $commandesEnCours = [];
        $commandesTerminees = [];

        foreach ($commandes as $commande) {
            $statut = $commande->getStatutCommande()?->getLibelle();

            // Commandes gamifiables
            if (in_array($statut, $statutsGamifiables) && $statut !== 'Terminée') {
                $commandesGamifiees[] = $commande;
                continue; // Si gamifiée, ne pas la mettre ailleurs
            }

            // Commandes terminées
            if ($commande->isTerminee() || in_array($statut, ['Livrée', 'Annulée', 'Terminée'])) {
                $commandesTerminees[] = $commande;
            } else {
                $commandesEnCours[] = $commande;
            }
        }

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

        $formAvis = [];
        foreach ($commandesTerminees as $commande) {
            $avisExistant = $this->em->getRepository(\App\Entity\Avis::class)->findOneBy(['commande' => $commande]);

            if (!$avisExistant) {
                $avis = new \App\Entity\Avis();
                $avis->setCommande($commande)
                        ->setDateCreation(new \DateTime());
                        $statutEnAttente = $this->em->getRepository(\App\Entity\StatutAvis::class)
                                            ->findOneBy(['libelle' => 'En attente']);
                    $avis->setStatut($statutEnAttente);

                    $formAvis[$commande->getId()] = $this->createFormBuilder($avis)
                        ->add('notes', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, [
                            'choices' => ['⭐' => 1, '⭐⭐' => 2, '⭐⭐⭐' => 3, '⭐⭐⭐⭐' => 4, '⭐⭐⭐⭐⭐' => 5],
                            'expanded' => true,
                            'multiple' => false,
                        ])
                        ->add('contenu', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, [
                            'attr' => ['rows' => 3, 'placeholder' => 'Votre avis...']
                        ])
                        ->getForm();

                    $formAvis[$commande->getId()]->handleRequest($request);
                    if ($formAvis[$commande->getId()]->isSubmitted() && $formAvis[$commande->getId()]->isValid()) {
                        $this->em->persist($avis);
                        $this->em->flush();
                        $this->addFlash('success', 'Merci pour votre avis !');
                        return $this->redirectToRoute('dashboard_user');
                    }
            }
        }

        return $this->render('user/index.html.twig', [
            'user' => $user,
            'badge' => $badge,
            'commandesGamifiees' => $commandesGamifiees,
            'commandesEnCours' => $commandesEnCours,
            'commandesTerminees' => $commandesTerminees,
            'userForm' => $userForm->createView(), 
            'formAvis' => $formAvis,
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

    /**
     * Ajoute un badge à l'utilisateur si il ne l'a pas déjà
     */
    private function addBadgeIfMissing(Utilisateur $user, ?Badge $badge): void
    {
        if (!$badge) return;

        foreach ($user->getUtilisateurBadge() as $ub) {
            if ($ub->getBadge()?->getId() === $badge->getId()) return;
        }

        $utilisateurBadge = new UtilisateurBadge();
        $utilisateurBadge->setUtilisateur($user)
                        ->setBadge($badge)
                        ->setDateObtention(new \DateTime());
        $this->em->persist($utilisateurBadge);
    }
}