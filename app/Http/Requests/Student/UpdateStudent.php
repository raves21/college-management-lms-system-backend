<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudent extends FormRequest
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
            'email' => ['sometimes', 'email'],
            'first_name' => ['sometimes', 'string'],
            'last_name' => ['sometimes', 'string'],
            'password' => ['sometimes', 'string', 'min:8'],
            'department_id' => ['sometimes', 'integer', 'exists:departments,id']
        ];
    }
}
