<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use MongoDB\Client as MongoClient;
use MongoDB\BSON\UTCDateTime;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class DashboardAdminController extends AbstractController
{
    private function getMongoCollection()
    {
        $client = new MongoClient("mongodb://admin:admin123@mongo:27017");
        return $client->selectDatabase("vite_gourmand_stats")->selectCollection("commandes");
    }

    public function index(): Response
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    public function statsPage(): Response
    {
        $commandes = $this->getMongoCollection();

        // Récupérer tous les menus distincts
        $menus = $commandes->distinct('menu.nom', [
            'statutCommande' => [
                '$in' => ['Acceptée', 'En attente de retour du matériel', 'En livraison', 'En préparation', 'Livrée', 'Terminée']
            ]
        ]) ?? [];

        // Chiffre d'affaires global depuis le 01/01/2026
        $startGlobal = new UTCDateTime(strtotime('2026-01-01') * 1000);
        $pipeline = [
            ['$match' => [
                'statutCommande' => [
                    '$in' => ['Acceptée', 'En attente du matériel', 'En livraison', 'En préparation', 'Livrée', 'Terminée']
                ],
                'dateCommande' => ['$gte' => $startGlobal]
            ]],
            ['$group' => ['_id' => null, 'totalCA' => ['$sum' => '$prixTotal']]]
        ];
        $caGlobalResult = $commandes->aggregate($pipeline)->toArray();
        $caGlobal = $caGlobalResult[0]->totalCA ?? 0;

        return $this->render('admin/stats/stats.html.twig', [
            'menus' => $menus,
            'caGlobal' => $caGlobal,
        ]);
    }

    public function getStatsData(Request $request): JsonResponse
    {
        $start = $request->query->get('start');
        $end = $request->query->get('end');
        $selectedMenus = $request->query->all('menu'); // tableau si plusieurs menus

        $commandes = $this->getMongoCollection();

        $filter = [
            'statutCommande' => [
                '$in' => ['Acceptée', 'En attente du matériel', 'En livraison', 'En préparation', 'Livrée', 'Terminée']
            ]
        ];

        if ($start || $end) {
            $filter['dateCommande'] = [];
            if ($start) $filter['dateCommande']['$gte'] = new UTCDateTime(strtotime($start) * 1000);
            if ($end) $filter['dateCommande']['$lte'] = new UTCDateTime(strtotime($end) * 1000);
        }

        if (!empty($selectedMenus)) {
            $filter['menu.nom'] = ['$in' => $selectedMenus];
        }

        $pipeline = [
            ['$match' => $filter],
            ['$group' => [
                '_id' => '$menu.nom',
                'count' => ['$sum' => 1],
                'ca' => ['$sum' => '$prixTotal']
            ]],
            ['$sort' => ['count' => -1]]
        ];

        $results = $commandes->aggregate($pipeline);

        $labels = [];
        $counts = [];
        $ca = [];

        foreach ($results as $r) {
            $labels[] = $r->_id ?? 'Inconnu';
            $counts[] = (int) ($r->count ?? 0);
            $ca[] = (float) ($r->ca ?? 0);
        }

        return $this->json([
            'labels' => $labels,
            'counts' => $counts,
            'ca' => $ca
        ]);
    }
}