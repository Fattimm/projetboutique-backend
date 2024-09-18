<?php

namespace App\Repositories\Interfaces;

use App\Models\Article;

class ArticleRepository
{
    // Récupérer un article par son ID
    public function findById($articleId)
    {
        return Article::find($articleId);
    }

    // Insérer un article avec l'ID existant (utile lors de la restauration)
    public function insert(array $articleData)
    {
        return Article::insert($articleData); // Insert permet de garder l'ID d'origine
    }

    // Mettre à jour un article
    public function update($articleId, array $data)
    {
        $article = Article::find($articleId);
        if ($article) {
            $article->update($data);
        }
        return $article;
    }

    // Supprimer un article
    public function delete($articleId)
    {
        $article = Article::find($articleId);
        if ($article) {
            $article->delete();
        }
    }
}
