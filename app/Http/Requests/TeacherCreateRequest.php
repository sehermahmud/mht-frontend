<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class TeacherCreateRequest extends Request
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
            'email' => 'required|email',
            'description' => 'required',
            'password' => 'required|min:6',
            'password_re' => 'required|min:6|same:password',
        ];
    }
}
