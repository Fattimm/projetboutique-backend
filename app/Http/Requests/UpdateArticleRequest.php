<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
{
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
   

    public function rules()
    {
        return [
            'articles.*.id' => 'required_with:articles|exists:articles,id',
            'articles.*.qteStock' => 'required_with:articles.*.id|integer|min:0',

            'article.id' => 'required_with:article|exists:articles,id',
            'qteStock' => 'required|integer|min:0'
        ];
    }
}
