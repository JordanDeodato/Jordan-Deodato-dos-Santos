<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class ApplicationRequest extends FormRequest
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
            'candidate_uuid' => ['sometimes', 'required', 'string', 'min:1'],
            'vacancy_uuid' => ['sometimes', 'required', 'string', 'min:1'],
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
        ];
    }

    /**
     * Check if the user is a candidate
     *
     * @param $validator
     *
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $userUuid = $this->input('candidate_uuid');

            $user = User::where('uuid', $userUuid)->first();

            if (!$user) {
                $validator->errors()->add('candidate_uuid', 'Candidato não encontrado.');
                return;
            }

            if ($user->user_type_id !== 2) {
                $validator->errors()->add('candidate_uuid', 'O usuário informado não é do tipo Candidato.');
            }
        });
    }
    
    /**
     * Stop the requisition and show the validation messages
     *
     * @param Validator $validator
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
