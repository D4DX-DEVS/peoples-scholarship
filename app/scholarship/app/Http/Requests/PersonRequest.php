<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Input;

class PersonRequest extends FormRequest
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
        if(Input::get('course') == '-10')
            $course = 'required';
        else
            $course = '';


        return [
            'gender' => 'required',
            'age' => 'required',
            'dob' => 'required|date',
            'address' => 'required',
            'pin' => 'required',
            'district' => 'required',
            'area' => 'required',
            'unit' => 'required',
            'category' => 'required',
            'course' => 'required',
            'course_other' => $course,
            'institution' => 'required',
        ];
    }

    public function messages()
    {

        return [
            'gender.required' => 'Gender is required!',
            'age.required' => 'Age is required!',
            'dob.required' => 'Date of birth is required!',
            'address.required' => 'Address is required!',
            'pin.required' => 'PIN is required!',
            'district.required' => 'District is required!',
            'area.required' => 'Area is required!',
            'unit.required' => 'Unit is required!',
            'category.required' => 'Category is required!',
            'course.required' => 'Course is required!',
            'course_other.required' => 'Other Course is required!',
            'institution.required' => 'Institution is required!',
        ];

    }
}
