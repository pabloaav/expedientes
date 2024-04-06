<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest;

class ValidateCrearFojaRequest extends FormRequest
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
      $rules =[
          'content' => 'required|string',
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
