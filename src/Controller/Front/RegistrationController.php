<?php

namespace App\Controller\Front;

use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        $user = new Utilisateur();
        $user->setDateCreation(new \DateTime());

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Vérification email AVANT tout
            $existingUser = $em->getRepository(\App\Entity\Utilisateur::class)
                                            ->findOneBy([
                'email' => $user->getEmail()
            ]);

            if ($existingUser) {
                $this->addFlash('error', 'Un compte avec cette adresse email existe déjà.');
                return $this->redirectToRoute('app_register');
            }

            // Hash du mot de passe
            $plainPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setMotDePasse($hashedPassword);

            // Persist utilisateur
            $em->persist($user);
            $em->flush();

            // Envoi du mail (dans un try/catch)
            try {
                $email = (new TemplatedEmail())
                    ->from('viteetgourmand@gmail.com')
                    ->to($user->getEmail())
                    ->subject('Bienvenue chez Vite & Gourmand !')
                    ->htmlTemplate('emails/registration.html.twig')
                    ->context([
                        'utilisateur' => $user,
                    ]);

                $mailer->send($email);
            } catch (\Throwable $e) {
                // On log mais on ne bloque pas l'inscription
                $this->addFlash(
                    'warning',
                    'Votre compte a été créé, mais le mail de bienvenue n’a pas pu être envoyé.'
                );
            }

            $this->addFlash('success', 'Inscription réussie !');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}