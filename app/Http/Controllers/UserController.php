<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\UserServiceImpl;
use App\Http\Requests\StoreUserRequest;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserServiceImpl $userService)
    {
        $this->userService = $userService;
    }

    
    public function store(StoreUserRequest $request){
        return User::create($request->all());
    }

    

    public function index(Request $request)
    {
        $filters = $request->only(['role', 'active']);
        $response = $this->userService->listUsers($filters);
        return response()->json($response, $response['status']);
    }

}
