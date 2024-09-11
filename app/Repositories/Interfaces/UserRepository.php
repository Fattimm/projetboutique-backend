<?php

namespace App\Repositories\Interfaces;

use App\Http\Requests\StoreUserRequest;

interface UserRepository
{
    public function all();
    public function index();
    public function store(StoreUserRequest $request);
    public function whereNull();
    public function whereNotNull();
    public function has();
    public function where();
    
    
}
