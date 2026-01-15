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
        $user = $this->getUser();

        // On récupère la commande
        $commande = $this->em->getRepository(Commande::class)->find($commandeId);
        if (!$commande || $commande->getClient()?->getId() !== $user->getId()) {
            throw $this->createNotFoundException("Commande introuvable ou non autorisée.");
        }

        // Vérifier si un avis existe déjà pour cette commande
        if (count($commande->getAvis()) > 0) {
            $this->addFlash('warning', 'Vous avez déjà laissé un avis pour cette commande.');
            return $this->redirectToRoute('dashboard_user');
        }

        $avis = new Avis();
        $avis->setCommande($commande);
        $avis->setDateCreation(new \DateTime());

        // On définit le statut initial (ex: "En attente")
        $statutInitial = $this->em->getRepository(StatutAvis::class)->findOneBy(['libelle' => 'En attente']);
        $avis->setStatut($statutInitial);

        $form = $this->createFormBuilder($avis)
            ->add('notes', \Symfony\Component\Form\Extension\Core\Type\IntegerType::class, [
                'label' => 'Note (1 à 5)',
                'attr' => ['min' => 1, 'max' => 5]
            ])
            ->add('contenu', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, [
                'label' => 'Votre avis'
            ])
            ->add('submit', \Symfony\Component\Form\Extension\Core\Type\SubmitType::class, [
                'label' => 'Enregistrer'
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($avis);
            $this->em->flush();

            $this->addFlash('success', 'Merci pour votre avis !');
            return $this->redirectToRoute('dashboard_user');
        }

        return $this->render('user/avis.html.twig', [
            'form' => $form->createView(),
            'commande' => $commande
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function list(): Response {
        $avis = $this->em->getRepository(Avis::class)->findAll();
        return $this->render('admin/avis.list.html.twig', [
            'avis' => $avis,
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function edit(Avis $avis, Request $request): Response {
        $form = $this->createForm(AvisType::class, $avis);
        $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
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
    public function show(Avis $avis): Response
    {
        return $this->render('admin/avis/show.html.twig', [
            'avis' => $avis
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
