<?php

namespace App\Jobs;

use App\Models\Client;
use App\Models\User;
use App\Services\LoyaltyCardService;
use App\Services\QrCodeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateQrCodeAndLoyaltyCardJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $client;
    public $user;

    public function __construct(Client $client, User $user = null)
    {
        $this->client = $client;
        $this->user = $user;
    }

    public function handle(QrCodeService $qrCodeService, LoyaltyCardService $loyaltyCardService)
    {
        // Générer le QR code
        $qrCodePath = $qrCodeService->generateQrCode($this->client, $this->user);

        // Créer la carte de fidélité
        $loyaltyCardService->createLoyaltyCard($this->client, $qrCodePath, $this->user, $this->user->photopath);
    }
}
