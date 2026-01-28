<?php

namespace App\EventListener;

use App\Entity\Commande;
use App\Service\MongoService;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class CommandeListener
{
    private $mongoService;

    public function __construct(MongoService $mongoService)
    {
        $this->mongoService = $mongoService;
    }

    public function postPersist(Commande $commande, LifecycleEventArgs $args)
    {
        $this->sendToMongo($commande);
    }

    public function postUpdate(Commande $commande, LifecycleEventArgs $args)
    {
        $this->sendToMongo($commande);
    }

    private function sendToMongo(Commande $commande)
    {
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

        $this->mongoService->upsertCommande($doc);
    }
}