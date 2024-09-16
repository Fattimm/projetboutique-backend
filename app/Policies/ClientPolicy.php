<?php


namespace App\Policies;

use App\Models\User;
use App\Models\Client;

class ClientPolicy
{
    // Seul un boutiquier peut voir tous les clients
    public function viewAny(User $user)
    {
        return $user->role === 'BOUTIQUIER';
    }

    // Seul un boutiquier peut créer un client
    public function store(User $user)
    {
        return $user->role === 'BOUTIQUIER';
    }

    public function filterByAccount(User $user)
    {
        return $user->role === 'BOUTIQUIER';
    }

    public function filterByStatus(User $user)
    {
        return $user->role === 'BOUTIQUIER';
    }

    public function searchByTelephone(User $user)
    {
        return $user->role === 'BOUTIQUIER';
    }

    public function deleteAccount(User $user)
    {
        return $user->role === 'BOUTIQUIER';
    }

    public function show(User $user)
    {
        return $user->role === 'BOUTIQUIER';
    }

    public function getClientWithUser(User $user)
    {
        return $user->role === 'BOUTIQUIER';
    }

    public function getClientDettes(User $user)
    {
        return $user->role === 'BOUTIQUIER';
    }
}
