<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Client;

class AuthController extends Controller
{

    /* public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);
    
        $credentials = $request->only('login', 'password');
    
        // Utiliser le gardien 'api' pour Passport
        if (!Auth::guard('api')->attempt($credentials)) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }
    
        $user = User::find(Auth::guard('api')->user()->id);
        $token = $user->createToken('Personal Access Token')->plainTextToken;
    
        return response()->json(['status' => 'success', 'token' => $token]);
    } */
    
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('login', $request->input('login'))->first();
        // var_dump($user);

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }
        $user = User::find(Auth::guard('api')->user()->id);
        $token = $user->createToken('Personal Access Token')->plainTextToken;

        return response()->json(['status' => 'success', 'token' => $token]);
    }
    
    public function register(Request $request)
    {
        $request->validate([
            'login' => 'required|string|unique:users,login',
            'password' => [
                'required',
                'min:5',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&#]/',
            ],
            'clientid' => 'required|exists:clients,id',
            'photo' => 'nullable|image',
        ]);

        $client = Client::find($request->input('clientid'));

        if (!$client) {
            return response()->json(['status' => 'error', 'data' => ['clientid' => ['Client does not exist']]], 400);
        }

        $user = User::create([
            'login' => $request->input('login'),
            'password' => Hash::make($request->input('password')),
        ]);

        $client->user()->associate($user)->save();

        return response()->json([
            'status' => 'success',
            'data' => [
                'client' => $client,
                'user' => $user
            ]
        ]);
    }
}