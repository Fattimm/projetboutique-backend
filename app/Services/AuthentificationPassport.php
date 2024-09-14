<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Requests\AuthRequest;
use Illuminate\Support\Facades\DB;
use App\Services\CloudinaryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\ClientRepository;
use App\Http\Requests\StoreUserRequest;
use App\Http\Middleware\JsonResponseMiddleware;
use App\Services\Interfaces\AuthentificationServiceInterface;

class AuthentificationPassport implements AuthentificationServiceInterface
{
    protected $cloudinary;
    protected $authentificateService;


    public function __construct(){
        $this->cloudinary = new cloudinaryService();
    }

    public function login(AuthRequest $request)
    {
        $credentials = $request->only('login', 'password');

        if (Auth::attempt(['login' => $credentials['login'], 'password' => $credentials['password']])) {
            $user = Auth::user();

            // Création du jeton d'accès personnel
            $tokenResult = $user->createToken('AuthToken');

            return [
                'status' => 200,
                'data' => [
                    'accessToken' => $tokenResult,
                ],
                'message' => 'Login réussi'
            ];
        }

        return [
            'status' => 401,
            'message' => 'Non autorisé'
        ];
    }

    public function logout()
    {
        $user = Auth::user();

        if ($user) {
            // Révoquer tous les tokens de l'utilisateur authentifié
            $user->tokens->delete();
        }

        return [
            'status' => 200,
            'message' => 'Déconnexion réussie'
        ];
    }

    public function register(StoreUserRequest $request)
    {
        DB::beginTransaction();  // Démarrer la transaction

        try {
            // Validation des données de la requête
            $validatedData = $request->validated();

            // Vérifier si le client existe
            $client = Client::find($request->input('clientid')) ;

            if (!$client) {
                throw new Exception('Le client n\'existe pas');
            }

            // Gestion de l'upload de l'image (hors transaction car externe à la DB)
            $photoUrl = null;
            if ($request->hasFile('photo')) {
                $userPhoto = $request->file('photo');

                // Enregistrer l'image dans Cloudinary
                $uploadResult = $this->cloudinary->getCloudinary()->uploadApi()->upload($userPhoto->getRealPath(), [
                    'folder' => 'photos'
                ]);
               // Enregistrement de la photo dans le stockage local (storage/app/public/photos)
               $photoLocalPath = $userPhoto->storeAs('public/photos', $userPhoto->getClientOriginalName());


                // Obtenir l'URL sécurisée de l'image
                $photoUrl = $uploadResult['secure_url'];
            }

            // Créer l'utilisateur (opération transactionnelle)
            $user = User::create([
                'login' => $validatedData['login'],
                'nom' => $validatedData['nom'],
                'prenom' => $validatedData['prenom'],
                'email' => $validatedData['email'],
                'password' => bcrypt($validatedData['password']),
                'role' => $validatedData['role'],
                'photo' => $photoUrl,  // URL de la photo si disponible
            ]);

            // Associer l'utilisateur au client (opération transactionnelle)
            $client->user()->associate($user);
            $client->save();

            // Confirmer la transaction
            DB::commit();

            // Répondre avec succès
            return response()->json([
                'status' => 'success',
                'data' => [
                    'client' => $client,
                    'user' => $user,
                    'image_path' => $photoUrl,
                ]
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();  // Annuler la transaction en cas d'erreur

            // Répondre avec une erreur
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de la création de l\'utilisateur : ' . $e->getMessage()
            ], 500);
        }
    }


}
