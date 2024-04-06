<?php

namespace App\Http\Requests;

use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;

class DocumentosOrganimosRequest extends FormRequest
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
          // $todayDate = date('d/m/Y');
          $rules = [
            'organismo' => 'required|integer',/* campo extracto , numerico*/
            'fecha_desde' => 'required|date',/* campo extracto , numerico*/
            'fecha_hasta' => 'required|date',/* campo extracto , numerico*/
            'filtro' => 'nullable|string'
        ];

        return $rules;
    }

    public function response(array $errors)
    {
        return new JsonResponse(['error' => $errors], 400);
    }
}
