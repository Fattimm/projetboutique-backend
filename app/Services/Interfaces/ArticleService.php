<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;

interface ArticleService
{
    public function index(Request $request);
    public function create(array $data);
    public function updateStock(array $data, $id);
    public function updateMultipleStocks(array $validatedData);
    public function showById($id);
    public function showByLibelle(array $data);

}