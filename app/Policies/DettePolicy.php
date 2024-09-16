<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Dette;

class DettePolicy
{
    public function store(User $user)
    {
        // Seul un boutiquier peut créer une dette
        return $user->role === 'BOUTIQUIER';
    }

    public function index(User $user)
    {
        // Un admin ou un boutiquier peut voir les dettes
        return $user->role === 'BOUTIQUIER';
    }

    public function show(User $user)
    {
        return $user->role === 'BOUTIQUIER';
    }

    public function listArticles(User $user)
    {
        // Seul un boutiquier peut ajouter un paiement à une dette
        return $user->role === 'BOUTIQUIER';
    }

    public function listPaiements(User $user)
    {
        // Un admin ou un boutiquier peut voir les paiements des dettes
        return $user->role === 'BOUTIQUIER';
    }

    public function addPaiement(User $user)
    {
        // Un admin ou un boutiquier peut voir les paiements des dettes
        return $user->role === 'BOUTIQUIER';
    }
}
