<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganismoUserRequest extends FormRequest
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
            'user' => 'required|email|unique:users',
            'password' => 'required|min:5|max:10|confirmed',
        ];
    }

    public function messages(){
        return [
            
            'name.required' => 'Campo obligatorio',
            'name.min' => 'El campo name  tiene un minimo de 5 caracteres',
            'name.max' => 'El campo name  tiene un maximo de 20 caracteres',         
            'email.required' => 'Campo email obligatorio',
            'email.email' => 'El campo no es de tipo correo electronico',
            'email.unique' => 'El correo electronico que intenta ingresar esta en uso',
            'password.required' => 'Campo password obligatorio',
            'password.min' => 'El campo password  tiene un minimo de 5 caracteres',
            'password.max' => 'El campo password  tiene un maximo de 10 caracteres',   
            
        ];
    }
}
