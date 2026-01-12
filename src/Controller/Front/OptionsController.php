<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\CategoryCheese;
use App\Entity\CategoryDrink; 
use App\Entity\CategoryMateriel; 
use App\Entity\CategoryPersonnel; 
use Doctrine\Persistence\ManagerRegistry;

class OptionsController extends AbstractController
{
    // ======================
    // PAGE FROMAGES
    // ======================
    public function fromages(ManagerRegistry $doctrine): Response
    {
        $categories = $doctrine->getRepository(CategoryCheese::class)->findAll();

        return $this->render('front/fromages.html.twig', [
            'categories' => $categories,
        ]);
    }

    public function saveFromagesSession(Request $request): JsonResponse
    {
        return $this->saveOptionSession($request, 'fromages_selections');
    }

    // ======================
    // PAGE BOISSONS
    // ======================
    public function boissons(ManagerRegistry $doctrine): Response
    {
        $categories = $doctrine->getRepository(CategoryDrink::class)->findAll();

        return $this->render('front/boissons.html.twig', [
            'categories' => $categories,
        ]);
    }

    public function saveBoissonsSession(Request $request): JsonResponse
    {
        return $this->saveOptionSession($request, 'boissons_selections');
    }

    // ======================
    // PAGE MATERIEL
    // ======================
    public function materiel(ManagerRegistry $doctrine): Response
    {
        $categories = $doctrine->getRepository(CategoryMateriel::class)->findAll();

        return $this->render('front/materiel.html.twig', [
            'categories' => $categories,
        ]);
    }

    public function saveMaterielSession(Request $request): JsonResponse
    {
        return $this->saveOptionSession($request, 'materiel_selections');
    }

    // ======================
    // PAGE PERSONNEL
    // ======================
    public function personnel(ManagerRegistry $doctrine): Response
    {
        $categories = $doctrine->getRepository(CategoryPersonnel::class)->findAll();

        return $this->render('front/personnel.html.twig', [
            'categories' => $categories,
        ]);
    }

    public function savePersonnelSession(Request $request): JsonResponse
    {
        return $this->saveOptionSession($request, 'personnel_selections');
    }

    // ======================
    // MÉTHODE PRIVÉE DE SAUVEGARDE (FUSION DES DOUBLONS)
    // ======================
    private function saveOptionSession(Request $request, string $sessionKey): JsonResponse
    {
        $selections = json_decode($request->getContent(), true);
        $session = $request->getSession();
        $existing = $session->get($sessionKey, []);

        foreach ($selections as $sel) {
            $found = false;
            foreach ($existing as &$ex) {
                if ($ex['id'] == $sel['id']) {
                    $ex['qty'] += $sel['qty']; // ajoute les quantités si déjà présent
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $existing[] = $sel;
            }
        }

        $session->set($sessionKey, $existing);

        return new JsonResponse(['success' => true]);
    }
}