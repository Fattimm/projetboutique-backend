<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use Illuminate\Http\Request;
use App\Facades\ClientServiceFacade;

class ClientController extends Controller
{

    public function index(){
        return ClientServiceFacade::index();
    }

    public function store(StoreClientRequest $request){
        return ClientServiceFacade::store($request);
    }

    public function filterByAccount(Request $request){
        return ClientServiceFacade::filterByAccount($request);
    }

    public function filterByStatus(Request $request)
    {
        return ClientServiceFacade::filterByStatus($request);
    }

    public function searchByTelephone(Request $request)
    {
        return ClientServiceFacade::searchByTelephone($request);
    }

    public function deleteAccount($userId)
    {
        return ClientServiceFacade::deleteAccount($userId);
    }

    public function show($id)
    {
        return ClientServiceFacade::show($id);
    }

    public function getClientWithUser($id)
    {
        return ClientServiceFacade::getClientWithUser($id);
    }

    public function getClientDettes($id)
    {
        return ClientServiceFacade::getClientDettes($id);
    }

}
