<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;

class ValidarDocumentoPersonasRequest extends FormRequest
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
            'num_doc' => 'required|integer',/* campo extracto , numerico*/
            'dni' => 'required|string',
            'nombre' => 'required|max:45',
            'apellido' => 'required|max:45',
            'sexo' => [
                'required',
                 Rule::in(['M','F']),
            ]
        ];

        return $rules;
    }

    public function response(array $errors)
    {
        return new JsonResponse(['error' => $errors], 400);
    }
}
