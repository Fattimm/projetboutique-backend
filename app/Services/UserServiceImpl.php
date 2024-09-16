<?php

namespace App\Services;

use \Exception;
use App\Models\User;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use App\Services\QrCodeService;
use Illuminate\Support\Facades\DB;
use App\Services\LoyaltyCardService;
use App\Http\Requests\StoreUserRequest;
use App\Services\Interfaces\UserService;

class UserServiceImpl implements UserService
{
    protected $cloudinaryService;
    protected $clientServiceImpl;

    public function __construct() {

        $this->cloudinaryService = new cloudinaryService();

    }

    public function store(StoreUserRequest $request)
    {

        DB::beginTransaction();

        try {
            // Récupérer les données de la requête validée
            $userData = $request->only('nom', 'prenom', 'login', 'password','email', 'role','photo');

            // Valider le rôle
            $allowedRoles = ['ADMIN', 'BOUTIQUIER'];
            if (!in_array($userData['role'], $allowedRoles)) {
                throw new \Exception('Rôle non autorisé');
            }
            // Gestion de la photo
            $photoUrl = null;
            if ($request->hasFile('user.photo')) {
                $userPhoto = $request->file('user.photo');

               // Utilisation de l'instance Cloudinary
               $uploadResult = $this->cloudinaryService->getCloudinary()->uploadApi()->upload($userPhoto->getRealPath(), [
                'folder' => 'users/photos'
            ]);

                // Enregistrement de la photo dans le stockage local (storage/app/public/photos)
               $photoLocalPath = $userPhoto->storeAs('public/photos', $userPhoto->getClientOriginalName());


                // Obtenir l'URL de l'image
                $userPhotoUrl = $uploadResult['secure_url'];
            }

            // Créer un nouvel utilisateur
            $user = User::create([
                'nom' => $userData['nom'],
                'prenom' => $userData['prenom'],
                'login' => $userData['login'],
                'email' => $userData['email'],
                'password' => bcrypt($userData['password']),
                'role' => $userData['role'],
                'photo' => $userPhotoUrl,
            ]);

            DB::commit();

            return [
                'status' => 201,
                'data' => $user,
                'message' => 'Utilisateur créé avec succès'
            ];

        } catch (Exception $e) {
            DB::rollBack();

            return [
                'status' => 500,
                'data' => null,
                'message' => 'Erreur lors de la création de l\'utilisateur : ' . $e->getMessage()
            ];
        }
    }
   
    public function listUsers($filters = [])
    {
        $query = User::query();

        if (isset($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        if (isset($filters['active'])) {
            if ($filters['active'] === 'oui') {
                $query->whereNull('deleted_at');
            } elseif ($filters['active'] === 'non') {
                $query->whereNotNull('deleted_at');
            }
        }

        $users = $query->get();

        return [
            'status' => 200,
            'data' => $users->isEmpty() ? null : $users,
            'message' => $users->isEmpty() ? 'Aucun utilisateur trouvé' : 'Liste des utilisateurs récupérée avec succès'
        ];
    }

}
