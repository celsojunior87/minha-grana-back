<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name' => 'required',
            'role_id' => 'required',
            'email' => 'required',
            'cpf' => 'required',
            'sexo' => 'required',
            'telefone' => 'required',
            'celular' => 'required',
        ];
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes(){
        return  [
            'name' => 'Nome',
            'role_id' => 'Perfil',
            'email' => 'E-mail',
            'cpf' => 'CPF',
            'sexo' => 'Sexo',
            'celular' => 'Celular',
            'telefone' => 'Telefone'
        ];
    }
}
