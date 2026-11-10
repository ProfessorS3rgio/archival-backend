<?php

namespace App\Http\Requests;

use App\Models\User;
use App\Rules\ValidPassword;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    public function rules(): array
    {
        $user = $this->isMethod('PUT') ? User::findOrFail($this->route('id')) : null;
        // Editing accounts must not grant administrator access or change an
        // existing administrator's role. Creation keeps its separate role choices.
        $allowedRoles = $user
            ? ($user->role === 'admin' ? ['admin'] : ['staff', 'legislator'])
            : ['admin', 'staff', 'legislator'];

        return [
            'name' => ['bail', 'required', 'string', 'max:255', 'not_regex:/[\x00-\x1F\x7F]/u'],
            'email' => ['bail', 'required', 'string', 'max:255', 'email', Rule::unique('users', 'email')->ignore($user)],
            'username' => ['bail', 'required', 'string', 'max:255', 'regex:/\A[\pL\pM\pN_.-]+\z/u', Rule::unique('users', 'username')->ignore($user)],
            'role' => ['bail', 'required', 'string', Rule::in($allowedRoles)],
            'password' => ['bail', $user ? 'nullable' : 'required', 'string', 'min:8', new ValidPassword, 'confirmed'],
            'password_confirmation' => ['bail', 'nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.not_regex' => 'The name cannot contain line breaks or control characters.',
            'username.regex' => 'Use only letters, numbers, periods, underscores, or hyphens in the username. Spaces are not allowed.',
            'email.unique' => 'An account with this email address already exists.',
            'username.unique' => 'This username is already in use. Please choose another.',
            'password.confirmed' => 'The password confirmation does not match.',
            'role.in' => $this->isMethod('PUT')
                ? 'Choose Staff or Legislator. Existing administrator roles cannot be changed.'
                : 'Choose Admin, Staff, or Legislator.',
        ];
    }
}
