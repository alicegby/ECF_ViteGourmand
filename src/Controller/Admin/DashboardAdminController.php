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

    public function index(): Response
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    // Collection MongoDB
    private function collection()
    {
        return (new Client("mongodb://admin:admin123@mongo:27017"))
            ->selectDatabase('vite_gourmand_stats')
            ->selectCollection('commandes');
    }

    // Tous les menus connus (pour le select)
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

    // Dashboard principal
    public function statsPage(): Response
    {
        $collection = $this->collection();

        // Dates par défaut
        $start = new UTCDateTime(strtotime('2026-01-01 00:00:00') * 1000);
        $validStatuses = ['Acceptée', 'En attente de retour du matériel', 'En livraison', 'En préparation', 'Livrée', 'Terminée'];

        // CA global depuis 01/01/2026
        $pipeline = [
            ['$addFields' => [
                'dateCommandeDate' => ['$dateFromString' => ['dateString' => '$dateCommande']],
                'prixTotalDouble' => ['$toDouble' => '$prixTotal'],
                'menuNomString' => ['$toString' => ['$ifNull' => ['$menu.nom', 'INCONNU']]]
            ]],
            ['$match' => [
                'dateCommandeDate' => ['$gte' => $start],
                'statutCommande' => ['$in' => $validStatuses]
            ]],
            ['$group' => [
                '_id' => null,
                'caTotal' => ['$sum' => '$prixTotalDouble']
            ]]
        ];

        $caResult = $collection->aggregate($pipeline)->toArray();
        $caGlobal = $caResult[0]->caTotal ?? 0;

        // Préparer le select menu
        $menusForSelect = array_map(fn($name) => ['nom' => $name], $this->allMenus);

        return $this->render('admin/stats/stats.html.twig', [
            'menus' => $menusForSelect,
            'caGlobal' => $caGlobal
        ]);
    }

    // API pour Chart.js
    public function getStatsData(Request $request): JsonResponse
    {
        $collection = $this->collection();

        $start = new UTCDateTime(strtotime($request->query->get('start', '2026-01-01').' 00:00:00') * 1000);
        $end   = new UTCDateTime(strtotime($request->query->get('end', date('Y-m-d')).' 23:59:59') * 1000);
        $menusFilter = array_filter(
            $request->query->all('menu'), 
            fn($menu) => in_array($menu, $this->allMenus, true)
        );
        $validStatuses = ['Acceptée', 'En attente de retour du matériel', 'En livraison', 'En préparation', 'Livrée', 'Terminée'];

        $match = [
            'statutCommande' => ['$in' => $validStatuses],
            'dateCommandeDate' => ['$gte' => $start, '$lte' => $end]
        ];

        if (!empty($menusFilter)) {
            $match['menuNomString'] = ['$in' => $menusFilter];
        }

        $pipeline = [
            ['$addFields' => [
                'dateCommandeDate' => ['$dateFromString' => ['dateString' => '$dateCommande']],
                'prixTotalDouble' => ['$toDouble' => '$prixTotal'],
                'menuNomString' => ['$toString' => ['$ifNull' => ['$menu.nom', 'INCONNU']]]
            ]],
            ['$match' => $match],
            ['$group' => [
                '_id' => '$menuNomString',
                'count' => ['$sum' => 1],
                'ca' => ['$sum' => '$prixTotalDouble']
            ]]
        ];

        $data = $collection->aggregate($pipeline)->toArray();

        // Préparer le chart avec tous les menus, même ceux sans commandes
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