<?php

namespace App\Http\Requests;

class BookRequest extends FormRequest
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
            'author_id' => 'required|integer',
            'title' => 'required|string',
            'description' => 'required|string',
            'pages' => 'required|integer',
            'isbn' => 'required|string',
            'price' => 'required|integer'
        ];
    }
}

