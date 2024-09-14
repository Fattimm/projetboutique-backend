<?php

namespace App\Services;

use \Exception;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Events\ClientCreated;
use App\Services\LoyaltyCardMail;
use Illuminate\Support\Facades\DB;
use App\Services\CloudinaryService;
use Endroid\QrCode\Builder\Builder;
use App\Services\LoyaltyCardService;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Facades\ClientRepositoryFacade;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreClientRequest;
use App\Services\Interfaces\ClientService;

class ClientServiceImpl implements ClientService
{
    protected $cloudinary;
    protected $loyaltyCardService;
    protected $qrCodeService;
    protected $loyaltyCardEmailService;



    public function __construct()
    {
        $this->cloudinary = new cloudinaryService();

        $this->loyaltyCardService = new loyaltyCardService;
        $this->qrCodeService = new QrCodeService();
        $this->loyaltyCardEmailService = new LoyaltyCardEmailService;
    }


    public function store(StoreClientRequest $request)
    {
        DB::beginTransaction();

        try {
            $clientData = $request->only('surname', 'adresse', 'telephone');

            // Assurez-vous que tous les champs requis sont présents
            if (!isset($clientData['surname']) || !isset($clientData['adresse']) || !isset($clientData['telephone'])) {
                throw new \Exception("Tous les champs requis pour le client doivent être remplis.");
            }

            $client = ClientRepositoryFacade::store($clientData);

            $user = null;

            if ($request->has('user')) {
                $userData = $request->input('user');


                $user = User::create([
                    'nom' => $userData['nom'],
                    'prenom' => $userData['prenom'],
                    'login' => $userData['login'],
                    'email' => $userData['email'],
                    'password' => bcrypt($userData['password']),
                    'role' => $userData['role'] ?? 'CLIENT',
                ]);


                $client->user()->associate($user);
                $client->save();

                // store the photo in storage_path("app/public/qr_codes/"
                $photo = $request->file('user.photo');
                $storedPhoto = $photo->storeAs('public/photos', $client->surname . '.png');
                $photoPath = storage_path('app/' . $storedPhoto);
                $user->photo = $storedPhoto;
            }

            /*    // Générer le QR code
         $qrCodePath = $this->qrCodeService->generateQrCode($client, $user);

         // Créer la carte de fidélité
         $loyaltyCardPath = $this->loyaltyCardService->createLoyaltyCard($client, $qrCodePath, $user, $photoLocalPath);
 
         // Envoyer l'e-mail avec la carte de fidélité
         $this->loyaltyCardEmailService->sendLoyaltyCardEmail($client, $user, $loyaltyCardPath);
  */
            event(new ClientCreated($client, $user, $photoPath));


            DB::commit();

            return [
                'status' => 201,
                'data' => $client,
                'message' => 'Client créé avec succès'
            ];
        } catch (Exception $e) {
            DB::rollBack();

            return [
                'status' => 500,
                'data' => null,
                'message' => 'Erreur lors de la création du client : ' . $e->getMessage()
            ];
        }
    }

    public function index()
    {
        return ClientRepositoryFacade::all();
        return [
            'status' => 200,
            'data' => $clients,
            'message' => 'Liste des clients'
        ];
    }

