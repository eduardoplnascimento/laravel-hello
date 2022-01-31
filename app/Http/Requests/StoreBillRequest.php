<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBillRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return !str_contains($this->user()->name, 'Guest');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'invoice' => 'required',
            'installment' => 'required',
            'value' => 'required',
            'client_id' => 'required'
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
            'invoice.required' => 'Uma fatura é obrigatória',
            'installment.required' => 'Uma parcela é obrigatória',
            'value.required' => 'Um valor é obrigatório',
            'client_id.required' => 'Um cliente é obrigatório'
        ];
    }
}
