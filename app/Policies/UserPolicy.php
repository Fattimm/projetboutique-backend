<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    // Autorisation pour créer un compte utilisateur (ADMIN uniquement)
    public function store(User $user)
    {
        return $user->role === 'ADMIN';
    }

    // Autorisation pour lister les utilisateurs (ADMIN uniquement)
    public function index(User $user)
    {
        return $user->role === 'ADMIN';
    }

}
