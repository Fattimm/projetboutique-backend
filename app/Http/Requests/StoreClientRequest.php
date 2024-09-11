<?php

namespace App\Http\Requests;

use App\Models\Role;
use App\Enums\StateEnum;
use App\Rules\CustumPasswordRule;
use App\Rules\TelephoneRule;
use App\Traits\RestResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreClientRequest extends FormRequest
{
    use RestResponseTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $roles = Role::pluck('name')->toArray();

        return [
            // Validation pour le client
            'surname' => ['required', 'string', 'max:255', 'unique:clients,surname'],
            'adresse' => ['required', 'string', 'max:255'],
            'telephone' => ['required', new TelephoneRule()],

            // Validation pour l'utilisateur (optionnel)
            'user' => ['sometimes', 'array'],
            'user.nom' => ['required_with:user', 'string', 'max:255'],
            'user.prenom' => ['required_with:user', 'string', 'max:255'],
            'user.login' => ['required_with:user', 'string', 'max:255', 'unique:users,login'],
            'user.email' => ['required_with:user', 'email', 'unique:users,email', 'max:255'], // Ajout de l'email
            'user.photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // Ajout de la photo
            'user.role' => ['required_with:user', 'string', 'in:CLIENT,BOUTIQUIER,ADMIN'],
            'user.password' => ['required_with:user', new CustumPasswordRule(), 'confirmed'],
        ];
    }

    /**
     * Messages d'erreurs personnalisés.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Messages pour le client
            'surname.required' => 'Le surnom est obligatoire.',
            'surname.unique' => 'Ce surnom est déjà utilisé.',
            'adresse.required' => 'L\'adresse est obligatoire.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',

            // Messages pour l'utilisateur (optionnel)
            'user.nom.required_with' => 'Le nom de l\'utilisateur est obligatoire lorsque le compte est créé.',
            'user.prenom.required_with' => 'Le prénom de l\'utilisateur est obligatoire lorsque le compte est créé.',
            'user.login.required_with' => 'Le login de l\'utilisateur est obligatoire.',
            'user.login.unique' => 'Ce login est déjà utilisé.',
            'user.email.required_with' => 'L\'email est obligatoire lorsque le compte utilisateur est créé.',
            'user.email.email' => 'L\'email doit être une adresse valide.',
            'user.email.unique' => 'Cet email est déjà utilisé.',
            'user.photo.image' => 'La photo doit être une image.',
            'user.photo.mimes' => 'La photo doit être au format jpeg, png, jpg ou gif.',
            'user.photo.max' => 'La taille de la photo ne doit pas dépasser 2 Mo.',
            'user.role.required_with' => 'Le rôle est obligatoire lorsque le compte utilisateur est créé.',
            'user.role.in' => 'Le rôle sélectionné est invalide.',
            'user.password.required_with' => 'Le mot de passe est obligatoire lorsque le compte utilisateur est créé.',
            'user.password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ];
    }

    /**
     * Gestion des erreurs de validation.
     *
     * @param Validator $validator
     */
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->sendResponse(
            $validator->errors(),
            StateEnum::ECHEC,
            404
        ));
    }
}