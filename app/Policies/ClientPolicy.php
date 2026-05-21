<?php


namespace App\Policies;

use App\Models\User;
use App\Models\Client;

class ClientPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasRole('BOUTIQUIER');
    }

    public function store(User $user)
    {
        return $user->hasRole('BOUTIQUIER');
    }

    public function filterByAccount(User $user)
    {
        return $user->hasRole('BOUTIQUIER');
    }

    public function filterByStatus(User $user)
    {
        return $user->hasRole('BOUTIQUIER');
    }

    public function searchByTelephone(User $user)
    {
        return $user->hasRole('BOUTIQUIER');
    }

    public function deleteAccount(User $user)
    {
        return $user->hasRole('BOUTIQUIER');
    }

    public function show(User $user)
    {
        return $user->hasRole('BOUTIQUIER');
    }

    public function getClientWithUser(User $user)
    {
        return $user->hasRole('BOUTIQUIER');
    }

    public function getClientDettes(User $user)
    {
        return $user->hasRole('BOUTIQUIER');
    }

    public function register(User $user)
    {
        return $user->hasRole('ADMIN');
    }
}
