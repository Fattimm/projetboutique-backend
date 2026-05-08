<?php

namespace App\Policies;

use App\Models\User;
use OpenApi\Attributes\Post;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    
    public function viewAny(User $user)
    {
        // Logic to determine if the user can view any posts
        return true; // or false
    }

    
    public function view(User $user, Post $post)
    {
        // Logic to determine if the user can view the post
        return $user->id === $post->user_id;
    }

    public function create(User $user)
    {
        // Logic to determine if the user can create posts
        return true; // or false
    }

   
    public function update(User $user, Post $post)
    {
        // Logic to determine if the user can update the post
        return $user->id === $post->user_id;
    }

   
    public function delete(User $user, Post $post)
    {
        // Logic to determine if the user can delete the post
        return $user->id === $post->user_id;
    }
}
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

    public function archiver(User $user)
    {
        return $user->role === 'ADMIN';
    }

    public function afficherDettesArchivees(User $user)
    {
        return $user->role === 'ADMIN';
    }

    public function restaurer(User $user)
    {
        return $user->role === 'ADMIN';
    }

    public function restaurerParClient(User $user)
    {
        return $user->role === 'ADMIN';
    }

    public function restaurerParDate(User $user)
    {
        return $user->role === 'ADMIN';
    }


}
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
