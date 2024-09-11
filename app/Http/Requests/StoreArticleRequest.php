<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Obtient les règles de validation qui s'appliquent à la requête.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];

        if ($this->isMethod('post')) {
            if ($this->has('libelle') && !$this->has('prix') && !$this->has('qteStock')) {
                // Affichage d'un article par libellé
                $rules['libelle'] = 'required|string|max:255';
                // `prix` et `qteStock` ne sont pas requis
            } else {
                // Création d'un article
                $rules = [
                    'libelle' => 'required|string|max:255|unique:articles,libelle',
                    'prix' => 'required|numeric|min:0',
                    'qteStock' => 'required|numeric|min:0',
                ];
            }
        } elseif ($this->isMethod('patch') || $this->isMethod('put')) {
            $rules = [
                'qteStock' => 'required|numeric|min:0',
            ];
        }

        return $rules;
    }

    /**
     * Messages d'erreur personnalisés pour les règles de validation.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'libelle.required' => 'Le libellé est requis.',
            'libelle.string' => 'Le libellé doit être une chaîne de caractères.',
            'libelle.unique' => 'Le libellé doit être unique. Cet article existe déjà.',
            'prix.required' => 'Le prix est requis.',
            'prix.numeric' => 'Le prix doit être un nombre.',
            'qteStock.required' => 'La quantité en stock est requise.',
            'qteStock.numeric' => 'La quantité en stock doit être un nombre.',
            'qteStock.min' => 'La quantité en stock ne peut pas être négative.',
        ];
    }
}
