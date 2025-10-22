<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $emailRules = ['required', 'email', 'max:255', Rule::unique('users', 'email')];
        $nameRules = ['required', 'string', 'max:255'];
        $passWordRules = ['required', 'string', 'max:255', Password::defaults()];
        if($this->has('users')) {
            return [
                'users' => ['required', 'array'],
                'users.*.name' => $nameRules,
                'users.*.email' => $emailRules,
                'users.*.password' => $passWordRules,
            ];

        }
        return [
            'name' => $nameRules,
            'email' => $emailRules,
            'password' => $passWordRules,
        ];
    }
}
