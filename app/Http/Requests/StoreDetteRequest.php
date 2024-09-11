<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDetteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'montant' => 'required|numeric|min:0',
            'clientId' => 'required|exists:clients,id',
            'articles' => 'required|array|min:1',
            'articles.*.articleId' => 'required|exists:articles,id',
            'articles.*.qteVente' => 'required|integer|min:1',
            'articles.*.prixVente' => 'required|numeric|min:0',
            'paiement.montant' => 'required|numeric|min:0',


        ];
    }

    public function messages()
    {
        return [
            'montant.required' => 'Le montant est requis.',
            'clientId.exists' => 'Le client n\'existe pas.',
            'articles.required' => 'Il doit y avoir au moins un article.',
            'articles.*.qteVente.min' => 'La quantité vendue doit être inférieure ou égale à la quantité en stock.',
        ];
    }
}
