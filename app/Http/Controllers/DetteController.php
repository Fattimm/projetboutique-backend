<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Interfaces\DetteService;
use App\Http\Requests\StoreDetteRequest;

class DetteController extends Controller
{
    protected $detteService;

    public function __construct(DetteService $detteService)
    {
        $this->detteService = $detteService;
    }

    public function store(StoreDetteRequest $request)
    {
        return $this->detteService->create($request);
    }

    public function index(Request $request)
    {
        $statut = $request->query('statut');
        return $this->detteService->index($statut);
    }

    public function show($id)
    {
        return $this->detteService->show($id);
    }

    public function listArticles($id)
    {
        return $this->detteService->listArticles($id);
    }

    public function listPaiements($id)
    {
        return $this->detteService->listPaiements($id);
    }

    public function addPaiement(Request $request, $id)
    {
        return $this->detteService->addPaiement($request, $id);
    }
    
}

