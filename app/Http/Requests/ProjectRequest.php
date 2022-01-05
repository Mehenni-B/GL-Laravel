<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
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
            'name' => 'required|string|max:255|min:3',
            'tasks' => 'required|array',
            'tasks.*.name' => 'required|string|max:255',
            'tasks.*.duration' => 'required|integer|min:1',
            'tasks.*.previous' => 'array',
            'tasks.*.previous.*' => 'string|max:255',
        ];
    }
}
