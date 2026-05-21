<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Dette;

class DettePolicy
{
    public function store(User $user)
    {
        return $user->hasRole('BOUTIQUIER');
    }

    public function index(User $user)
    {
        return $user->hasRole('BOUTIQUIER');
    }

    public function show(User $user)
    {
        return $user->hasRole('BOUTIQUIER');
    }

    public function listArticles(User $user)
    {
        return $user->hasRole('BOUTIQUIER');
    }

    public function listPaiements(User $user)
    {
        return $user->hasRole('BOUTIQUIER');
    }

    public function addPaiement(User $user)
    {
        return $user->hasRole('BOUTIQUIER');
    }

    public function archiver(User $user)
    {
        return $user->hasRole('ADMIN');
    }

    public function afficherDettesArchivees(User $user)
    {
        return $user->hasRole('ADMIN');
    }

    public function restaurer(User $user)
    {
        return $user->hasRole('ADMIN');
    }

    public function restaurerParClient(User $user)
    {
        return $user->hasRole('ADMIN');
    }

    public function restaurerParDate(User $user)
    {
        return $user->hasRole('ADMIN');
    }

}
