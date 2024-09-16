<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\Request;
use App\Http\Requests\StoreDetteRequest;

interface DetteRepository 
{

    public function create(StoreDetteRequest $request);
    public function update($id, Request $request);
    public function delete($id);
    public function find($id);
    public function index($statut = null);
    public function show($id);
    public function listArticles($id);
    public function listPaiements($id);
    public function addPaiement(Request $request, $id);
    public function is_null(Request $request);
    public function is_numeric(Request $request);
    
}