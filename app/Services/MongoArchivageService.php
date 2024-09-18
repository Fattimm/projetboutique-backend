<?php

namespace App\Services;

use Exception;
use MongoDB\Client;
use App\Models\Dette;
use App\Models\Article;
use App\Models\Paiement;
use Illuminate\Support\Facades\Log;
use App\Services\Interfaces\ArchivageService;

class MongoArchivageService implements ArchivageService
{
    protected $mongo;

    public function __construct()
    {
        $this->mongo = (new Client('mongodb+srv://diamata998:qPDUuA1WJoF6CKY7@cluster0.b1mt2.mongodb.net/maboutique?retryWrites=true&w=majority&appName=Cluster0'))
            ->selectDatabase('maboutique');
    }

    public function archiverDette($detteId)
    {
        try {
            // 1. Récupérer la dette avec les paiements associés
            $dette = Dette::with('client', 'articles', 'paiements')->findOrFail($detteId);

            // 2. Calculer le total des paiements
            $totalPaye = $dette->paiements->sum('montant');

            // 3. Vérifier si la dette est complètement payée
            if ($totalPaye < $dette->montant) {
                return [
                    'status' => 400,
                    'message' => "La dette n'est pas entièrement payée. Veuillez vérifier les paiements."
                ];
            }

            // 4. Convertir la dette en tableau pour archivage et ajouter des champs supplémentaires
            $detteArray = $dette->toArray();
            $detteArray['_id'] = (string) $detteId;  // Transformer l'ID en chaîne de caractères pour MongoDB
            $detteArray['date_archivage'] = now()->toDateTimeString();  // Ajouter la date d'archivage

            // 5. Insérer la dette archivée dans MongoDB
            $collectionName = 'archive_dettes_' . now()->format('Y_m_d');
            $result = $this->mongo->$collectionName->insertOne($detteArray);

            // 6. Vérifier si l'insertion a réussi
            if ($result->getInsertedCount() === 0) {
                throw new Exception("Échec de l'insertion dans MongoDB");
            }

            // 7. Supprimer la dette de la base de données relationnelle
            $dette->delete();

            // 8. Log du succès et retour de la réponse
            Log::info("Dette ID {$detteId} archivée avec succès dans MongoDB");

            return [
                'status' => 200,
                'message' => "Dette archivée avec succès"
            ];
        } catch (Exception $e) {
            // Gestion des erreurs
            Log::error("Erreur lors de l'archivage de la dette ID {$detteId}: " . $e->getMessage());
            return [
                'status' => 500,
                'message' => "Erreur lors de l'archivage de la dette : " . $e->getMessage()
            ];
        }
    }

    public function afficherDettesArchivees()
    {
        try {
            // Récupération des dettes archivées
            $dettes = $this->mongo->archive_dette->find()->toArray();

            return [
                'status' => 200,
                'data' => $dettes,
                'message' => "Liste des dettes archivées récupérée avec succès"
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => "Erreur lors de la récupération des dettes archivées : " . $e->getMessage()
            ];
        }
    }
    

    public function restaurerDette($detteId)
    {
        try {
            // Recherche de la dette archivée
            $detteData = $this->mongo->archive_dette->findOne(['_id' => $detteId]);
    
            if (!$detteData) {
                throw new Exception("Dette non trouvée dans les archives");
            }
    
            // Convertir BSONDocument en tableau
            $detteArray = json_decode(json_encode($detteData), true); // Conversion en tableau
    
            // Restauration dans la base locale
            Dette::create($detteArray);
    
            // Suppression de la dette dans les archives MongoDB
            $this->mongo->archive_dette->deleteOne(['_id' => $detteId]);
    
            return [
                'status' => 200,
                'message' => "Dette restaurée avec succès"
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => "Erreur lors de la restauration de la dette : " . $e->getMessage()
            ];
        }
    }


    public function restaurerDettesParDate($date)
    {
        try {
            // Implémentation pour restaurer les dettes archivées à une date donnée
            // Exemple de logique que vous pourriez utiliser :
            $dettesData = $this->mongo->archive_dette->find(['date_archivage' => $date])->toArray();

            foreach ($dettesData as $detteData) {
                Dette::create($detteData->toArray());
                $this->mongo->archive_dette->deleteOne(['_id' => $detteData['_id']]);
            }

            return [
                'status' => 200,
                'message' => "Dettes restaurées avec succès pour la date $date"
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => "Erreur lors de la restauration des dettes pour la date $date : " . $e->getMessage()
            ];
        }
    }

    public function restaurerDettesParClient($clientId)
    {
        try {
            // Implémentation pour restaurer toutes les dettes d'un client
            $dettesData = $this->mongo->archive_dette->find(['client_id' => $clientId])->toArray();

            foreach ($dettesData as $detteData) {
                Dette::create($detteData->toArray());
                $this->mongo->archive_dette->deleteOne(['_id' => $detteData['_id']]);
            }

            return [
                'status' => 200,
                'message' => "Toutes les dettes du client $clientId ont été restaurées"
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'message' => "Erreur lors de la restauration des dettes du client $clientId : " . $e->getMessage()
            ];
        }
    }
}
