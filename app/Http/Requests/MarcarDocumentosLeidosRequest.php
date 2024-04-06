<?php

namespace App\Http\Requests;

use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;

class MarcarDocumentosLeidosRequest extends FormRequest
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
            'organismo' => 'required|integer',
            'content' => 'required|array',
            'content.*.identificador' => 'required|integer',
            'content.*.num_doc' => 'required|string',
            // 'content.*' => 'required|integer:identificador,num_documento'
        ];

        return $rules;
    }

    public function response(array $errors)
    {
        return new JsonResponse(['error' => $errors], 400);
    }
}
