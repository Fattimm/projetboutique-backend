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



// <?php

// namespace App\Http\Requests;

// use App\Enums\RoleEnum;
// use App\Enums\StateEnum;
// use App\Enums\UserRole;
// use App\Rules\CustumPasswordRule;
// use App\Rules\PasswordRules;
// use Illuminate\Contracts\Validation\Validator;
// use Illuminate\Foundation\Http\FormRequest;
// use Illuminate\Http\Exceptions\HttpResponseException;

// class StoreUserRequest extends FormRequest
// {
//     /**
//      * Determine if the user is authorized to make this request.
//      */
//     public function authorize(): bool
//     {
//         return true;
//     }

//     /**
//      * Get the validation rules that apply to the request.
//      *
//      * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
//      */
//     public function Rules(): array
//     {
//         return [
//             'nom' => 'required|string|max:255',
//             'prenom' => 'required|string|max:255',
//             'login' => 'required|string|max:255|unique:users,login',
//             'role' => ['required', 'in:' . implode(',', array_column(RoleEnum::cases(), 'value'))],
//           //  'email' => 'required|email|unique:users,email',
//             'password' =>['confirmed', new CustumPasswordRule()],
//         ];
//     }

//     public function validationMessages(): array
//     {
//         return [
//             'nom.required' => 'Le nom est obligatoire.',
//             'prenom.required' => 'Le prénom est obligatoire.',
//             'role.required' => 'Le rôle est obligatoire.',
//             'role.in' => 'Le rôle doit être ADMIN ou BOUTIQUIER ou CLIENT',
//             'email.required' => "L'email est obligatoire.",
//             'email.email' => "L'email doit être une adresse email valide.",
//             'email.unique' => "Cet email est déjà utilisé.",
//             'login.required' => 'Le login est obligatoire.',
//             'login.unique' => "Cet login est déjà utilisé.",
//         ];
//     }

//     function failedValidation(Validator $validator)
//     {
//         throw new HttpResponseException($this->sendResponse($validator->errors(),StateEnum::ECHEC,404));
//     }
// }