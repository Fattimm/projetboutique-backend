<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Resources\ClientCollection;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Models\User;
use App\Traits\RestResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \Exception;
use Spatie\QueryBuilder\QueryBuilder;
use App\Models\Role;

class ClientController extends Controller
{
    use RestResponseTrait;

    public function store(StoreClientRequest $request)
    {
        DB::beginTransaction();

        try {
            $clientData = $request->only('surname', 'address', 'telephone');
            $client = Client::create($clientData);

            if ($request->has('user')) {
                $roleName = $request->input('user.role');
                $validRoles = ['CLIENT', 'BOUTIQUIER', 'ADMIN'];

                if (!in_array($roleName, $validRoles)) {
                    throw new Exception('Rôle non valide');
                }

                $userData = $request->input('user');
                $user = new User();
                $user->nom = $userData['nom'];
                $user->prenom = $userData['prenom'];
                $user->login = $userData['login'];
                $user->password = bcrypt($userData['password']);
                $user->role = $roleName;
                $user->save();
            }

            DB::commit();

            return response()->json([
                'status' => 201,
                'data' => new ClientResource($client),
                'message' => 'Client enregistré avec succès'
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 500,
                'data' => null,
                'message' => 'Erreur lors de l\'enregistrement du client: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Liste tous les clients.
     */
    public function index()
    {
        $clients = Client::all();

        return response()->json([
            'status' => 200,
            'data' => $clients,
            'message' => 'Liste des clients'
        ]);
    }

    /**
     * Filtrer les clients par comptes.
     */
        public function filterByAccount(Request $request)
        {
            $comptes = $request->query('comptes');
            dd($comptes); // Vérifiez ce qui est reçu dans $comptes



            if ($comptes === 'oui') {
                // Clients ayant un user_id non nul (ont un compte utilisateur)
                $clients = Client::whereNotNull('user_id')->get();
            } elseif ($comptes === 'non') {
                // Clients n'ayant pas de user_id (n'ont pas de compte utilisateur)
                $clients = Client::whereNull('user_id')->get();
            } else {
                return response()->json([
                    'status' => 400,
                    'data' => null,
                    'message' => 'Paramètre "comptes" invalide. Utilisez "oui" ou "non".'
                ], 400);
            }

            return response()->json([
                'status' => 200,
                'data' => $clients,
                'message' => 'Liste des clients'
            ]);
        }
    /**
     * Filtrer les clients par statut actif.
     */
    public function filterByStatus(Request $request)
    {
        $active = $request->query('active');

        if ($active === 'oui') {
            // Clients avec un compte utilisateur non supprimé (deletedAt est null)
            $clients = Client::whereHas('user', function ($query) {
                $query->whereNull('deletedAt');
            })->get();
        } elseif ($active === 'non') {
            // Clients avec un compte utilisateur supprimé (deletedAt n'est pas null)
            $clients = Client::whereHas('user', function ($query) {
                $query->whereNotNull('deletedAt');
            })->get();
        } else {
            return response()->json([
                'status' => 400,
                'data' => null,
                'message' => 'Paramètre "active" invalide'
            ], 400);
        }

        return response()->json([
            'status' => 200,
            'data' => $clients,
            'message' => 'Liste des clients'
        ]);
    }
    /**
     * Rechercher un client par téléphone.
     */
    public function searchByTelephone(Request $request)
    {
        $telephone = $request->input('telephone');
        $client = Client::where('telephone', $telephone)->first();
        if ($client) {
            return response()->json([
                'status' => 200,
                'data' => $client,
                'message' => 'Client trouvé'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'data' => null,
                'message' => 'Client non trouvé'
            ], 404);
        }
    }

    public function deleteAccount($userId)
    {
        $user = User::find($userId);
        if ($user) {
            $user->deletedAt = now();
            $user->save();

            return response()->json([
                'status' => 200,
                'message' => 'Compte utilisateur marqué comme supprimé.'
            ]);
        }

        return response()->json([
            'status' => 404,
            'message' => 'Utilisateur non trouvé.'
        ], 404);
    }

    //LISTER LES INFOS D'UN CLIENT

    public function show($id)
    {
        $client = Client::find($id);
    
        if ($client) {
            return response()->json([
                'status' => 200,
                'data' => $client,
                'message' => 'Client trouvé'
            ], 200);
        } else {
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouvé'
            ], 411);
        }
    }

    // LISTER LES INFOS D'UN CLIENT AVEC L'UTILISATEUR 
    public function getClientWithUser($id)
    {
        $client = Client::with('user')->find($id);

        if ($client) {
            return response()->json([
                'status' => 200,
                'data' => $client,
                'message' => 'Client trouvé'
            ], 200);
        } else {
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouvé'
            ], 411);
        }
    }

    // LISTER LES DETTES D'UN CLIENT
    public function getClientDettes($id)
    {
        $client = Client::with('dettes')->find($id);

        if ($client) {
            $dettes = $client->dettes;
            return response()->json([
                'status' => 200,
                'data' => $dettes->isNotEmpty() ? $dettes : null,
                'message' => 'Client trouvé'
            ], 200);
        } else {
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouvé'
            ], 411);
        }
    }


    







}
