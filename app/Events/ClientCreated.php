<?php

namespace App\Events;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClientCreated
{
    use Dispatchable, SerializesModels;

    public $client;
    public $user;
    public $photoPath;

    public function __construct(Client $client, User $user = null, $photoPath)
    {
        $this->client = $client;
        $this->user = $user;
        $this->photoPath = $photoPath;
    }
}
