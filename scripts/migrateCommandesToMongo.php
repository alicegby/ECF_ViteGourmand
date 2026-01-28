<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Entity\Commande;
use Symfony\Component\Dotenv\Dotenv; 
use MongoDB\Client as MongoClient;

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env.local');

// Connexion MongoDB
$client = new MongoClient("mongodb://admin:admin123@mongo:27017");
$collection = $client->selectDatabase("vite_gourmand_stats")->selectCollection("commandes");

// Boot Symfony pour accéder à l'EntityManager
$kernel = new \App\Kernel('dev', true);
$kernel->boot();
$entityManager = $kernel->getContainer()->get('doctrine')->getManager();

// Récupérer toutes les commandes SQL
$commandes = $entityManager->getRepository(Commande::class)->findAll();

// Migration vers MongoDB
foreach ($commandes as $commande) { 
    $doc = [
        '_id' => $commande->getId(),
        'numeroCommande' => $commande->getNumeroCommande(),
        'client' => $commande->getClient()?->getId(),
        'menu' => [
            'id' => $commande->getMenu()?->getId(),
            'nom' => $commande->getMenu()?->getNom(),
        ],
        'dateCommande' => $commande->getDateCommande()?->format('c'),
        'statutCommande' => $commande->getStatutCommande()?->getLibelle(),
        'prixTotal' => $commande->getPrixTotal(),
    ];

    $collection->updateOne(
        ['_id' => $commande->getId()],
        ['$set' => $doc],
        ['upsert' => true]
    );
}

echo "Toutes les commandes SQL ont été migrées vers MongoDB (menus seulement).\n";