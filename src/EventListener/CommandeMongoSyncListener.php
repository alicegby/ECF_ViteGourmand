<?php

namespace App\EventListener;

use App\Entity\Commande;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use MongoDB\Client as MongoClient;

class CommandeMongoSyncListener
{
    private MongoClient $mongoClient;
    private $collection;

    public function __construct()
    {
        $this->mongoClient = new MongoClient("mongodb://admin:admin123@mongo:27017");
        $this->collection = $this->mongoClient
            ->selectDatabase("vite_gourmand_stats")
            ->selectCollection("commandes");
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof Commande) {
            $this->syncMongo($entity);
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if ($entity instanceof Commande) {
            $this->syncMongo($entity);
        }
    }

    private function syncMongo(Commande $commande): void
    {
        $doc = [];

        $fields = [
            'numeroCommande' => $commande->getNumeroCommande(),
            'client' => $commande->getClient()?->getId(),
            'menu' => [
                'id' => $commande->getMenu()?->getId(),
                'nom' => $commande->getMenu()?->getNom(),
            ],
            'dateCommande' => $commande->getDateCommande()?->format('c'),
            'dateLivraison' => $commande->getDateLivraison()?->format('Y-m-d'),
            'heureLivraison' => $commande->getHeureLivraison()?->format('H:i:s'),
            'nbPersonne' => $commande->getNbPersonne(),
            'prixTotal' => $commande->getPrixTotal(),
            'statutCommande' => $commande->getStatutCommande()?->getLibelle(),
        ];

        foreach ($fields as $key => $value) {
            if ($value !== null) $doc[$key] = $value;
        }

        // Upsert dans MongoDB
        $this->collection->updateOne(
            ['_id' => $commande->getId()],
            ['$set' => $doc],
            ['upsert' => true]
        );
    }
}