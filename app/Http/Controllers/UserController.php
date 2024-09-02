<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class UserController extends Controller
{
    /**
     * Crée un nouvel utilisateur.
     *
     * @param StoreUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreUserRequest $request)
    {
        DB::beginTransaction();

        try {
            // Récupérer les données de la requête validée
            $userData = $request->only('nom', 'prenom', 'login', 'password', 'role');

            // Créer un nouvel utilisateur
            $user = User::create([
                'nom' => $userData['nom'],
                'prenom' => $userData['prenom'],
                'login' => $userData['login'],
                'password' => bcrypt($userData['password']),
                'role' => $userData['role'],
            ]);

            DB::commit();

            return response()->json([
                'status' => 201,
                'data' => $user,
                'message' => 'Utilisateur créé avec succès'
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 500,
                'data' => null,
                'message' => 'Erreur lors de la création de l\'utilisateur : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Affiche la liste des utilisateurs.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Initialisation de la requête pour filtrer par rôle, état actif, ou les deux
        $query = User::query();

        // Filtrer par rôle, si le paramètre est fourni
        if ($request->has('role')) {
            $query->where('role', $request->input('role'));
        }

        // Filtrer par état actif, si le paramètre est fourni
        $active = $request->query('active');

        if ($active === 'oui') {
            // Utilisateurs avec un compte non supprimé (deletedAt est null)
            $query->whereNull('deletedAt');
        } elseif ($active === 'non') {
            // Utilisateurs avec un compte supprimé (deletedAt n'est pas null)
            $query->whereNotNull('deletedAt');
        } elseif ($active !== null) {
            // Gestion du cas où le paramètre "active" est invalide
            return response()->json([
                'status' => 400,
                'data' => null,
                'message' => 'Paramètre "active" invalide'
            ], 400);
        }

        // Récupération de la liste des utilisateurs en fonction des filtres appliqués
        $users = $query->get();

        return response()->json([
            'status' => 200,
            'data' => $users->isEmpty() ? null : $users,
            'message' => $users->isEmpty() ? 'Aucun utilisateur trouvé' : 'Liste des utilisateurs récupérée avec succès'
        ], 200);
    }
}
