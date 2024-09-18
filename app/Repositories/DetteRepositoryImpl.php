<?php

namespace App\Repositories;
use App\Models\Dette;
use App\Repositories\Interfaces\DetteRepository;

class DetteRepositoryImpl extends DetteRepository{
   
    public function create($request){
        return Dette::class::create($request);
    }

    public function update($id, $request){
        $dette = Dette::find($id);
        $dette->update($request);
        return $dette;
    }

    public function delete($id){
        Dette::destroy($id);
    }

    public function index($statut = null){
        if($statut){
            return Dette::where('statut', $statut)->get();
        }
        return Dette::all();
    }

    public function show($id){
        return Dette::find($id);
    }

    public function listArticles($id){
        return Dette::find($id)->articles;
    }

    public function listPaiements($id){
        return Dette::find($id)->paiements;
    }

    public function addPaiement($request, $id){
        $dette = Dette::find($id);
        $dette->paiements()->create($request);
    }   

    public function is_numeric($request){
        return is_numeric($request);
    }

    public function is_null($request){
        return is_null($request);
    }


}