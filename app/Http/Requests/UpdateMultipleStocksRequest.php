<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMultipleStocksRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'articles' => 'required|array',
            'articles.*.id' => 'required|exists:articles,id',
            'articles.*.qteStock' => 'required|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'articles.required' => 'La liste des articles est requise.',
            'articles.array' => 'La liste des articles doit être un tableau.',
            'articles.*.id.required' => 'L\'ID de l\'article est requis.',
            'articles.*.id.exists' => 'L\'article spécifié n\'existe pas.',
            'articles.*.qteStock.required' => 'La quantité en stock est requise pour chaque article.',
            'articles.*.qteStock.integer' => 'La quantité en stock doit être un nombre entier.',
            'articles.*.qteStock.min' => 'La quantité en stock ne peut pas être négative.',
        ];
    }
}