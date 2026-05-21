<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Article;

class ArticlePolicy
{
    public function store(User $user)
    {
        return $user->hasRole('BOUTIQUIER');
    }

    public function index(User $user)
    {
        return $user->hasRole('BOUTIQUIER');
    }

    public function updateStock(User $user)
    {
        return $user->hasRole('BOUTIQUIER');
    }

    public function updateMultipleStocks(User $user)
    {
        return $user->hasRole('BOUTIQUIER');
    }

    public function showById(User $user)
    {
        return $user->hasRole('BOUTIQUIER');
    }

    public function showByLibelle(User $user)
    {
        return $user->hasRole('BOUTIQUIER');
    }
}
