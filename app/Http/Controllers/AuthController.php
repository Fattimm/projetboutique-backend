<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Client;
use App\Http\Requests\StoreUserRequest;
use App\Rules\TelephoneRule;
use App\Rules\CustumPasswordRule;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('login', $request->input('login'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->accessToken;

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function register(StoreUserRequest $request)
    {
        $client = Client::find($request->input('clientid'));

        if (!$client) {
            return response()->json(['status' => 'error', 'data' => ['clientid' => ['Client does not exist']]], 400);
        }

        $user = User::create([
            'login' => $request->input('login'),
            'password' => Hash::make($request->input('password')),
            'role' => $request->input('role'),
        ]);

        $client->user()->associate($user)->save();

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->accessToken;

        return response()->json([
            'status' => 'success',
            'data' => [
                'client' => $client,
                'user' => $user,
                'token' => $token
            ]
        ]);
    }
}