<?php

namespace App\Service;

use MongoDB\Client;

class MongoService
{
    private Client $client;
    private $collection;

    public function __construct(string $mongoUrl)
    {
        $this->client = new Client($mongoUrl);
        $this->collection = $this->client
            ->selectDatabase('vite_gourmand_stats')
            ->selectCollection('commandes');
    }

    public function upsertCommande(array $data): void
    {
        $this->collection->replaceOne(['_id' => $data['_id']], $data, ['upsert' => true]);
    }
}