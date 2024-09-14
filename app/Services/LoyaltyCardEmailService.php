<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Models\Client;
use App\Services\LoyaltyCardMail;
use Illuminate\Support\Facades\Mail;

class LoyaltyCardEmailService
{
    /**
     * Send the loyalty card email to the user or client.
     *
     * @param Client $client
     * @param User|null $user
     * @param string $loyaltyCardPath
     * @throws Exception
     */
    public function sendLoyaltyCardEmail(Client $client, ?User $user, string $loyaltyCardPath)
    {
        $email = $user ? $user->email : $client->email;

        if (!$email) {
            throw new Exception("Aucune adresse e-mail disponible pour envoyer la carte de fidélité.");
        }

        Mail::to($email)->send(new LoyaltyCardMail($client, $loyaltyCardPath));
    }
}
