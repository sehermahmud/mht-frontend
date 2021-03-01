<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class UserRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'fullname' => 'required|min:4',
            'uemail'   => 'required|email',
            'upassword' => 'required|min:6',
            'upassword_re' => 'required|min:6|same:upassword',
            'uroles' => 'required'
        ];
    }

}
