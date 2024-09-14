<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Client;
use Illuminate\Bus\Queueable;
use App\Services\QrCodeService;
use App\Services\LoyaltyCardService;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\LoyaltyCardEmailService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class SendLoyaltyCardEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $client;
    public $user;
    protected $qrCodeService;
    protected $loyaltyCardService;
    public $photoPath;


    public function __construct(Client $client, User $user = null, $photoPath, QrCodeService $qrCodeService, LoyaltyCardService $loyaltyCardService)
    {
        $this->client = $client;
        $this->user = $user;
        $this->qrCodeService = $qrCodeService;
        $this->loyaltyCardService = $loyaltyCardService;
        $this->photoPath = $photoPath;
    }

    public function handle(LoyaltyCardEmailService $emailService)
    {
        Log::debug('creating Qrcode');
        $qrCodePath = $this->qrCodeService->generateQrCode($this->client, $this->user);
        Log::debug('created Qrcode');


        Log::debug('photo', [
            'photoPath' => $this->photoPath,
        ]);

        Log::debug('generating FidelityCard');
        $loyaltyCardPath = $this->loyaltyCardService->createLoyaltyCard($this->client, $qrCodePath, $this->user, $this->photoPath);
        Log::debug('generated FidelityCard');

        // Send the email with the loyalty card
        $emailService->sendLoyaltyCardEmail($this->client, $this->user, $loyaltyCardPath);
    }
}
