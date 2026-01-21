<?php

namespace App\Controller;

use App\Entity\Horaire;
use App\Form\HoraireType;
use App\Repository\HoraireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HoraireController extends AbstractController
{
    private HoraireRepository $horaireRepository;
    private EntityManagerInterface $em;

    public function __construct(HoraireRepository $horaireRepository, EntityManagerInterface $em)
    {
        $this->horaireRepository = $horaireRepository;
        $this->em = $em;
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function list(): Response
    {
        return $this->render('admin/horaire/list.html.twig', [
            'horaires' => $this->horaireRepository->findAllOrdered(),
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function edit(Horaire $horaire, Request $request): Response
    {
        $form = $this->createForm(HoraireType::class, $horaire);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $horaire->setModifiePar($this->getUser());
            $horaire->setDateModif(new \DateTime());

            $this->em->flush();

            $this->addFlash('success', 'Horaire modifié');

            return $this->redirectToRoute('employe_dashboard');
        }

        return $this->render('admin/horaire/form.html.twig', [
            'form' => $form->createView(),
            'horaire' => $horaire
        ]);
    }

   #[IsGranted('ROLE_EMPLOYE')]
    public function show(Horaire $horaire): Response
    {
        return $this->render('admin/horaire/show.html.twig', [
            'horaire' => $horaire
        ]); 
    }
} 