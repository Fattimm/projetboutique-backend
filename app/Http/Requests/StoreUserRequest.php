<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Rules\CustumPasswordRule;

class StoreUserRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette demande.
     */
    public function authorize()
    {
        // Vous pouvez ajouter des règles d'autorisation ici, si nécessaire
        return true;
    }

    /**
     * Règles de validation qui s'appliquent à la requête.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'login' => 'required|string|unique:users,login|max:255',
            'password' => [
                'required',
                'string',
                'min:5',
                'regex:/[a-z]/', // Doit contenir des lettres minuscules
                'regex:/[A-Z]/', // Doit contenir des lettres majuscules
                'regex:/[0-9]/', // Doit contenir des chiffres
                'regex:/[@$!%*?&]/', // Doit contenir des caractères spéciaux
                'confirmed',
                new CustumPasswordRule(),
                
            ],
            'role' => 'required|in:ADMIN,BOUTIQUIER,CLIENT'
        ];
    }

    /**
     * Messages d'erreur personnalisés pour la validation.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'login.required' => 'Le login est obligatoire.',
            'login.unique' => 'Ce login est déjà utilisé.',
            'login.max' => 'Le login ne doit pas dépasser 255 caractères.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 5 caractères.',
            'password.regex' => 'Le mot de passe doit contenir au moins une lettre majuscule, une lettre minuscule, un chiffre, et un caractère spécial (@$!%*?&).',
            'role.required' => 'Le rôle est obligatoire.',
            'role.in' => 'Le rôle doit être soit ADMIN, soit BOUTIQUIER, soit CLIENT.',
        ];
    }

    /**
     * Gérer une tentative de validation échouée.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status' => 422,
            'data' => $validator->errors(),
            'message' => 'Erreur de validation des données fournies.',
        ], 422));
    }
}
