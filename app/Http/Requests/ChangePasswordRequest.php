<?php

namespace App\Http\Requests;

use App\Rules\ValidPassword;
use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['bail', 'required', 'string', 'max:4096', 'current_password:web'],
            'password' => ['bail', 'required', 'string', 'min:8', new ValidPassword, 'confirmed'],
            'password_confirmation' => ['bail', 'required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.current_password' => 'Current password is incorrect.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }
}
