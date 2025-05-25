<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class VacancyRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'min:3', 'max:255'],
            'description' => ['sometimes', 'required', 'string', 'min:3', 'max:500'],
            'vacancy_type_id' => ['sometimes', 'required', 'integer', 'min:1'],
            'opened' => ['sometimes', 'required', 'boolean'],
        ];
    }

    /**
     * Returns the validation messages 
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'required' => 'O campo :attribute é obrigatório.',
            'min' => 'O campo :attribute deve ter no mínimo :min caracteres.',
            'max' => 'O campo :attribute deve ter no máximo :max caracteres.',
            'email' => 'O campo :attribute deve conter um endereço de e-mail válido.',
            'digits' => 'O campo :attribute deve conter exatamente :digits dígitos.',
            'integer' => 'O campo :attribute deve ser um número inteiro.',
            'password.regex' => 'A senha deve conter pelo menos uma letra maiúscula, uma minúscula, um número e um caractere especial.'
        ];
    }

    /**
     * Stop the requisition and show the validation messages
     *
     * @param Validator $validator [explicite description]
     *
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Erro de validação',
            'errors' => $validator->errors()
        ], 422));
    }
}
