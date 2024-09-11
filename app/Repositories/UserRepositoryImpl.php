<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepository;
class UserRepositoryImpl implements UserRepository
{
    public function all()
    {
        return User::all();
    }

    public function index()
    {
        return User::index();
    }
    public function store($request){
        return User::store($request);
    }

    public function whereNull()
    {
        return User::whereNull('name')->get();
    }

    public function whereNotNull()
    {
        return User::whereNotNull('name')->get();
    }

    public function has()
    {
        return User::has('posts')->get();
    }

    public function where()
    {
        return User::where('name')->get();
    }

    public function listUsers($filters = []){
        return User::listUsers($filters);
    }
    


}