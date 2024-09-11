<?php
namespace App\Services\Interfaces;

use Illuminate\Http\Request;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\StoreUserRequest;

interface AuthentificationServiceInterface
{
    public function login(AuthRequest $request);
    public function logout();
    // public function register(StoreUserRequest $request);
}
