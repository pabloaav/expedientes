<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DocumentoSectorActualRequest extends FormRequest
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
        return [
            'organismo' => [
                'required'
            ],
            'num_doc' => [
                'required'
            ],
            'año_doc' => [
                'nullable'
            ],
            'tipo_doc' => [
                'nullable', 'string'
            ]
        ];
    }

    public function messages()
    {
        return [
            '*.required' => "El campo :attribute es obligatorio",
            '*.string' => "El campo :attribute debe ser una cadena"
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'parametros' => $validator->errors(),
            'error' => 'validación formulario'
        ], 422));
    }
}
