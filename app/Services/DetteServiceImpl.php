<?php

namespace App\Services;

use Exception;
use App\Models\Dette;
use App\Models\Client;
use App\Models\Article;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreDetteRequest;
use App\Services\Interfaces\DetteService;

class DetteServiceImpl implements DetteService
{
    public function create(StoreDetteRequest $request)
    {
        
        try {
            DB::beginTransaction();
            //Validation des données
            $client = Client::findOrFail($request->clientId);
            if ($request->montant <= 0) {
                throw new Exception("Le montant de la dette doit être positif");
            }

            //Créer la dette
            $dette = Dette::create([
                'client_id' => $request->clientId,
                'montant' => $request->montant,
            ]);

            // Ajouter les articles liés à la dette
            foreach ($request->articles as $articleData) {
                $article = Article::findOrFail($articleData['articleId']);
                if ($article->qteStock < $articleData['qteVente']) {
                    throw new Exception("Quantité insuffisante pour l'article {$article->libelle}");
                }

                //Mettre à jour la quantité en stock
                $article->qteStock -= $articleData['qteVente'];
                $article->save();

                // Associer l'article à la dette
                $dette->articles()->attach($article->id, [
                    'qteVente' => $articleData['qteVente'],
                    'prixVente' => $articleData['prixVente'],
                ]);
            }

            // Gestion du paiement facultatif
            if ($request->filled('paiement.montant')) {
                if ($request->paiement['montant'] > $request->montant) {
                    throw new Exception("Le montant du paiement ne peut pas être supérieur au montant de la dette");
                }

                $dette->paiements()->create([
                    'montant' => $request->paiement['montant'],
                    'date' => now(),
                ]);
            }

            DB::commit();

            return [
                'status' => 201,
                'data' => $dette->load('client', 'articles', 'paiements'),
                'message' => 'Dette enregistrée avec succès',
            ];
        } catch (Exception $e) {
            DB::rollBack();

            return [
                'status' => 500,
                'data' => null,
                'message' => 'Erreur lors de l\'enregistrement de la dette: ' . $e->getMessage(),
            ];
        }
    }


    public function index($statut = null)
    {
        try {
            $query = Dette::query()->with('client');

            if ($statut === 'Solde') {
                $query->whereRaw('montant - COALESCE((SELECT SUM(montant) FROM paiements WHERE paiements.dette_id = dettes.id), 0) = 0');
            } elseif ($statut === 'NonSolde') {
                $query->whereRaw('montant - COALESCE((SELECT SUM(montant) FROM paiements WHERE paiements.dette_id = dettes.id), 0) > 0');
            }

            $dettes = $query->get();

            if ($dettes->isEmpty()) {
                return [
                    'status' => 404,
                    'data' => null,
                    'message' => 'Aucune dette trouvée',
                ];
            }

            return [
                'status' => 200,
                'data' => $dettes,
                'message' => 'Liste des dettes',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'data' => null,
                'message' => 'Erreur lors de la récupération des dettes: ' . $e->getMessage(),
            ];
        }
    }

    public function show($id)
    {
        try {
            $dette = Dette::with('client')->find($id);

            if (!$dette) {
                return [
                    'status' => 404,
                    'data' => null,
                    'message' => 'Dette non trouvée',
                ];
            }

            return [
                'status' => 200,
                'data' => $dette,
                'message' => 'Dette trouvée',
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'data' => null,
                'message' => 'Erreur lors de la récupération de la dette: ' . $e->getMessage(),
            ];
        }
    }

    public function listArticles($id)
    {
        try {
            $dette = Dette::with('articles')->find($id);

            if (!$dette) {
                return [
                    'status' => 404,
                    'data' => null,
                    'message' => 'Dette non trouvée',
                ];
            }

            return [
                'status' => 200,
                'data' => $dette->articles,
                'message' => 'Articles trouvés',
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'data' => null,
                'message' => 'Erreur lors de la récupération des articles: ' . $e->getMessage(),
            ];
        }
    }

    public function listPaiements($id)
    {
        try {
            $dette = Dette::with('paiements')->find($id);

            if (!$dette) {
                return [
                    'status' => 404,
                    'data' => null,
                    'message' => 'Dette non trouvée',
                ];
            }

            return [
                'status' => 200,
                'data' => $dette->paiements,
                'message' => 'Paiements trouvés',
            ];
        } catch (Exception $e) {
            return [
                'status' => 500,
                'data' => null,
                'message' => 'Erreur lors de la récupération des paiements: ' . $e->getMessage(),
            ];
        }
    }

    public function addPaiement(Request $request, $id)
    {
        $dette = Dette::find($id);
        if (!$dette) {
            return [
                'status' => 404,
                'data' => null,
                'message' => 'Dette non trouvée',
            ];
        }

        // Calculer le montant total payé et le montant restant
        $montantTotalPaye = $dette->paiements->sum('montant');
        $montantRestant = $dette->montant - $montantTotalPaye;

        // Vérifier que le montant du paiement est valide
        $montantPaiement = $request->input('paiement.montant');

        if (is_null($montantPaiement) || !is_numeric($montantPaiement)) {
            return [
                'status' => 400,
                'data' => null,
                'message' => 'Le montant du paiement est invalide',
            ];
        }

        if ($montantPaiement > $montantRestant) {
            return [
                'status' => 400,
                'data' => null,
                'message' => 'Le montant du paiement ne peut pas dépasser le montant restant',
            ];
        }

        // Ajouter le paiement
        try {
            $paiement = Paiement::create([
                'dette_id' => $dette->id,
                'montant' => $montantPaiement,
                'date' => now(),
            ]);

            // Inclure les paiements mis à jour avec la dette
            $dette->load('paiements');

            return [
                'status' => 200,
                'data' => [
                    'paiements' => $dette->paiements,
                ],
                'message' => 'Paiement ajouté avec succès',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'data' => null,
                'message' => 'Erreur lors de l\'ajout du paiement: ' . $e->getMessage(),
            ];
        }
    }
    
}
