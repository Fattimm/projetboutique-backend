<?php
namespace App\Services\Interfaces;

use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRequest;
use App\Models\Role;

interface UserService 
{
    public function store(StoreUserRequest $request);
    public function listUsers($filters = []);

}