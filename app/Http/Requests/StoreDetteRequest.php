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
            'clientId' => 'required|exists:clients,id',
            'montant' => 'required|numeric|min:0',
            'articles' => 'required|array',
            'articles.*.articleId' => 'required|exists:articles,id',
            'articles.*.qteVente' => 'required|numeric|min:0',
            'articles.*.prixVente' => 'required|numeric|min:0',
            'paiement.montant' => 'nullable|numeric|min:0',
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
