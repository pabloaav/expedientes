<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
class ValidateCrearDocumentoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'usuario' => 'required|integer',/* campo extracto , numerico*/
            'sector' => 'required|integer',
            'importancia' => [
                'required',
                 Rule::in(['Urgente','Alta','Media','Baja']),
            ],
            'extracto' => 'required|max:150', /* campo extracto , string*/
            'tipo_documento' => 'required|integer',
            'num_documento' => 'integer|nullable',
            'fecha_inicio'  => 'date|nullable',
            'organismo' => 'required|integer',
        ];

        return $rules;
    }


    public function response(array $errors)
    {
        return new JsonResponse(['error' => $errors], 400);
    }
    
}
