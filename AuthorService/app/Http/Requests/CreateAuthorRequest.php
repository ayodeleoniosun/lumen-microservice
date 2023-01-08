<?php

namespace App\Http\Requests;

class CreateAuthorRequest extends FormRequest
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
            'user_id' => 'required|integer|unique:App\Models\Author,user_id',
            'bio' => 'required|string',
            'url' => 'required|string|url',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'user_id.unique' => 'This user already exist as an author',
        ];
    }
}

