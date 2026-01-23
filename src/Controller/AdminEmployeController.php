<?php

namespace App\Controller;

use App\Entity\Employe;
use App\Form\EmployeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class AdminEmployeController extends AbstractController
{
    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher)
    {
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
    }

    #[IsGranted('ROLE_ADMIN')]
    public function list(): Response
    {
        $employes = $this->em->getRepository(Employe::class)->findAll();

        return $this->render('admin/employe/list.html.twig', [
            'employes' => $employes,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, MailerInterface $mailer): Response
    {
        $employe = new Employe();
        $form = $this->createForm(EmployeType::class, $employe, [
            'is_edit' => false
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si l'email existe déjà
            $existing = $this->em->getRepository(Employe::class)
                                 ->findOneBy(['email' => $employe->getEmail()]);
            if ($existing) {
                $this->addFlash('error', 'Un employé avec cet email existe déjà.');
                return $this->redirectToRoute('admin_dashboard');
            }

            // Hasher le mot de passe si renseigné
            $plainPassword = $form->get('motDePasse')->getData();
            if ($plainPassword) {
                $employe->setPassword(
                    $this->passwordHasher->hashPassword($employe, $plainPassword)
                );
            }

            $this->em->persist($employe);
            $this->em->flush();

            $email = (new TemplatedEmail())
                        ->from('viteetgourmand@gmail.com')
                        ->to($employe->getEmail())
                        ->subject('Votre compte Vite & Gourmand a été créé !')
                        ->htmlTemplate('emails/employe_creation.html.twig')
                        ->context([
                            'employe' => $employe,
                        ]);
            try {
                $mailer->send($email);
            } catch (\Throwable $e) {
                $this->addFlash('warning', 'Compte créé mais le mail n’a pas pu être envoyé.');
            }

            $this->addFlash('success', 'Employé créé avec succès !');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/employe/form.html.twig', [
            'form' => $form->createView(),
            'employe' => $employe
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    public function edit(Employe $employe, Request $request): Response
    {
        $form = $this->createForm(EmployeType::class, $employe, [
            'is_edit' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hasher le mot de passe si renseigné
            $plainPassword = $form->get('motDePasse')->getData();
            if ($plainPassword) {
                $employe->setPassword(
                    $this->passwordHasher->hashPassword($employe, $plainPassword)
                );
            }

            $this->em->flush();
            $this->addFlash('success', 'Employé mis à jour !');
            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/employe/form.html.twig', [
            'form' => $form->createView(),
            'employe' => $employe
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    public function show(Employe $employe): Response
    {
        return $this->render('admin/employe/show.html.twig', [
            'employe' => $employe
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    public function delete(Employe $employe, Request $request): Response
    {
        // Vérification CSRF
        if ($this->isCsrfTokenValid('delete'.$employe->getId(), $request->request->get('_token'))) {
            $this->em->remove($employe);
            $this->em->flush();
            $this->addFlash('success', 'Employé supprimé !');
        } else {
            $this->addFlash('error', 'Token CSRF invalide. Suppression annulée.');
        }

        return $this->redirectToRoute('admin_dashboard');
    }
}