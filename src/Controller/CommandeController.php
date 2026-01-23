<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\StatutCommande;
use App\Entity\Plats;
use App\Form\CommandeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Form\FormError;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class CommandeController extends AbstractController
{
    private EntityManagerInterface $em; 

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function list(Request $request): Response
    {
        $statutId = $request->query->get('statutId');

        $commandeRepo = $this->em->getRepository(Commande::class);
        $statutRepo = $this->em->getRepository(StatutCommande::class);

        if ($statutId) {
            $commandes = $commandeRepo->findBy(['statutCommande' => $statutId]);
        } else {
            $commandes = $commandeRepo->findAll();
        }

        $statuts = $statutRepo->findAll();

        // Si c’est une requête Ajax, on ne renvoie que le fragment HTML de la table
        if ($request->isXmlHttpRequest()) {
            return $this->render('admin/commande/list.html.twig', [
                'commandes' => $commandes,
                'statuts' => $statuts,
            ]);
        }

        // Sinon on peut avoir un wrapper complet ou juste le même template
        return $this->render('admin/commande/list.html.twig', [
            'commandes' => $commandes,
            'statuts' => $statuts,
        ]);
    }

   #[IsGranted('ROLE_EMPLOYE')]
    public function edit(Commande $commande, Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ancienStatut = $this->em->getUnitOfWork()->getOriginalEntityData($commande)['statutCommande'] ?? null;
            $nouveauStatut = $commande->getStatutCommande();
        
            /** @var \App\Entity\Employe $user */
            $user = $this->getUser();
            $commande->setModifiePar($user);
            $commande->setDateModif(new \DateTime());
            $this->em->flush();

            if ($ancienStatut !== $nouveauStatut) {
                $client = $commande->getClient();
                if ($client && $client->getEmail()) {
                    $email = (new TemplatedEmail())
                        ->from('viteetgourmand@gmail.com')
                        ->to($client->getEmail())
                        ->subject('Mise à jour de votre commande')
                        ->htmlTemplate('emails/commande_statut.html.twig')
                        ->context([
                            'client' => $client,
                            'commande' => $commande,
                            'statut' => $nouveauStatut,
                        ]);

                    $mailer->send($email);
                }
            }

            $this->addFlash('success', 'Commande mise à jour !');

            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'success' => true,
                ]);
            }
            return $this->redirectToRoute('employe_dashboard');
        }

        if ($request->isXmlHttpRequest()) {
            $formHtml = $this->renderView('admin/commande/form.html.twig', [
                'form' => $form->createView(),
                'commande' => $commande
            ]);

            return $this->json([
                'success' => false,
                'formHtml' => $formHtml,
                'errors' => (string) $form->getErrors(true, false)
            ]);
        }

        return $this->render('admin/commande/form.html.twig', [
            'form' => $form->createView(),
            'commande' => $commande
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function show(Commande $commande): Response
    {
        return $this->render('admin/commande/show.html.twig', [
            'commande' => $commande
        ]);
    }
}
