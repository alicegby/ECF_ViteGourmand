<?php 

namespace App\EventListener;

use App\Entity\Commande;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use App\Service\MongoService;
use MongoDB\BSON\UTCDateTime;

class CommandeListener
{
    private MongoService $mongoService;

    private array $validStatuses = [
        'Acceptée',
        'En attente de retour du matériel',
        'En livraison',
        'En préparation',
        'Livrée',
        'Terminée'
    ];

    public function __construct(MongoService $mongoService)
    {
        $this->mongoService = $mongoService;
    }

        public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof Commande) return;

        $this->syncCommande($entity);
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof Commande) return;

        $this->syncCommande($entity);
    }

    private function syncCommande(Commande $entity): void
    {

        $statut = trim($entity->getStatutCommande()?->getLibelle() ?? '');
        if (!in_array($statut, $this->validStatuses, true)) {
            return; // Ignore les statuts non valides
        }
        
        $dateCommande = $entity->getDateCommande();
        $dateUTC = $dateCommande ? new \MongoDB\BSON\UTCDateTime($dateCommande->getTimestamp() * 1000) : null;

        $this->mongoService->upsertCommande([
            '_id' => $entity->getId(),
            'numeroCommande' => $entity->getNumeroCommande(),
            'client' => $entity->getClient()?->getId(),
            'menu' => [
                'id' => $entity->getMenu()?->getId(),
                'nom' => $entity->getMenu()?->getNom(),
            ],
            'dateCommande' => $dateCommande?->format(\DateTime::ATOM), 
            'dateCommandeDate' => $dateUTC, 
            'statutCommande' => $statut,
            'prixTotal' => $entity->getPrixTotal(),
        ]);
    }
}