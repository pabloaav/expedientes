<?php

namespace App\Http\Requests;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;

class SectoresUsuarioRequest extends FormRequest
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
    
    public function rules()
    {
        // $todayDate = date('d/m/Y');
        $rules = [
            'usuario' => 'required|integer',/* campo extracto , numerico*/
        ];

        return $rules;
    }

    public function response(array $errors)
    {
        return new JsonResponse(['error' => $errors], 400);
    }
}