<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoyaltyCardMail extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $loyaltyCardPath;

    public function __construct(Client $client, string $loyaltyCardPath)
    {
        $this->client = $client;
        $this->loyaltyCardPath = $loyaltyCardPath;
    }

    public function build()
    {
        return $this->view('emails.loyalty-card')
                    ->subject('Votre carte de fidélité')
                    ->attach($this->loyaltyCardPath, [
                        'as' => 'carte_fidelite.pdf',
                        'mime' => 'application/pdf',
                    ]);
    }
}
