<?php

require_once __DIR__ . '/../vendor/autoload.php';

use MongoDB\Client as MongoClient;
use MongoDB\BSON\UTCDateTime;

// Connexion MongoDB
$mongoClient = new MongoClient("mongodb://admin:admin123@mongo:27017");
$mongoDB = $mongoClient->selectDatabase("vite_gourmand_stats");
$commandesCollection = $mongoDB->selectCollection("commandes");

// --- Config --- //
$startDate = '2026-01-01 00:00:00';
$endDate   = '2026-01-26 23:59:59';
$startMongo = new UTCDateTime(strtotime($startDate) * 1000);
$endMongo   = new UTCDateTime(strtotime($endDate) * 1000);

// --- Mapping menu_id → nom --- //
$menuMapping = [
    1 => 'Saveurs Enchantées',
    2 => 'Le Festin des Cloches',
    3 => 'Saveurs & Sortilèges',
    4 => 'Minuit Étincelant',
    5 => 'Lune de Miel',
    6 => 'Les Aventuriers',
    7 => 'Lumière & Tendresse',
    8 => 'Festin Carnivore',
    9 => 'Sapori d\'Italia',
    10 => 'Jardin des Délices',
    11 => 'Évasion Asiatique',
    12 => 'Symphonie Maritime',
    13 => 'Palette Végétale',
    14 => 'Nature Sereine',
    15 => 'Délicatesse'
];

// --- Corriger menu.nom si absent --- //
$missingMenus = $commandesCollection->find(['menu.nom' => ['$exists' => false]]);
foreach ($missingMenus as $cmd) {
    $id = $cmd['menu_id'] ?? null;
    if ($id && isset($menuMapping[$id])) {
        $commandesCollection->updateOne(
            ['_id' => $cmd['_id']],
            ['$set' => ['menu.nom' => $menuMapping[$id]]]
        );
    }
}

echo "✅ Menu.nom corrigé pour les commandes manquantes.\n";

// --- Nombre de commandes par menu --- //
$pipelineCount = [
    ['$addFields' => [
        'dateCommandeDate' => ['$dateFromString' => ['dateString' => '$dateCommande']]
    ]],
    ['$match' => ['dateCommandeDate' => ['$gte' => $startMongo, '$lte' => $endMongo]]],
    ['$group' => ['_id' => '$menu.nom', 'nombreCommandes' => ['$sum' => 1]]],
    ['$sort' => ['nombreCommandes' => -1]]
];

$resultsCount = $commandesCollection->aggregate($pipelineCount);

echo "\n📊 Nombre de commandes par menu :\n";
foreach ($resultsCount as $menu) { 
    echo "- {$menu->_id} : {$menu->nombreCommandes}\n";
}

// --- Chiffre d'affaires global --- //
$pipelineRevenue = [
    ['$addFields' => [
        'dateCommandeDate' => ['$dateFromString' => ['dateString' => '$dateCommande']],
        'prixTotalDouble' => ['$toDouble' => '$prixTotal']
    ]],
    ['$match' => ['dateCommandeDate' => ['$gte' => $startMongo, '$lte' => $endMongo]]],
    ['$group' => ['_id' => null, 'caGlobal' => ['$sum' => '$prixTotalDouble']]]
];

$result = $commandesCollection->aggregate($pipelineRevenue)->toArray();
$caGlobal = $result[0]['caGlobal'] ?? 0;

echo "\n💰 CA global du $startDate au $endDate : " . number_format($caGlobal, 2, ',', ' ') . " €\n";