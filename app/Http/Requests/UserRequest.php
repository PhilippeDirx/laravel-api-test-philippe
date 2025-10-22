<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
use voku\helper\ASCII;

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
        $passWordRules = ['required', 'string', 'max:255', Password::min(8)->mixedCase()->numbers()->symbols()];
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

    /**
     * Saves the form. If multiple users, will save all of them and return them.
     * @return User|array
     */
    public function save()
    {
        if ( ! $this->has('users'))
            return $this->saveUser($this->all());

        $users = [];
        foreach($this->get('users') as $input) {
            $users[] = $this->saveUser($input);
        }

        return $users;
    }

    /**
     * Save a single user.
     * @param array $input
     * @return User
     */
    private function saveUser($input)
    {
        $user = new user();
        $user->name = $input['name'];
        $user->email = $input['email'];
        $user->password = Hash::make($input['password']);
        $user->save();
        return $user;
    }
}
