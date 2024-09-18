<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Services\Interfaces\ArticleService;
use App\Repositories\Interfaces\ArticleRepository;

class ArticleServiceImpl implements ArticleService
{
    protected $repo;

    public function __construct(ArticleRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $disponible = $request->query('disponible');

        if ($disponible === 'oui') {
            $articles = $this->repo->getAvailableArticles();
        } elseif ($disponible === 'non') {
            $articles = $this->repo->getUnavailableArticles();
        } else {
            $articles = $this->repo->index($request);
        }

        return [
            'data' => $articles,
            'message' => $articles->isEmpty() ? 'Pas d\'articles disponibles' : 'Liste des articles',
            'status_code' => $articles->isEmpty() ? 404 : 200
        ];
    }

    
    public function create(array $data)
    {
        return $this->repo->create($data);
    }


    public function updateStock(array $data, $id)
    {
        // Trouver l'article par ID
        $article = Article::find($id);

        if (!$article) {
            return [
                'data' => null,
                'message' => 'Article non trouvé',
                'status_code' => 404
            ];
        }

        // Mettre à jour le stock
        $article->qteStock += $data['qteStock'];
        $article->save();

        return [
            'data' => $article,
            'message' => 'Quantité de stock mise à jour avec succès',
            'status_code' => 200
        ];
    }


    
    public function updateMultipleStocks(array $validatedData)
    {
        $success = [];
        $error = [];

        foreach ($validatedData['articles'] as $articleData) {
            $article = $this->repo->find($articleData['id']);

            if ($article) {
                $article->qteStock += $articleData['qteStock'];
                $article->save();
                $success[] = $article;
            } else {
                $error[] = $articleData['id'];
            }
        }

        return [
            'data' => [
                'success' => $success,
                'error' => $error
            ],
            'message' => 'Mise à jour des stocks terminée',
            'status_code' => empty($error) ? 200 : 207 // Status "Multi-status" pour indiquer des erreurs partielles
        ];
    }

    public function showById($id)
    {
        $article = $this->repo->find($id);

        return [
            'data' => $article,
            'message' => $article ? 'Article trouvé' : 'Article non trouvé',
            'status_code' => $article ? 200 : 404 // Status 200 pour succès, 404 pour non trouvé
        ];
    }

    public function showByLibelle(array $data)
    {
        // Assurez-vous que le tableau contient 'libelle'
        $libelle = $data['libelle'] ?? '';

        // Rechercher les articles par libellé
        $articles = $this->repo->findByLibelle($libelle);

        return [
            'data' => $articles,
            'message' => $articles->isNotEmpty() ? 'Articles trouvés' : 'Aucun article trouvé',
            'status_code' => $articles->isNotEmpty() ? 200 : 404
        ];
    }



}
