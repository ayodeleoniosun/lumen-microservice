<?php

namespace App\Http\Requests;

class CreateUserRequest extends FormRequest
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
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'gender' => 'required|string|in:male,female',
            'email' => 'required|email|unique:users',
            'password' => 'required|string'
        ];
    }
}

