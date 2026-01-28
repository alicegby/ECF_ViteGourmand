<?php 

namespace App\EventListener;

use App\Entity\Commande;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Service\MongoService;

class CommandeListener
{
    private MongoService $mongoService;

    public function __construct(MongoService $mongoService)
    {
        $this->mongoService = $mongoService;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        if (!$entity instanceof Commande) return;

        $this->mongoService->upsertCommande([
            '_id' => $entity->getId(),
            'numeroCommande' => $entity->getNumeroCommande(),
            'client' => $entity->getClient()?->getId(),
            'menu' => [
                'id' => $entity->getMenu()?->getId(),
                'nom' => $entity->getMenu()?->getNom(),
            ],
            'dateCommande' => $entity->getDateCommande()?->format('c'),
            'statutCommande' => $entity->getStatutCommande()?->getLibelle(),
            'prixTotal' => $entity->getPrixTotal(),
        ]);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->postPersist($args); // même logique pour update
    }
}