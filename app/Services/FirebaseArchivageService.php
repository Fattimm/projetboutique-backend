<?php

namespace App\Services;

use Exception;
use App\Models\Dette;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;
use App\Services\Interfaces\ArchivageService;

class FirebaseArchivageService implements ArchivageService
{
    protected $firebase;

    public function __construct()
    {
        $this->firebase = (new Factory())->withServiceAccount(__DIR__ . '/path-to-firebase-credentials.json')->createDatabase();
    }

    public function archiverDette($detteId)
    {
        try {
        $dette = Dette::with('client', 'articles', 'paiements')->findOrFail($detteId);
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

        $this->firebase->getReference('archive_dette/' . $detteId)->set($dette->toArray());


        // Suppression locale après archivage
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
        return $this->firebase->getReference('archive_dette')->getValue();
    }

    public function restaurerDette($detteId)
    {
        $detteData = $this->firebase->getReference('archive_dette/' . $detteId)->getValue();
        Dette::create($detteData);  // Restauration locale
        $this->firebase->getReference('archive_dette/' . $detteId)->remove();  // Suppression cloud
    }

    public function restaurerDettesParDate($date)
    {

    }

    public function restaurerDettesParClient($clientId)
    {

    }
}
