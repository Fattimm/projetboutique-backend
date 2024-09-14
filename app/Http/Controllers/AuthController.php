<?php

namespace App\Http\Controllers;

use App\Facades\UploadFacade;
use Illuminate\Http\Request;
use App\Services\AuthentificationPassport;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Client;
use App\Services\Upload;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\AuthRequest;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Cloudinary;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

     public function register(StoreUserRequest $request) {
         return $this->authService->register($request);   
     }


}