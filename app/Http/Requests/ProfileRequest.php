<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'profile_picture' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=4096,max_height=4096'],
            'remove_profile_picture' => ['sometimes', 'boolean'],
            'name' => ['bail', 'required', 'string', 'max:255', 'not_regex:/[\x00-\x1F\x7F]/u'],
            'email' => ['bail', 'required', 'string', 'max:255', 'email', Rule::unique('users', 'email')->ignore($this->user())],
            'username' => ['bail', 'required', 'string', 'max:255', 'regex:/\A[\pL\pM\pN_.-]+\z/u', Rule::unique('users', 'username')->ignore($this->user())],
        ];
    }

    public function messages(): array
    {
        return [
            'name.not_regex' => 'The name cannot contain line breaks or control characters.',
            'username.regex' => 'Use only letters, numbers, periods, underscores, or hyphens in the username. Spaces are not allowed.',
            'email.unique' => 'An account with this email address already exists.',
            'username.unique' => 'This username is already in use. Please choose another.',
        ];
    }
}
