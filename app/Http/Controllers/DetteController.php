<?php

namespace App\Http\Controllers;

use App\Models\Dette;
use Illuminate\Http\Request;
use App\Http\Requests\StoreDetteRequest;
use App\Services\Interfaces\DetteService;
use App\Services\FirebaseArchivageService;

class DetteController extends Controller
{
    protected $detteService;

    public function __construct(DetteService $detteService)
    {
        $this->detteService = $detteService;
    }

    public function store(StoreDetteRequest $request)
    {
        $this->authorize('store', Dette::class);
        return $this->detteService->create($request);
    }

    public function index(Request $request)
    {
        $this->authorize('index', Dette::class);
        $statut = $request->query('statut');
        return $this->detteService->index($statut);
    }

    public function show($id)
    {
        $this->authorize('show', Dette::class);
        return $this->detteService->show($id);
    }

    public function listArticles($id)
    {
        $this->authorize('listArticles', Dette::class);
        return $this->detteService->listArticles($id);
    }

    public function listPaiements($id)
    {
        $this->authorize('listPaiements', Dette::class);
        return $this->detteService->listPaiements($id);
    }

    public function addPaiement(Request $request, $id)
    {
        $this->authorize('addPaiement', Dette::class);
        return $this->detteService->addPaiement($request, $id);
    }
    

}

