<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Facades\Validator;
class ArticleController extends Controller
{
    //CRÉER UN ARTICLE 
    public function store(Request $request)
    {
        // Définir les règles de validation
        $rules = [
            'libelle' => 'required|string|unique:articles,libelle',
            'prix' => 'required|numeric',
            'qteStock' => 'required|integer',
        ];

        // Valider la requête
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 411,
                'message' => 'Erreur de validation',
                'data' => $validator->errors()
            ], 411);
        }

        // Enregistrer l'article dans la base de données
        $article = Article::create([
            'libelle' => $request->input('libelle'),
            'prix' => $request->input('prix'),
            'qteStock' => $request->input('qteStock'),
        ]);

        // Répondre avec succès
        return response()->json([
            'status' => 201,
            'message' => 'Article enregistré avec succès',
            'data' => $article
        ], 201);
    }


    //AFFICHER DES ARTICLES/EN FONCTION DE LEURS DISPONIBILITÉS
    public function index(Request $request)
    {
        // Vérifie si un filtre de disponibilité est demandé
        $disponible = $request->query('disponible');

        // Si 'disponible' est défini, filtre les articles en fonction de la disponibilité
        if ($disponible === 'oui') {
            $articles = Article::where('qteStock', '>', 0)->get();
        } elseif ($disponible === 'non') {
            $articles = Article::where('qteStock', '=', 0)->get();
        } else {
            // Sinon, récupère tous les articles
            $articles = Article::all();
        }

        // Prépare la réponse
        if ($articles->isEmpty()) {
            return response()->json([
                'status' => 200,
                'data' => null,
                'message' => 'Pas d\'articles'
            ]);
        }

        return response()->json([
            'status' => 200,
            'data' => $articles,
            'message' => 'Liste des articles'
        ]);
    }


    //METTRE A JOUR LA QUANTITE DE STOCK D'UN ARTICLE
    public function updateStock(Request $request, $id)
    {
        // Validation de la requête
        $request->validate([
            'qteStock' => 'required|numeric|min:0'
        ]);

        // Rechercher l'article par ID
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouvé'
            ]);
        }

        // Mise à jour de la quantité de stock
        $article->qteStock += $request->qteStock;
        $article->save();

        return response()->json([
            'status' => 200,
            'data' => $article,
            'message' => 'Quantité de stock mise à jour'
        ]);
    }


    //METTRE A JOUR LA QUANTITE DE STOCK DE PLUSIEURS ARTICLES
    public function updateMultipleStocks(Request $request)
    {
        // Validation de la requête
        $request->validate([
            'articles' => 'required|array|min:1',
            'articles.*.id' => 'required|exists:articles,id',
            'articles.*.qteStock' => 'required|numeric|min:0'
        ]);

        $success = [];
        $error = [];

        foreach ($request->articles as $articleData) {
            $article = Article::find($articleData['id']);
            
            if ($article) {
                $article->qteStock += $articleData['qteStock'];
                $article->save();
                $success[] = $article;
            } else {
                $error[] = $articleData['id'];
            }
        }

        return response()->json([
            'status' => 200,
            'data' => [
                'success' => $success,
                'error' => $error
            ],
            'message' => 'Mise à jour des stocks terminée'
        ]);
    }


    //RÉCUPÉRER LES DETAILS D'UN ARTICLE PAR SON ID
    public function showById($id)
    {
        // Rechercher l'article par ID
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouvé'
            ]);
        }

        return response()->json([
            'status' => 200,
            'data' => $article,
            'message' => 'Article trouvé'
        ]);
    }


    //RÉCUPÉRER LES DETAILS D'UN ARTICLE PAR SON LIBELLE
    public function showByLibelle(Request $request)
    {
        // Validation de la requête
        $request->validate([
            'libelle' => 'required|string'
        ]);

        // Rechercher l'article par libellé
        $article = Article::where('libelle', $request->libelle)->first();

        if (!$article) {
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouvé'
            ]);
        }

        return response()->json([
            'status' => 200,
            'data' => $article,
            'message' => 'Article trouvé'
        ]);
    }

}
