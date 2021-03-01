<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class StudentCreateRequest extends Request
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
            'name' => 'required|min:3',
            'fathers_name' => 'required|min:3',
            'mothers_name' => 'required|min:3',
            'student_phone_number' => 'required',
            'guardian_phone_number' => 'required',
            'schools_id' => 'required|not_in:default',
            'batch_types_id' => 'required|not_in:default',
        ];
    }
}
            