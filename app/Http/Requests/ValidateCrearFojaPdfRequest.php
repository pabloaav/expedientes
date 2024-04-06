<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

class ValidateCrearFojaPdfRequest extends FormRequest
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
        $rules =[
            'content' => 'required|mimes:pdf',
            'num_documento' => 'required|integer',
            'año'  => 'required|integer',
            'organismo' => 'required|integer',
          ];
  
          return $rules;
    }
    public function response(array $errors)
    {
        return new JsonResponse(['error' => $errors], 400);
    }
}
