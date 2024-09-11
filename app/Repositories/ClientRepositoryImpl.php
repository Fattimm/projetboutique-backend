<?php

namespace App\Repositories;

use App\Http\Requests\StoreClientRequest;
use App\Models\Client;
use App\Repositories\Interfaces\ClientRepository;

class ClientRepositoryImpl implements ClientRepository
{
    public function index(){
        return Client::all();
    }
    
    public function all(){
        return Client::all();
    }

    public function find($id){
        return Client::find($id);
    }

    public function create(){
        return new Client();
    }

    public function store(array $clientData)
    {
        return Client::create($clientData);
    }

    public function filterByAccount($request){
        return Client::where('account_number', $request->account_number)->get();
    }

    public function filterByStatus($request){
        return Client::where('status', $request->status)->get();
    }

    public function searchByTelephone($request){
        return Client::where('telephone', $request->telephone)->first();
    }

    public function deleteAccount($userId){
        Client::destroy($userId);
    }

    public function show($id){
        return Client::find($id);
    }

    public function getClientWithUser($id){
        return Client::with('user')->find($id);
    }

    public function with(){
        return Client::with('dettes')->get();
    }

    public function getClientDettes($id){
        return Client::find($id)->dettes;
    }


}
