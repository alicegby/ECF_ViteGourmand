<?php

namespace App\Controller;

use App\Entity\Badge;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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

        if ($request->isMethod('POST')) {
            $badge->setNom($request->request->get('nom'));
            $badge->setDescription($request->request->get('description'));
            $badge->setIcone($request->request->get('icone'));
            $badge->setConditionObtention($request->request->get('conditionObtention'));
            $badge->setActif($request->request->get('actif') ? true : false);

            $em->persist($badge);
            $em->flush();

            $this->addFlash('success', 'Badge créé avec succès');

            return $this->redirectToRoute('badge_list');
        }

        return $this->render('admin/badge/form.html.twig');
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function edit(Badge $badge, Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) {
            $badge->setNom($request->request->get('nom'));
            $badge->setDescription($request->request->get('description'));
            $badge->setIcone($request->request->get('icone'));
            $badge->setConditionObtention($request->request->get('conditionObtention'));
            $badge->setActif($request->request->get('actif') ? true : false);

            $em->flush();

            $this->addFlash('success', 'Badge modifié avec succès');

            return $this->redirectToRoute('badge_list');
        }

        return $this->render('admin/badge/form.html.twig', [
            'badge' => $badge
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function delete(Badge $badge, EntityManagerInterface $em): Response
    {
        $em->remove($badge);
        $em->flush();

        $this->addFlash('success', 'Badge supprimé');

        return $this->redirectToRoute('badge_list');
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function show(Badge $badge): Response
    {
        return $this->render('admin/badge/show.html.twig', [
            'badge' => $badge
        ]); 
    }
}