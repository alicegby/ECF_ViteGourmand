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
        return $client
            ->selectDatabase("vite_gourmand_stats")
            ->selectCollection("commandes");
    }

    public function index(): Response
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    /**
     * PAGE STATS
     * - menus distincts
     * - CA global (sans filtres dynamiques)
     */
    public function statsPage(): Response
    {
        $commandes = $this->getMongoCollection();

        // Menus distincts
        $menus = $commandes->distinct('menu.nom', [
            'statutCommande' => [
                '$in' => [
                    'Acceptée',
                    'En attente du matériel',
                    'En livraison',
                    'En préparation',
                    'Livrée',
                    'Terminée'
                ]
            ]
        ]) ?? [];

        // CA global depuis le 01/01/2026
        $startGlobal = new UTCDateTime(strtotime('2026-01-01 00:00:00') * 1000);

        $pipelineCA = [
            [
                '$match' => [
                    'statutCommande' => [
                        '$in' => [
                            'Acceptée',
                            'En attente du matériel',
                            'En livraison',
                            'En préparation',
                            'Livrée',
                            'Terminée'
                        ]
                    ],
                    'prixTotal' => ['$exists' => true, '$ne' => null]
                ]
            ],
            [
                '$addFields' => [
                    'prixTotalDouble' => [
                        '$convert' => [
                            'input' => '$prixTotal',
                            'to' => 'double',
                            'onError' => 0,
                            'onNull' => 0
                        ]
                    ],
                    'dateCommandeDate' => [
                        '$dateFromString' => [
                            'dateString' => '$dateCommande'
                        ]
                    ]
                ]
            ],
            [
                '$match' => [
                    'dateCommandeDate' => ['$gte' => $startGlobal]
                ]
            ],
            [
                '$group' => [
                    '_id' => null,
                    'totalCA' => ['$sum' => '$prixTotalDouble']
                ]
            ]
        ];

        $result = $commandes->aggregate($pipelineCA)->toArray();
        $caGlobal = (!empty($result) && isset($result[0]->totalCA))
            ? (float) $result[0]->totalCA
            : 0;

        return $this->render('admin/stats/stats.html.twig', [
            'menus' => $menus,
            'caGlobal' => $caGlobal,
        ]);
    }

    /**
     * API STATS (Chart.js)
     * - filtres dates
     * - filtres menus
     */
    public function getStatsData(Request $request): JsonResponse
    {
        $start = $request->query->get('start');
        $end = $request->query->get('end');
        $selectedMenus = $request->query->all('menu');

        $commandes = $this->getMongoCollection();

        // Filtre statuts + menus
        $match = [
            'statutCommande' => [
                '$in' => [
                    'Acceptée',
                    'En attente du matériel',
                    'En livraison',
                    'En préparation',
                    'Livrée',
                    'Terminée'
                ]
            ]
        ];

        if (!empty($selectedMenus)) {
            $match['menu.nom'] = ['$in' => $selectedMenus];
        }

        $pipeline = [

            // 1. Filtre statuts / menus
            [
                '$match' => $match
            ],

            // 2. Conversion date + prix
            [
                '$addFields' => [
                    'dateCommandeDate' => [
                        '$dateFromString' => [
                            'dateString' => '$dateCommande'
                        ]
                    ],
                    'prixTotalDouble' => [
                        '$convert' => [
                            'input' => '$prixTotal',
                            'to' => 'double',
                            'onError' => 0,
                            'onNull' => 0
                        ]
                    ]
                ]
            ],

            // 3. Filtrage par période (APRÈS conversion)
            [
                '$match' => array_filter([
                    'dateCommandeDate' => [
                        '$gte' => $start ? new UTCDateTime(strtotime($start . ' 00:00:00') * 1000) : null,
                        '$lte' => $end   ? new UTCDateTime(strtotime($end   . ' 23:59:59') * 1000) : null,
                    ]
                ])
            ],

            // 4. Groupement
            [
                '$group' => [
                    '_id' => '$menu.nom',
                    'count' => ['$sum' => 1],
                    'ca' => ['$sum' => '$prixTotalDouble']
                ]
            ],

            // 5. Tri
            [
                '$sort' => ['count' => -1]
            ]
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