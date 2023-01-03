<?php

namespace App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Laravel\Lumen\Routing\ProvidesConvenienceMethods;

class FormRequest
{
    use ProvidesConvenienceMethods;

    public Request $request;

    public function __construct(Request $request, array $messages = [], array $customAttributes = [])
    {
        $this->request = $request;

        if (!$this->authorize()) throw new UnauthorizedException;

        $this->validate($this->request, $this->rules(), $messages, $customAttributes);
    }

    public function validated()
    {
        return $this->request->all();
    }

    public function get(string $key, $default = null)
    {
        return $this->request->get($key, $default);
    }

    protected function authorize()
    {
        return true;
    }

    protected function rules()
    {
        return [];
    }
}
