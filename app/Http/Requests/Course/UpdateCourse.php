<?php

namespace App\Http\Requests\Course;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourse extends FormRequest
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
            'name' => ['sometimes', 'string'],
            'code' => ['sometimes', 'string'],
            'department_ids' => ['sometimes', 'array', 'min:1'],
            'department_ids.*' => ['distinct', 'integer', 'exists:departments,id']
        ];
    }
}
