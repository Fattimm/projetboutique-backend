<?php
namespace App\Services;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\Interfaces\AuthentificationServiceInterface;

class AuthentificationSanctum implements AuthentificationServiceInterface
{
    public function login(Request $request)
    {

    }

    public function logout()
    {

    }
   
}
