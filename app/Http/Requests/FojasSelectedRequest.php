<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FojasSelectedRequest extends FormRequest
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
            // 'notificar_personas' => [
            //     'required'
            // ],
            'mychecks' => [
                'required'
            ]
        ];
    }

    public function messages()
    {
        return [
            'mychecks.required' => 'Debe seleccionar al menos 1 foja para armar el PDF',
            // 'notificar_personas.required' => 'Debe seleccionar al menos 1 persona para compartir'
        ];
    }
}
