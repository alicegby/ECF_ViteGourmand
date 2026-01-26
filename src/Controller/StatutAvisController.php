<?php 

namespace App\Controller;

use App\Entity\StatutAvis;
use App\Form\StatutAvisType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class StatutAvisController extends AbstractController {
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em =$em;
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function list(): Response {
        $statutsAvis = $this->em->getRepository(StatutAvis::class)->findAll();
        return $this->render('admin/statutavis/list.html.twig', [
            'statutsAvis' => $statutsAvis,
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function create(Request $request): Response {
        $statutAvis = new StatutAvis();
        $form = $this->createForm(StatutAvisType::class, $statutAvis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($statutAvis);
            $this->em->flush();
            $this->addFlash('success', 'Statut créé !');
            if ($this->isGranted('ROLE_ADMIN')) {
                    return $this->redirectToRoute('admin_dashboard');
                } else {
                    return $this->redirectToRoute('employe_dashboard');
                }
        }
        return $this->render('admin/statutavis/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function edit(StatutAvis $statutAvis, Request $request): Response
    {
        $form = $this->createForm(StatutAvisType::class, $statutAvis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash('success', 'Statut modifié !');

            if ($this->isGranted('ROLE_ADMIN')) {
                    return $this->redirectToRoute('admin_dashboard');
                } else {
                    return $this->redirectToRoute('employe_dashboard');
                }
        }

        return $this->render('admin/statutavis/form.html.twig', [
            'form' => $form->createView(),
            'statutAvis' => $statutAvis
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function delete(StatutAvis $statutAvis, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $statutAvis->getId(), $request->request->get('_token'))) {
            $this->em->remove($statutAvis);
            $this->em->flush();
            $this->addFlash('success', 'Statut supprimé !');
        }

        if ($this->isGranted('ROLE_ADMIN')) {
                    return $this->redirectToRoute('admin_dashboard');
                } else {
                    return $this->redirectToRoute('employe_dashboard');
                }
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function show(StatutAvis $statutAvis): Response
    {
        return $this->render('admin/statutavis/show.html.twig', [
           'statutAvis' => $statutAvis
        ]);
    }
}