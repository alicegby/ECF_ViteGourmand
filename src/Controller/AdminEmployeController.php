<?php

namespace App\Controller;

use App\Entity\Employe;
use App\Form\EmployeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminEmployeController extends AbstractController {
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[IsGranted('ROLE_ADMIN')]
    public function list(): Response {
        $employes = $this->em->getRepository(Employe::class)->findAll();
        return $this->render('admin/employe/list.html.twig', [
            'employes' => $employes,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): Response {
        $employe = new Employe();
        $form = $this->createForm(EmployeType::class, $employe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('motDePasse')->getData();
            if ($plainPassword) {
                $employe->setMotDePasse(
                    password_hash($plainPassword, PASSWORD_BCRYPT)
                );
            }
            $this->em->persist($employe);
            $this->em->flush();

            $this->addFlash('success', 'Employé créé avec succès !');
            return $this->redirectToRoute('employe_list');
        }
        return $this->render('admin/employe/form.html.twig', [
            'form' => $form->createView(),
            'employe' => $employe
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    public function edit(Employe $employe, Request $request): Response {
        $form = $this->createForm(EmployeType::class, $employe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('motDePasse')->getData();
            if ($plainPassword) {
                $employe->setMotDePasse(
                    password_hash($plainPassword, PASSWORD_BCRYPT)
                );
            }
            $this->em->flush();
            $this->addFlash('succes', 'Employé mis à jour !');
            return $this->redirectToRoute('employe_list');
        }
        return $this->render('admin/employe/form.html.twig', [
            'form' => $form->createView(),
            'employe' => $employe
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    public function show(Employe $employe): Response {
        return $this->render('admin/employe/show.html.twig', [
            'employe' => $employe
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    public function delete(Employe $employe): Response {
        $this->em->remove($employe);
        $this->em->flush();
        $this->addFlash('success', 'Employé supprimé !');
        return $this->redirectToRoute('employe_list');
    }
}