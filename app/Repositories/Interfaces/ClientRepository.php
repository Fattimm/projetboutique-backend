<?php

namespace App\Repositories\Interfaces;

use App\Http\Requests\StoreClientRequest;

interface ClientRepository
{
    public function all();
    public function index();
    public function find($id);
    public function create();
    public function insert();
    public function store(array $clientData);
    public function whereNull();
    public function whereNotNull();
    public function filterByAccount($request);
    public function filterByStatus($request);
    public function searchByTelephone($request);
    public function deleteAccount($userId);
    public function show($id);
    public function getClientWithUser($id);
    public function getClientDettes($id);

}