    public function filterByAccount(Request $request)
    {
        $comptes = $request->query('comptes');

        if ($comptes === 'oui') {
            // Clients ayant un user_id non nul (ont un compte utilisateur)
            $clients = Client::whereNotNull('user_id')->get();
        } elseif ($comptes === 'non') {
            // Clients n'ayant pas de user_id (n'ont pas de compte utilisateur)
            $clients = Client::whereNull('user_id')->get();
        } else {
            return [
                'status' => 400,
                'data' => null,
                'message' => 'Paramètre "comptes" invalide. Utilisez "oui" ou "non".'
            ];
        }

        return [
            'status' => 200,
            'data' => $clients,
            'message' => 'Liste des clients'
        ];
    }
    public function filterByStatus(Request $request)
    {
        $active = $request->query('active');

        if ($active === 'oui') {
            // Clients avec un compte utilisateur non supprimé (deletedAt est null)
            $clients = Client::whereHas('user', function ($query) {
                $query->whereNull('deleted_at');
            })->get();
        } elseif ($active === 'non') {
            // Clients avec un compte utilisateur supprimé (deletedAt n'est pas null)
            $clients = Client::whereHas('user', function ($query) {
                $query->whereNotNull('deleted_at');
            })->get();
        } else {
            return [
                'status' => 400,
                'data' => null,
                'message' => 'Paramètre "active" invalide'
            ];
        }

        return [
            'status' => 200,
            'data' => $clients,
            'message' => 'Liste des clients'
        ];
    }

    public function searchByTelephone(Request $request)
    {
        $telephone = $request->input('telephone');

        // Chercher le client par son numéro de téléphone
        try {
            $client = Client::where('telephone', 'like', '%' . $telephone . '%')->first();

            if ($client) {
                // Vérifier si le client a une photo
                $photoBase64 = null;
                if ($client->photo) {
                    // Récupérer l'URL de la photo du client depuis Cloudinary
                    $photoUrl = $client->photo;

                    try {
                        // Télécharger l'image depuis Cloudinary avec le client HTTP de Laravel
                        $response = Http::get($photoUrl);

                        if ($response->ok()) {
                            $imageContents = $response->body();

                            // Convertir l'image en base64
                            $photoBase64 = base64_encode($imageContents);
                        } else {
                            // En cas d'échec du téléchargement, laisser l'image à null
                            $photoBase64 = null;
                        }
                    } catch (\Exception $e) {
                        // En cas d'erreur lors du téléchargement de l'image
                        $photoBase64 = null;
                    }
                }

                // Retourner les données du client, y compris la photo en base64 si disponible
                return [
                    'status' => 200,
                    'data' => [
                        'client' => $client,
                        'photo_base64' => $photoBase64,
                    ],
                    'message' => 'Client trouvé'
                ];
            } else {
                // Retourner une erreur si le client n'est pas trouvé
                return [
                    'status' => 404,
                    'data' => null,
                    'message' => 'Client non trouvé'
                ];
            }
        } catch (\Exception $e) {
            // En cas d'erreur lors de la recherche du client
            return [
                'status' => 500,
                'data' => null,
                'message' => 'Erreur lors de la recherche du client : ' . $e->getMessage()
            ];
        }
    }

    public function deleteAccount($userId)
    {
        $user = User::find($userId);
        if ($user) {
            $user->deletedAt = now();
            $user->save();

            return [
                'status' => 200,
                'message' => 'Compte utilisateur marqué comme supprimé.'
            ];
        }

        return [
            'status' => 404,
            'message' => 'Utilisateur non trouvé.'
        ];
    }


    public function show($id)
    {
        $client = ClientRepositoryFacade::find($id);

        if ($client) {
            return [
                'status' => 200,
                'data' => $client,
                'message' => 'Client trouvé'
            ];
        } else {
            return [
                'status' => 404,
                'data' => null,
                'message' => 'Client non trouvé'
            ];
        }
    }

    public function getClientWithUser($id)
    {
        $client = ClientRepositoryFacade::with('user')->find($id);

        if ($client) {
            return [
                'status' => 200,
                'data' => $client,
                'message' => 'Client trouvé'
            ];
        } else {
            return [
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouvé'
            ];
        }
    }

    public function getClientDettes($id)
    {
        $client = ClientRepositoryFacade::with('dettes')->find($id);

        if ($client) {
            $dettes = $client->dettes;
            return [
                'status' => 200,
                'data' => $dettes->isNotEmpty() ? $dettes : null,
                'message' => 'Client trouvé'
            ];
        } else {
            return [
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouvé'
            ];
        }
    }
}
