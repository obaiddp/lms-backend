<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            'name' => ['required', 'max:120'],
            'category_type' => ['nullable', 'integer', 'in:1,2'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'image' => 'mimes:png|file|dimensions:min_width=60,min_height=60,max_width=60,max_height=60',
            'cover_image' => 'mimes:png,jpg,jpeg|file|dimensions:min_width=5,min_height=5,max_width=1900,max_height=400'
        ];
    }
}
