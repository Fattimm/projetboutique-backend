<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function store(User $user)
    {
        return $user->hasRole('ADMIN');
    }

    public function index(User $user)
    {
        return $user->hasRole('ADMIN');
    }

    public function deleteAccount(User $user)
    {
        return $user->hasRole('ADMIN');
    }

}
