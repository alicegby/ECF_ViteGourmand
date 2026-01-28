<?php

namespace App\Service;

use MongoDB\Client;

class MongoService
{
    private $client;
    private $collection;

    public function __construct()
    {
        $this->client = new Client("mongodb://admin:admin123@mongo:27017");
        $this->collection = $this->client
            ->selectDatabase('vite_gourmand_stats')
            ->selectCollection('commandes');
    }

    public function upsertCommande(array $data)
    {
        $this->collection->updateOne(
            ['_id' => $data['_id']],
            ['$set' => $data],
            ['upsert' => true]
        );
    }
}