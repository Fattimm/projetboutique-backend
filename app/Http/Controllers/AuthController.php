<?php

namespace App\Http\Controllers;

use App\Services\AuthentificationPassport;
use App\Models\Client;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\AuthRequest;


class AuthController extends Controller
{
    protected $authService;
    
    public function __construct(AuthentificationPassport $authService)
    {
        $this->authService = $authService;
    }

    public function login(AuthRequest $request)
    {
        $credentials = $request->only('login', 'password');
        return $this->authService->login($request);
    }

    public function logout() {
        return $this->authService->logout();
    }

     public function register(StoreUserRequest $request)
     {
        $this->authorize('register', Client::class);
        return $this->authService->register($request);   
     }


}