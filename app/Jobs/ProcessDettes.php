<?php

namespace App\Jobs;

use App\Models\Dette;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\FirebaseArchivageService;
use App\Services\MongoArchivageService;
use Illuminate\Support\Facades\DB;

class ProcessDettes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $dette;
    protected $firebaseArchivageService;
    protected $mongoArchivageService;

    /**
     * Create a new job instance.
     *
     * @param Dette $dette
     * @param FirebaseArchivageService $firebaseArchivageService
     * @param MongoArchivageService $mongoArchivageService
    */
    public function __construct(Dette $dette, FirebaseArchivageService $firebaseArchivageService, MongoArchivageService $mongoArchivageService)
    {
        $this->dette = $dette;
        $this->firebaseArchivageService = $firebaseArchivageService;
        $this->mongoArchivageService = $mongoArchivageService;
    }

    /**
     * Execute the job. 
     *
     * @return void
     */
    public function handle()
    {
        DB::beginTransaction();

        try {
            // Archiver sur Firebase
            $this->firebaseArchivageService->archiverDette($this->dette);

            // Archiver sur MongoDB
            $this->mongoArchivageService->archiverDette($this->dette);

            // Supprimer la dette du stockage local après archivage
            $this->dette->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de l'archivage de la dette ID {$this->dette->id}: " . $e->getMessage());
        }
    }
}