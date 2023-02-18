<?php

namespace App\Http\Requests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Routing\ProvidesConvenienceMethods;

class FormRequest
{
    use ProvidesConvenienceMethods;

    public Request $request;

    /**
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function __construct(Request $request, array|null $customAttributes = null)
    {
        $customAttributes ??= [];
        $this->request = $request;

        if (!$this->authorize()) {
            throw new UnauthorizedException();
        }

        $this->validate($this->request, $this->rules(), $this->messages(), $customAttributes);
    }

    public function validated(): array
    {
        return $this->request->all();
    }

    public function get(string $key, $default = null)
    {
        return $this->request->get($key, $default);
    }

    protected function authorize(): bool
    {
        return true;
    }

    protected function rules(): array
    {
        return [];
    }

    protected function messages(): array
    {
        return [];
    }
}
