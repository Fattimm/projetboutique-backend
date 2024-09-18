<?php

namespace App\Services\Interfaces;

interface ArchivageService
{
    public function archiverDette($detteId);
    public function afficherDettesArchivees();
    public function restaurerDette($detteId);
    public function restaurerDettesParDate($date);
    public function restaurerDettesParClient($clientId);
     
}
