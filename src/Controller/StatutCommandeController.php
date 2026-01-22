<?php

namespace App\Controller;

use App\Entity\StatutCommande;
use App\Entity\Commande;
use App\Form\StatutCommandeType;
use App\Form\CommandeEditType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class StatutCommandeController extends AbstractController {
    private EntityManagerInterface $em;
    private MailerInterface $mailer;

    public function __construct(EntityManagerInterface $em, MailerInterface $mailer) {
        $this->em =$em;
        $this->mailer = $mailer;
    }
 
    #[IsGranted('ROLE_EMPLOYE')]
    public function list(): Response {
        $statutsCommande = $this->em->getRepository(StatutCommande::class)->findAll();
        return $this->render('admin/statutcommande/list.html.twig', [
            'statutsCommande' => $statutsCommande,
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function create(Request $request): Response {
        $statutCommande = new StatutCommande();
        $form = $this->createForm(StatutCommandeType::class, $statutCommande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($statutCommande);
            $this->em->flush();
            $this->addFlash('success', 'Statut créé !');
            return $this->redirectToRoute('statutcommande_list');
        }
        return $this->render('admin/statutcommande/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function edit(StatutCommande $statutCommande, Request $request): Response
    {
        $form = $this->createForm(StatutCommandeType::class, $statutCommande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Statut modifié !');

            return $this->redirectToRoute('statutcommande_list');
        }

        return $this->render('admin/statutcommande/form.html.twig', [
            'form' => $form->createView(),
            'statutCommande' => $statutCommande
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function show(StatutCommande $statutCommande): Response
    {
        return $this->render('admin/statutcommande/show.html.twig', [
           'statutCommande' => $statutCommande
        ]);
    }
}