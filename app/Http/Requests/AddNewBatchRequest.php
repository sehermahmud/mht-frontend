<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class AddNewBatchRequest extends Request
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
            'batch_number' => 'required|numeric',
            'price' => 'required|numeric',
            'batch_types_id' => 'required|not_in:default',
            'grades_id' => 'required|not_in:default',
            'subjects_id' => 'required|not_in:default',
            'schedule' => 'required',
            'start_date' => 'required|date_format:d/m/Y',
            'end_date' => 'required|date_format:d/m/Y',
            'expected_students' => 'required',
        ];
    }
}
