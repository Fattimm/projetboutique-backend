<?php

namespace App\Services\Repository;

use MongoDB\Client;

class MongoArchivageRepositoryImpl
{
    protected $mongo;

    public function __construct()
    {
        $this->mongo = (new Client('mongodb+srv://diamata998:qPDUuA1WJoF6CKY7@cluster0.b1mt2.mongodb.net/maboutique?retryWrites=true&w=majority&appName=Cluster0'))
            ->selectDatabase('maboutique');
    }

    // Insérer une dette archivée
    public function insertArchivedDette(array $detteData)
    {
        $collectionName = 'archive_dettes_' . now()->format('Y_m_d');
        return $this->mongo->$collectionName->insertOne($detteData);
    }

    // Récupérer une dette archivée par ID
    public function findArchivedDetteById($detteId)
    {
        return $this->mongo->archive_dette->findOne(['_id' => $detteId]);
    }

    // Récupérer toutes les dettes archivées
    public function findAllArchivedDettes()
    {
        return $this->mongo->archive_dette->find()->toArray();
    }

    // Supprimer une dette archivée par ID
    public function deleteArchivedDetteById($detteId)
    {
        return $this->mongo->archive_dette->deleteOne(['_id' => $detteId]);
    }

    // Récupérer les dettes archivées par date d'archivage
    public function findArchivedDettesByDate($date)
    {
        return $this->mongo->archive_dette->find(['date_archivage' => $date])->toArray();
    }

    // Récupérer les dettes archivées par client ID
    public function findArchivedDettesByClientId($clientId)
    {
        return $this->mongo->archive_dette->find(['client_id' => $clientId])->toArray();
    }
}
