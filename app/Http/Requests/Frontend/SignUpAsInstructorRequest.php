<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Foundation\Http\FormRequest;

class SignUpAsInstructorRequest extends FormRequest
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
        $rules = [
            'first_name' => ['required', 'string','min:2', 'max:100'],
            'last_name' => ['required', 'string','min:2', 'max:100'],
            'account_type' => ['required'],
            'professional_title' => ['required', 'string','min:2', 'max:100'],
            'address' => ['required', 'string'],
            'cv_file' => ['required', 'max:10000', 'mimetypes:application/pdf'],
            'about_me' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
        ];

        if(!is_null(request()->input('mobile_number')) || !is_null(request()->input('area_code'))){
            $rules['area_code'] = 'required';
            $rules['mobile_number'] = 'bail|numeric|unique:users';
        }

        return $rules;
    }
}
