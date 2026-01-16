<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Avis;
use App\Entity\Theme;
use App\Entity\Regime;
use App\Form\ContactType;
use App\Repository\MenuRepository;
use App\Repository\AvisRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


class HomeController extends AbstractController {

    public function homepage(ManagerRegistry $doctrine): Response {
        /** @var \App\Repository\AvisRepository $avisRepository */
        $avisRepository = $doctrine->getRepository(Avis::class);
        $avis_list = $avisRepository->findValidAvis(3);

        return $this->render('front/home.html.twig', [
            'avis_list' => $avis_list,
        ]);
    }

    public function menuList(
        MenuRepository $menuRepository,
        ManagerRegistry $doctrine,
        Request $request
    ): Response {

        $selectedTheme = $request->query->get('theme');
        $selectedRegime = $request->query->get('regime');
        $keyword = $request->query->get('keyword');

        $criteria = [
            'theme' => $request->query->get('theme'),
            'regime' => $request->query->get('regime'),
            'search' => $request->query->get('keyword'),
        ];

        $menus = $menuRepository->findByFilters($criteria);

        $themes = $doctrine->getRepository(Theme::class)->findAll();
        $regimes = $doctrine->getRepository(Regime::class)->findAll();

        return $this->render('front/menus_list.html.twig', [
            'menus' => $menus,
            'themes' => $themes,
            'regimes' => $regimes,
            'selectedTheme' => $selectedTheme,
            'selectedRegime' => $selectedRegime,
            'keyword' => $keyword,
        ]);
}

    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if ($form->isValid()) {
                $data = $form->getData();

                // Création de l'email
                $email = (new Email())
                    ->from($data['email'])
                    ->to('vitegourmand@yahoo.com') // adresse de réception
                    ->subject($data['theme'] . ' - ' . $data['objet'])
                    ->text(
                        "Nom : {$data['nom']} {$data['prenom']}\n" .
                        "Statut : {$data['statut']}\n" .
                        "Message :\n{$data['message']}"
                    );
 
                $mailer->send($email);

                $this->addFlash('success', 'Votre message a bien été envoyé !');

                // Évite la resoumission du formulaire
                return $this->redirectToRoute('contact');
            } else {
                // Si le formulaire est soumis mais invalide
                $this->addFlash('error', 'Erreur : veuillez remplir correctement tous les champs.');
            }
        }

        return $this->render('front/contact.html.twig', [
            'contactForm' => $form->createView(),
        ]);
    }

    public function cgv(): Response {
        return $this->render('front/cgv.html.twig');
    }

    public function privacy(): Response {
        return $this->render('front/privacy.html.twig');
    }

    public function faq(): Response {
        return $this->render('front/faq.html.twig');
    }

    public function reviews(ManagerRegistry $doctrine): Response {
        $avisRepository = $doctrine->getRepository(Avis::class);

        // Récupère tous les avis validés, sans limite
        /** @var \App\Repository\AvisRepository $avisRepository */
        $avis_list = $avisRepository->findValidAvis();

        return $this->render('front/reviews.html.twig', [
            'avis_list' => $avis_list,
        ]);
    }
}