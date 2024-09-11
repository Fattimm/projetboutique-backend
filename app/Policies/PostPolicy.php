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
