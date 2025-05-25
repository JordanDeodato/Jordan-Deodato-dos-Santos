<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CandidateRequest extends FormRequest
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
            'user_uuid' => ['sometimes', 'required', 'string', 'min:1', 'max:255'],
            'resume' => ['sometimes', 'string', 'min:1', 'max:255'],
            'education_id' => ['sometimes', 'required', 'integer', 'min:1'],
            'experience' => ['sometimes', 'string', 'min:1', 'max:1000'],
            'skills' => ['sometimes', 'string', 'min:1', 'max:1000'],
            'linkedin_profile' => ['sometimes', 'string', 'min:1', 'max:255'],
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
