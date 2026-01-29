<?php

namespace App\Controller\Admin;

use MongoDB\Client;
use MongoDB\BSON\UTCDateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DashboardAdminController extends AbstractController
{
    private Client $mongoClient;
    private $collection;

    // Tous les menus connus
    private array $allMenus = [
        'Saveurs Enchantées',
        'Le Festin des Cloches',
        'Saveurs & Sortilèges',
        'Minuit Étincelant',
        'Lune de Miel',
        'Les Aventuriers',
        'Lumière & Tendresse',
        'Festin Carnivore', 
        'Sapori d\'Italia',
        'Jardin des Délices',
        'Évasion Asiatique',
        'Symphonie Maritime',
        'Palette Végétale',
        'Nature Sereine',
        'Délicatesse'
    ];

    // Constructor : MongoDB injecté
    public function __construct(Client $client)
    {
        $this->mongoClient = $client;
        $this->collection = $this->mongoClient
            ->selectDatabase('vite_gourmand_stats')
            ->selectCollection('commandes');
    }

    // PAGE ADMIN DASHBOARD
    public function index(): Response
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    // PAGE STATS
    public function statsPage(): Response
    {
        $validStatuses = ['Acceptée', 'En attente de retour du matériel', 'En livraison', 'En préparation', 'Livrée', 'Terminée'];

        $pipeline = [
            ['$addFields' => [
                'dateCommandeDate' => ['$dateFromString' => ['dateString' => '$dateCommande']],
                'prixTotalDouble' => ['$toDouble' => '$prixTotal'],
                'menuNomString' => ['$toString' => ['$ifNull' => ['$menu.nom', 'INCONNU']]]
            ]],
            ['$match' => [
                'dateCommandeDate' => ['$gte' => new UTCDateTime(strtotime('2026-01-01 00:00:00') * 1000)],
                'statutCommande' => ['$in' => $validStatuses]
            ]],
            ['$group' => [
                '_id' => null,
                'caTotal' => ['$sum' => '$prixTotalDouble']
            ]]
        ];

        $caResult = $this->collection->aggregate($pipeline)->toArray();
        $caGlobal = $caResult[0]->caTotal ?? 0;

        $menusForSelect = array_map(fn($name) => ['nom' => $name], $this->allMenus);

        return $this->render('admin/stats/stats.html.twig', [
            'menus' => $menusForSelect,
            'caGlobal' => $caGlobal
        ]);
    }

    // API CHART.JS
    public function getStatsData(Request $request): JsonResponse
    {
        $validStatuses = ['Acceptée', 'En attente de retour du matériel', 'En livraison', 'En préparation', 'Livrée', 'Terminée'];

        $start = new UTCDateTime(strtotime($request->query->get('start', '2026-01-01').' 00:00:00') * 1000);
        $end   = new UTCDateTime(strtotime($request->query->get('end', date('Y-m-d')).' 23:59:59') * 1000);

        $menusFilter = array_filter(
            $request->query->all('menu'), 
            fn($menu) => in_array($menu, $this->allMenus, true)
        );

        $match = [
            'statutCommande' => ['$in' => $validStatuses],
            'dateCommandeDate' => ['$gte' => $start, '$lte' => $end]
        ];

        if (!empty($menusFilter)) {
            $match['menuNomString'] = ['$in' => $menusFilter];
        }

        $pipeline = [
            ['$addFields' => [
                'prixTotalDouble' => ['$toDouble' => '$prixTotal'],
                'menuNomString' => ['$toString' => ['$ifNull' => ['$menu.nom', 'INCONNU']]],
                'dateCommandeDate' => ['$ifNull' => ['$dateCommandeDate', ['$dateFromString' => ['dateString' => '$dateCommande']]]]
            ]],
            ['$match' => $match],
            ['$group' => [
                '_id' => '$menuNomString',
                'count' => ['$sum' => 1],
                'ca' => ['$sum' => '$prixTotalDouble']
            ]]
        ];

        $data = $this->collection->aggregate($pipeline)->toArray();

        $labels = [];
        $counts = [];
        $ca = [];

        foreach ($this->allMenus as $menu) {
            $found = false;
            foreach ($data as $row) {
                if ($row->_id === $menu) {
                    $counts[] = $row->count;
                    $ca[] = $row->ca;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $counts[] = 0;
                $ca[] = 0;
            }
            $labels[] = $menu;
        }

        return new JsonResponse([
            'labels' => $labels,
            'counts' => $counts,
            'ca' => $ca
        ]);
    }
}