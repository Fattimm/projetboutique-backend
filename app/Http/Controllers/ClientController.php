<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Facades\ClientServiceFacade;
use App\Http\Requests\StoreClientRequest;

class ClientController extends Controller
{

    public function index()
    {
        $this->authorize('viewAny', Client::class);
        return ClientServiceFacade::index();
    }

    public function store(StoreClientRequest $request)
    {
        $this->authorize('store', Client::class);
        return ClientServiceFacade::store($request);
    }

    public function filterByAccount(Request $request)
    {
        $this->authorize('filterByAccount', Client::class);
        return ClientServiceFacade::filterByAccount($request);
    }

    public function filterByStatus(Request $request)
    {
        $this->authorize('filterByStatus', Client::class);
        return ClientServiceFacade::filterByStatus($request);
    }

    public function searchByTelephone(Request $request)
    {
        $this->authorize('searchByTelephone', Client::class);
        return ClientServiceFacade::searchByTelephone($request);
    }

    public function deleteAccount($userId)
    {
        $this->authorize('deleteAccount', Client::class);
        return ClientServiceFacade::deleteAccount($userId);
    }

    public function show($id)
    {
        $this->authorize('show', Client::class);
        return ClientServiceFacade::show($id);
    }

    public function getClientWithUser($id)
    {
        $this->authorize('getClientWithUser', Client::class);
        return ClientServiceFacade::getClientWithUser($id);
    }

    public function getClientDettes($id)
    {
        $this->authorize('getClientDettes', Client::class);
        return ClientServiceFacade::getClientDettes($id);
    }

}
