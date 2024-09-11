<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;
use App\Http\Requests\StoreDetteRequest;

interface DetteService
{
    public function create(StoreDetteRequest $request);
    public function index($statut = null);
    public function show($id);
    public function listArticles($id);
    public function listPaiements($id);
    public function addPaiement(Request $request, $id);
    
}
