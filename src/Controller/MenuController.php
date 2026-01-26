<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Entity\Theme;
use App\Entity\Regime;
use App\Entity\Condition;
use App\Entity\ImageMenu;
use App\Form\MenuType;
use App\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use DateTime;

class MenuController extends AbstractController
{
    private MenuRepository $menuRepository;
    private EntityManagerInterface $em;

    public function __construct(MenuRepository $menuRepository, EntityManagerInterface $em)
    {
        $this->menuRepository = $menuRepository;
        $this->em = $em;
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function create(Request $request): Response
    {
        $menu = new Menu();

        // Ajouter au moins une image vide pour le formulaire
        if ($menu->getImages()->isEmpty()) {
            $menu->addImage(new ImageMenu());
        }

        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // --- Gestion des images ---
            foreach ($form->get('images') as $imageForm) {
                /** @var ImageMenu $image */
                $image = $imageForm->getData();
                $uploadedFile = $imageForm->get('file')->getData();

                if ($uploadedFile) {
                    $filename = uniqid() . '.' . $uploadedFile->guessExtension();
                    $uploadedFile->move(
                        $this->getParameter('menus_images_directory'),
                        $filename
                    );
                    $image->setUrl($filename);
                }

                if (!$menu->getImages()->contains($image)) {
                    $menu->addImage($image);
                }
            }

            // Prix par personne en string
            $prix = $menu->getPrixParPersonne();
            if ($prix !== null) {
                $menu->setPrixParPersonne((string)$prix);
            }

            $menu->setModifiePar($this->getUser());
            $menu->setDateModif(new \DateTime());

            $this->em->persist($menu);
            $this->em->flush();

            // Réponse AJAX
            if ($request->isXmlHttpRequest()) {
                return $this->json([
                    'success' => true,
                    'redirectUrl' => $this->generateUrl('employe_dashboard', ['id' => $menu->getId()])
                ]);
            }

            $this->addFlash('success', 'Menu créé avec succès !');
            if ($this->isGranted('ROLE_ADMIN')) {
                    return $this->redirectToRoute('admin_dashboard', ['id' => $menu->getId()]);
                } else {
                    return $this->redirectToRoute('employe_dashboard', ['id' => $menu->getId()]);
                }
        }

        // Formulaire invalide ou pas soumis
        if ($request->isXmlHttpRequest() && $form->isSubmitted()) {
            $errors = [];
            foreach ($form->getErrors(true, false) as $error) {
                $errors[] = $error->getMessage();
            }
            return $this->json(['success' => false, 'errors' => $errors]);
        }

        // Affichage normal
        $conditions = $this->em->getRepository(Condition::class)->findAll();

        return $this->render('admin/menu/new.html.twig', [
            'menu' => $menu,
            'form' => $form->createView(),
            'conditions' => $conditions,
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function edit(Menu $menu, Request $request): Response
    {
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $menu->setModifiePar($this->getUser());
            $menu->setDateModif(new DateTime());
            $this->em->flush();

            $this->addFlash('success', 'Menu modifié avec succès !');
            if ($this->isGranted('ROLE_ADMIN')) {
                    return $this->redirectToRoute('admin_dashboard', ['id' => $menu->getId()]);
                } else {
                    return $this->redirectToRoute('employe_dashboard', ['id' => $menu->getId()]);
                }
        }

        $conditions = $this->em->getRepository(Condition::class)->findAll();

        return $this->render('admin/menu/form.html.twig', [
            'menu' => $menu,
            'form' => $form->createView(),
            'conditions' => $conditions,
        ]);
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function delete(Request $request, Menu $menu): Response
    {
        if (!$this->isCsrfTokenValid('delete'.$menu->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException();
        }

        $this->em->remove($menu);
        $this->em->flush();

        $this->addFlash('success', 'Menu supprimé !');
        if ($this->isGranted('ROLE_ADMIN')) {
                    return $this->redirectToRoute('admin_dashboard');
                } else {
                    return $this->redirectToRoute('employe_dashboard');
                }
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function list(Request $request): Response
    {
        $themeId = $request->query->get('theme');
        $regimeId = $request->query->get('regime');
        $keyword = $request->query->get('keyword');

        $qb = $this->menuRepository->createQueryBuilder('m')
            ->leftJoin('m.theme', 't')
            ->leftJoin('m.regime', 'r')
            ->addSelect('t', 'r')
            ->orderBy('m.dateModif', 'DESC');

        if ($themeId) $qb->andWhere('t.id = :themeId')->setParameter('themeId', $themeId);
        if ($regimeId) $qb->andWhere('r.id = :regimeId')->setParameter('regimeId', $regimeId);
        if ($keyword) $qb->andWhere('m.nom LIKE :keyword')->setParameter('keyword', '%' . $keyword . '%');

        $menus = $qb->getQuery()->getResult();
        $themes = $this->em->getRepository(Theme::class)->findAll();
        $regimes = $this->em->getRepository(Regime::class)->findAll();

        return $this->render('admin/menu/list.html.twig', [
            'menus' => $menus,
            'themes' => $themes,
            'selectedTheme' => $themeId,
            'regimes' => $regimes,
            'selectedRegime' => $regimeId,
            'keyword' => $keyword,
        ]); 
    }

    #[IsGranted('ROLE_EMPLOYE')]
    public function show(Menu $menu): Response
    {
        return $this->render('admin/menu/show.html.twig', [
            'menu' => $menu,
        ]);
    }
}