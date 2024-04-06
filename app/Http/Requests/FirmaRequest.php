<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FirmaRequest extends FormRequest
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
      'mychecks' => 'required',
      'cuil' => 'required',
    ];
  }

  public function messages()
  {
    return [

      'mychecks.required' => 'Debe seleccionar al menos una foja para firmar',
      'cuil.required' => 'El servicio de firma digital requiere un CUIL',
    ];
  }
}
