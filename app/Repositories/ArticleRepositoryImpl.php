<?php

namespace App\Repositories;

use App\Models\Article;
use App\Repositories\Interfaces\ArticleRepository;

class ArticleRepositoryImpl implements ArticleRepository
{
    protected $article;

    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    public function index()
    {
        return $this->article->all();
    }


    public function store(array $data)
    {
        return $this->article->create($data);
    }

    public function find($id)
    {
        return $this->article->find($id);
    }

    public function getAvailableArticles()
    {
        return $this->article->where('qteStock', '>', 0)->get();
    }

    public function getUnavailableArticles()
    {
        return $this->article->where('qteStock', '=', 0)->get();
    }
    
    public function findByLibelle($libelle)
    {
        return $this->article->where('libelle', $libelle)->first();
    }

    public function create(){
        return new Article();
    }
}