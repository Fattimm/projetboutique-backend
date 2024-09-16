<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Article;

class ArticlePolicy
{
    // Autorisation pour créer un article (BOUTIQUIER uniquement)
    public function store(User $user)
    {
        return $user->role === 'BOUTIQUIER';
    }

    // Autorisation pour lister les articles (BOUTIQUIER uniquement)
    public function index(User $user)
    {
        return $user->role === 'BOUTIQUIER';
    }

    // Autorisation pour mettre à jour un article (BOUTIQUIER uniquement)
    public function updateStock(User $user)
    {
        return $user->role === 'BOUTIQUIER';
    }

    public function updateMultipleStocks(User $user)
    {
        return $user->role === 'BOUTIQUIER';
    }
    public function showById(User $user)
    {
        return $user->role === 'BOUTIQUIER';
    }
    public function showByLibelle(User $user)
    {
        return $user->role === 'BOUTIQUIER';
    }
}
