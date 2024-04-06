<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExpedienteFojaRequest extends FormRequest
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
            'tipo_expediente' => 'required',
            'expediente' => 'required',
            'sectorusers' => 'required',
            'fecha_inicio' => 'required',
            'expediente_num' => 'required|numeric|min:0',
            'pdfs' => 'required|mimetypes:application/pdf,application/octet-stream|max:51200',
        ];
    }

    public function messages()
    {
        return [
            '*.required' => 'El campo :attribute es obligatorio',
            '*.numeric' => 'El campo :attribute debe ser numérico',
            '*.min' => 'El campo :attribute debe ser mayor a :min',
            'pdfs.max' => 'El PDF no puede superar los 50 MB',
            'pdfs.mimetypes' => 'El archivo adjunto debe ser de tipo .pdf',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => 'false',
            'errors' => $validator->errors()
        ], 422));
    }
}
