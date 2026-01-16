<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Commande;
use App\Entity\StatutAvis;
use App\Form\AvisType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AvisController extends AbstractController {
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[IsGranted('ROLE_USER')]
    public function new(Request $request, int $commandeId): Response
    {
        /** @var \App\Entity\Utilisateur $user */
        $user = $this->getUser();

        $commande = $this->em->getRepository(Commande::class)->find($commandeId);
        if (!$commande || $commande->getClient()?->getId() !== $user->getId()) {
            throw $this->createNotFoundException("Commande introuvable ou non autorisée.");
        }

        // Vérifier si un avis existe déjà
        if (count($commande->getAvis()) > 0) {
            $this->addFlash('warning', 'Vous avez déjà laissé un avis pour cette commande.');
            return $this->redirectToRoute('dashboard_user');
        }

        $avis = new Avis();
        $avis->setCommande($commande);
        $avis->setDateCreation(new \DateTime());

        // Statut initial "En attente"
        $statutInitial = $this->em->getRepository(StatutAvis::class)->findOneBy(['libelle' => 'En attente']);
        $avis->setStatut($statutInitial);

        // Formulaire
        $form = $this->createFormBuilder($avis)
            ->add('notes', \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, [
                'choices' => [
                    '5 étoiles' => 5,
                    '4 étoiles' => 4,
                    '3 étoiles' => 3,
                    '2 étoiles' => 2,
                    '1 étoile'  => 1,
                ],
                'expanded' => true,   
                'multiple' => false,
                'required' => true,
                'label' => false,
            ])
            ->add('contenu', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, [
                'required' => true,
                'attr' => ['rows' => 5],
                'label' => false,
            ])
            ->add('submit', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, [
                'label' => 'Enregistrer'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($avis);
            $this->em->flush();

            $this->addFlash('success', 'Merci pour votre avis ! Votre avis est en attente de validation.');
            return $this->redirectToRoute('dashboard_user');
        }

        return $this->render('user/avis.html.twig', [
            'form' => $form->createView(),
            'commande' => $commande
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function list(Request $request): Response
    {
        $statutId = $request->query->get('statut');

        $qb = $this->em->getRepository(Avis::class)->createQueryBuilder('a')
            ->leftJoin('a.statut', 's')
            ->addSelect('s');

        if ($statutId) {
            $qb->andWhere('s.id = :statutId')
            ->setParameter('statutId', $statutId);
        }

        $avis = $qb->getQuery()->getResult();
        $statuts = $this->em->getRepository(StatutAvis::class)->findAll();

        if ($request->isXmlHttpRequest()) {
            return $this->render('admin/avis/list.html.twig', [
                'avis' => $avis,
                'ajax' => true,
            ]);
        }

        return $this->render('admin/avis/list.html.twig', [
            'avis' => $avis,
            'statuts' => $statuts,
            'ajax' => false,
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function edit(Avis $avis, Request $request): Response {
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
            /** @var \App\Entity\Employe $user */
            $user = $this->getUser();
            $avis->setValidePar($user);
            $avis->setDateValidation(new \DateTime());
            $this->em->flush();
            $this->addFlash('success', 'Avis mis à jour !');

            return $this->redirectToRoute('avis_list');
        }

        return $this->render('admin/avis/form.html.twig', [
            'form' => $form->createView(),
            'avis' => $avis
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function show(Avis $avis, Request $request): Response
    {
        $statuts = $this->em->getRepository(StatutAvis::class)->findAll();

        if ($request->isMethod('POST')) {
            $statutId = $request->request->get('statut');
            $statut = $this->em->getRepository(StatutAvis::class)->find($statutId);
 
            if ($statut) {
                /** @var \App\Entity\Employe $user */
                $user = $this->getUser();
                $avis->setStatut($statut);
                $avis->setValidePar($user);

                if ($statut->getLibelle() === 'Validé') {
                    $avis->setDateValidation(new \DateTime());
                } else {
                    $avis->setDateValidation(null);
                }
                $this->em->flush();
                $this->addFlash('success', 'Statut mis à jour !');
                return $this->redirectToRoute('employe_dashboard');
            } else {
                $this->addFlash('error', 'Statut invalide.');
            }
        }
        return $this->render('admin/avis/show.html.twig', [
            'avis' => $avis,
            'statuts' => $statuts,
        ]);
    }

    public function recent(): JsonResponse {
        $avis = $this->em->getRepository(Avis::class)->findBy(
            [],                       // aucun filtre
            ['dateCreation' => 'DESC'], // tri par date de création décroissante
            3                          // limite à 3 avis
        );

        $data = array_map(fn(Avis $a) => [
            'nom_client' => $a->getCommande()?->getClient()?->getNom() ?? 'Client', 
            'note' => $a->getNotes(),
            'commentaire' => $a->getContenu(),
            'dateCreation' => $a->getDateCreation()?->format('Y-m-d H:i')
        ], $avis);

        return new JsonResponse($data);
    }
}
