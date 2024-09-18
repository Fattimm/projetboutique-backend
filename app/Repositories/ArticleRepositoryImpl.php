<?php

namespace App\Repositories;

use App\Models\Dette;
use App\Models\Article;
use App\Http\Requests\StoreArticleRequest;
use App\Repositories\Interfaces\ArticleRepository;

class ArticleRepositoryImpl implements ArticleRepository
{
    protected $article;

    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    public function create($request){
        return Dette::create($request->all());
    }
    
    public function update($id, $request){
        $dette = Dette::find($id);
        $dette->update($request->all());
        return $dette;
    }
    
    public function delete($id){
        Dette::destroy($id);
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
        $dette->paiements()->create($request->all());
    }

    public function find($id){
        return Dette::find($id);
    }

    public function findById($articleId)
    {
        return Dette::findById($articleId);
    }

    public function insert(array $articleData)
    {
        return Dette::insert($articleData);
    }

    public function getAvailableArticles()
    {
        return Article::where('qteStock', '>=', 1)->get();
    }

    // Méthode pour récupérer les articles avec qtéStock < 1
    public function getUnavailableArticles()
    {
        return Article::where('qteStock', '<', 1)->get();
    }

    // Méthode pour récupérer tous les articles
    public function index()
    {
        return Article::all();
    }

    public function findByLibelle($libelle){
        return Article::where('libelle', 'LIKE', '%'.$libelle.'%')->get();
    }


}