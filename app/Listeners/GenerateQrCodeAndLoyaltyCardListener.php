<?php

namespace App\Listeners;

use App\Events\ClientCreated;
use App\Jobs\SendLoyaltyCardEmailJob;
use App\Services\QrCodeService;
use App\Services\LoyaltyCardService;
use App\Services\LoyaltyCardEmailService;

class GenerateQrCodeAndLoyaltyCardListener
{
    protected $qrCodeService;
    protected $loyaltyCardService;
    protected $emailService;

    public function __construct(QrCodeService $qrCodeService, LoyaltyCardService $loyaltyCardService, LoyaltyCardEmailService $emailService)
    {
        $this->qrCodeService = $qrCodeService;
        $this->loyaltyCardService = $loyaltyCardService;
        $this->emailService = $emailService;
    }

    public function handle(ClientCreated $event)
    {
        // Dispatch du job d'envoi d'e-mail avec les services nécessaires
        SendLoyaltyCardEmailJob::dispatch(
            $event->client,
            $event->user,
            $event->photoPath,
            $this->qrCodeService,
            $this->loyaltyCardService,
            $this->emailService
        );
    }
}
