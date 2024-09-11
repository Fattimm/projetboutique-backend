<?php
namespace App\Repositories\Interfaces;

use App\Http\Requests\StoreArticleRequest;
use Illuminate\Http\Request;
interface ArticleRepository
{
    public function index();
    public function create(array $data);
    public function find($id);
    public function getAvailableArticles();
    public function getUnavailableArticles();
    public function findByLibelle($libelle);
    

}