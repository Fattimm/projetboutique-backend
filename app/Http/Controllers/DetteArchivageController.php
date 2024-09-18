<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MongoArchivageService;
use App\Models\Dette;
use App\Services\Interfaces\ArchivageService;

class DetteArchivageController extends Controller
{
    protected $archivageService;

    public function __construct(ArchivageService $archivageService)
    {
        $this->archivageService = $archivageService;
    }

    // Archiver une dette spécifique
    public function archiver($id)
    {
        $this->authorize('archiver', Dette::class);
        return $this->archivageService->archiverDette($id);
    }

    // Afficher toutes les dettes archivées
    public function afficherDettesArchivees()
    {
        $this->authorize('afficherDettesArchivees', Dette::class);
        return $dettes = $this->archivageService->afficherDettesArchivees();
    }

    // Restaurer une dette spécifique depuis l'archive
    public function restaurer($id)
    {
        $this->authorize('restaurer', Dette::class);
        return $this->archivageService->restaurerDette($id);
    }

    // Restaurer toutes les dettes d'un client spécifique depuis l'archive
    public function restaurerParClient($clientId)
    {
        $this->authorize('restaurerParClient', Dette::class);
        return $this->archivageService->restaurerDettesParClient($clientId);
    }

    // Restaurer les dettes archivées à une date donnée
    public function restaurerParDate(Request $request)
    {
        $this->authorize('restaurerParDate', Dette::class);
        $date = $request->input('date');
        return $this->archivageService->restaurerDettesParDate($date);
    }
    
}
