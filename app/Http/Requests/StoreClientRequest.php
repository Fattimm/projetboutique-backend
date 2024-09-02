<?php

namespace App\Http\Requests;

// use App\Enums\RoleEnum;
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
            'surname' => ['required', 'string', 'max:255', 'unique:clients,surname'],
            'address' => ['string', 'max:255'],
            'telephone' => ['required', new TelephoneRule()],

            'user' => ['sometimes', 'array'],
            'user.nom' => ['required_with:user', 'string'],
            'user.prenom' => ['required_with:user', 'string'],
            'user.login' => ['required_with:user', 'string', 'unique:users,login'],
            // 'user.role' => ['required_with:user', 'string', 'in:' . implode(',', $roles)],
            'user.role' => ['required_with:user', 'string', 'in:CLIENT,BOUTIQUIER,ADMIN'],
            'user.password' => ['required_with:user', new CustumPasswordRule(), 'confirmed'],
        ];
    }


    function messages()
    {
        return [
            'surname.required' => "Le surnom est obligatoire.",
        ];
    }

    function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->sendResponse($validator->errors(),StateEnum::ECHEC,404));
    }


}

