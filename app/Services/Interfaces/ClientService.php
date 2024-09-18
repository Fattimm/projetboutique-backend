<?php

namespace App\Services\Interfaces;
use App\Http\Requests\StoreClientRequest;
use Illuminate\Http\Request;

interface ClientService
{
    public function store(StoreClientRequest $request);
    public function index();
    public function filterByAccount(Request $request);
    public function filterByStatus(Request $request);
    public function searchByTelephone(Request $request);
    public function deleteAccount($userId);
    public function show($id);
    public function getClientWithUser($id);
    public function getClientDettes($id);
    // public function insert();
    
    
}
