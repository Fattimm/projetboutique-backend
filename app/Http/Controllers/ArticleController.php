<?php

namespace App\Http\Controllers;

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
        return $this->articleService->index($request);
    }

    public function store(StoreArticleRequest $request)
    {
        return $this->articleService->create($request->validated());
    }

    public function updateStock(UpdateArticleRequest $request, $id)
    {
        return $this->articleService->updateStock($request->validated(), $id);
    }

    public function updateMultipleStocks(UpdateMultipleStocksRequest $request)
    {
        return $this->articleService->updateMultipleStocks($request->validated());
    }

    public function showById($id)
    {
        return $this->articleService->showById($id);
    }

    public function showByLibelle(UpdateArticleRequest $request)
    {
        return $this->articleService->showByLibelle($request->validated());
    }

}