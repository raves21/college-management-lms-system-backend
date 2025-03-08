<?php

namespace App\Http\Requests\Professor;

use Illuminate\Foundation\Http\FormRequest;

class CreateProfessor extends FormRequest
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
            'email' => ['required', 'email'],
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8'],
            'department_id' => ['required', 'integer', 'exists:departments,id']
        ];
    }
}
