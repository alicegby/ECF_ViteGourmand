<?php

namespace App\Controller;

use App\Entity\Badge;
use App\Form\BadgeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BadgeController extends AbstractController
{
    #[IsGranted('ROLE_EMPLOYE')]
    public function list(EntityManagerInterface $em): Response
    {
        $badges = $em->getRepository(Badge::class)->findAll();

        return $this->render('admin/badge/list.html.twig', [
            'badges' => $badges
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $badge = new Badge();

        $form = $this->createForm(BadgeType::class, $badge, ['is_edit' => false]);
        $form->handleRequest($request);

       if ($form->isSubmitted()) {
        if ($form->isValid()) {
            $iconeFile = $form->get('icone')->getData();
            if ($iconeFile) {
                $newFilename = uniqid('badge_') . '.' . $iconeFile->guessExtension();
                try {
                    $iconeFile->move($this->getParameter('badge_icons_directory'), $newFilename);
                    $badge->setIcone('uploads/' . $newFilename);
                } catch (\Exception $e) {
                    if ($request->isXmlHttpRequest()) {
                        return $this->json(['success' => false, 'message' => "Erreur upload icône"]);
                    }
                    $this->addFlash('error', "Erreur upload icône");
                }
            }
            $badge->setIcone('uploads/' . $newFilename);
            $em->persist($badge);
            $em->flush();

            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'success' => true,
                    'message' => 'Badge créé avec succès !',
                    'redirectUrl' => $this->generateUrl('badge_list')
                ]);
            }
            $this->addFlash('success', 'Badge créé avec succès !');
            if ($this->isGranted('ROLE_ADMIN')) {
                    return $this->redirectToRoute('admin_dashboard');
                } else {
                    return $this->redirectToRoute('employe_dashboard');
                }
        }
        if ($request->isXmlHttpRequest()) {
            $errors = [];
            foreach ($form->all() as $child) {
                foreach ($child->getErrors(true) as $error) {
                    $errors[] = $error->getMessage();
                }
            }
            return $this->json(['success' => false, 'errors' => $errors]);
        }
       }
       return $this->render('admin/badge/new.html.twig', [
        'badge' => $badge,
        'form' => $form->createView()
       ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function edit(Badge $badge, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(BadgeType::class, $badge, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $iconeFile = $form->get('icone')->getData();
                if ($iconeFile) {
                    $newFilename = uniqid('badge_') . '.' . $iconeFile->guessExtension();
                    try {
                        $iconeFile->move($this->getParameter('badge_icons_directory'), $newFilename);
                    } catch (\Exception $e) {
                        if ($request->isXmlHttpRequest()) {
                            return $this->json(['succes' => false, 'message => "Erreur upload icône']);
                        }
                        $this->addFlash('error', "Erreur upload image");
                    }
                    $badge->setIcone('uploads/' . $newFilename);
                }
                $em->flush();

                if ($request->isXmlHttpRequest()) {
                    return $this->json([
                        'success' => true,
                        'message' => "Badge modifié avec succès !",
                        'redirectUrl' => $this->generateUrl('badge_list')
                    ]);
                }
                $this->addFlash('success', 'Badge modifié avec succès');
               if ($this->isGranted('ROLE_ADMIN')) {
                    return $this->redirectToRoute('admin_dashboard');
                } else {
                    return $this->redirectToRoute('employe_dashboard');
                }
            } else if ($request->isXmlHttpRequest()) {
                $errors = [];
                foreach ($form->all() as $child) {
                    foreach ($child->getErrors(true) as $error) {
                        $errors[] = $error->getMessage();
                    }
                }
                return $this->json(['success' => false, 'errors' => $errors]);
            }
        }
        if ($request->isXmlHttpRequest()) {
            return $this->render('admin/badge/form.html.twig', [
                'badge' => $badge,
                'form' => $form->createView(),
            ]);
        }
        return $this->render('admin/badge/form.html.twig', [
            'badge' => $badge,
            'form' => $form->createView(),
        ]); 
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function delete(Badge $badge, EntityManagerInterface $em): Response
    {
        $em->remove($badge);
        $em->flush();

        $this->addFlash('success', 'Badge supprimé');

        if ($this->isGranted('ROLE_ADMIN')) {
                    return $this->redirectToRoute('admin_dashboard');
                } else {
                    return $this->redirectToRoute('employe_dashboard');
                }
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function show(Badge $badge): Response
    {
        return $this->render('admin/badge/show.html.twig', [
            'badge' => $badge
        ]); 
    }
    
}