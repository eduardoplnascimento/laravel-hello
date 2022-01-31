<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
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
            'name' => 'required|min:5|max:255',
            'email' => 'sometimes|required',
            'phone' => 'sometimes|required',
            'id_number' => 'sometimes|required'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Um nome é obrigatório',
            'email.required' => 'E-mail não pode ser vazio',
            'phone.required' => 'Telefone não pode ser vazio',
            'id_number.required' => 'Identificação não pode ser vazia'
        ];
    }
}
