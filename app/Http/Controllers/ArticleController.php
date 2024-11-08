<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Services\Interfaces\ArticleService;
use App\Http\Requests\UpdateMultipleStocksRequest;


class ArticleController extends Controller
{
    protected $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    public function index(Request $request)
    {
        $this->authorize('index', Article::class);
        return $this->articleService->index($request);
    }

    public function store(StoreArticleRequest $request)
    {
        $this->authorize('store', Article::class);
        return $this->articleService->create($request->validated());
    }

    public function updateStock(UpdateArticleRequest $request, $id)
    {
        $this->authorize('updateStock', Article::class);
        return $this->articleService->updateStock($request->validated(), $id);
    }

    public function updateMultipleStocks(UpdateMultipleStocksRequest $request)
    {
        $this->authorize('updateMultipleStocks', Article::class);
        return $this->articleService->updateMultipleStocks($request->validated());
    }

    public function showById($id)
    {
        $this->authorize('showById', Article::class);
        return $this->articleService->showById($id);
    }

    public function showByLibelle(Request $request)
    {
        // Validation des données de la requête
        $request->validate([
            'libelle' => 'required|string|max:255',
        ]);

        // Autoriser l'accès basé sur la politique
        $this->authorize('showByLibelle', Article::class);

        // Obtenir le libellé de la requête
        $libelle = $request->input('libelle');

        // Appeler le service pour récupérer les articles
        return  $result = $this->articleService->showByLibelle(['libelle' => $libelle]);
                
    }

}